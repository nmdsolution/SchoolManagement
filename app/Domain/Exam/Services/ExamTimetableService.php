<?php

namespace App\Domain\Exam\Services;

use App\Domain\Exam\Repositories\ExamClassSectionRepository;
use App\Domain\Exam\Repositories\ExamMarksRepository;
use App\Domain\Exam\Repositories\ExamRepository;
use App\Domain\Exam\Repositories\ExamResultRepository;
use App\Domain\Exam\Repositories\ExamStatisticsRepository;
use App\Domain\Exam\Repositories\ExamTimetableRepository;
use App\Domain\Student\Repositories\StudentsRepository;
use App\Models\Exam;
use App\Models\ExamClassSection;
use App\Models\ExamTimetable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ExamTimetableService
{

    public function __construct(
        private readonly ExamRepository             $examRepository,
        private readonly ExamClassSectionRepository $examClassSectionRepository,
        private readonly ExamTimetableRepository    $examTimetableRepository,
        private readonly ExamMarksRepository        $examMarksRepository,
        private readonly ExamResultRepository       $examResultRepository,
        private readonly ExamStatisticsRepository   $examStatisticsRepository,
        private readonly StudentsRepository $studentRepository
    )
    {}

    public function publishResult(int $examClassSectionId): array
    {
        try {
            DB::beginTransaction();

            $examClassSection = $this->examClassSectionRepository->getById($examClassSectionId);

            if (!$this->areAllMarksSubmitted($examClassSection->exam_id)) {
                throw new \Exception(trans('marks_are_not_submitted'));
            }

            $exam = $this->examRepository->getWithRelations($examClassSectionId);
            $examStatus = $this->calculateExamStatus($exam);

            if (!$this->canPublishResult($examStatus, $exam)) {
                throw new \Exception(trans('exam_not_completed_yet'));
            }

            $this->processExamResults($examClassSection, $exam);

            if ($examClassSection->publish) {
                $this->updateExamStatistics($examClassSection);
            }

            DB::commit();
            return [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    private function areAllMarksSubmitted(int $examId): bool
    {
        $timetables = $this->examTimetableRepository->getByExamId($examId);
        return !$timetables->contains(fn($timetable) => $timetable->exam_marks->isEmpty());
    }

    private function calculateExamStatus(Exam $exam): string
    {
        $examDates = $this->examTimetableRepository->getExamDates(
            $exam->exam_class_section->first()->exam_id,
            $exam->exam_class_section->first()->id
        );

        $currentDate = Carbon::now()->toDateString();

        if ($currentDate > $examDates->start_date && $currentDate < $examDates->end_date) {
            return "1"; // On Going
        }
        return $currentDate < $examDates->start_date ? "0" : "2"; // Upcoming : Completed
    }

    private function canPublishResult(string $examStatus, Exam $exam): bool
    {
        return $examStatus == "2"
            && $exam->timetable->isNotEmpty()
            && $exam->marks->isNotEmpty();
    }

    private function processExamResults(ExamClassSection $examClassSection, Exam $exam): void
    {
        if ($examClassSection->publish) {
            $this->unpublishResults($examClassSection);
        } else {
            $this->publishResults($examClassSection, $exam);
        }
    }

    private function unpublishResults(ExamClassSection $examClassSection): void
    {
        $this->examResultRepository->deleteByExamId($examClassSection->exam_id);
        $this->examClassSectionRepository->updatePublishStatus($examClassSection, false);
    }

    private function publishResults(ExamClassSection $examClassSection, Exam $exam): void
    {
        foreach ($exam->marks as $examMark) {
            $resultData = $this->calculateExamResult($examMark, $exam);
            $this->examResultRepository->updateOrCreate(
                $this->getResultIdentifiers($examMark, $exam),
                $resultData
            );
        }
        $this->examClassSectionRepository->updatePublishStatus($examClassSection, true);
    }

    private function calculateExamResult($examMark, Exam $exam): array
    {
        $percentage = ($examMark->total_obtained_marks * 100) / $exam->timetable[0]->total_marks;
        $grade = findExamGrade($percentage);

        if (!$grade) {
            throw new \Exception(trans('grades_data_does_not_exists'));
        }

        return [
            'total_marks' => $exam->timetable[0]->total_marks,
            'obtained_marks' => $examMark->total_obtained_marks,
            'percentage' => round($percentage, 2),
            'grade' => $grade
        ];
    }

    private function getResultIdentifiers($examMark, Exam $exam): array
    {
        return [
            'exam_id' => $exam->id,
            'class_section_id' => $examMark->student->class_section_id,
            'student_id' => $examMark->student_id,
            'session_year_id' => $exam->session_year_id
        ];
    }

    private function updateExamStatistics(ExamClassSection $examClassSection): void
    {
        $sessionYear = getSettings('session_year');
        $sessionYearId = $sessionYear['session_year'];

        $totalStudents = $this->studentRepository->getTotalStudents(
            $sessionYearId,
            $examClassSection->class_section_id
        );

        $examTimetableIds = $this->examTimetableRepository->getTimetableIds(
            $examClassSection->exam_id,
            $examClassSection->class_section_id,
            $sessionYearId
        );

        $totalAttemptStudents = $this->examMarksRepository->getTotalAttemptStudents(
            $examTimetableIds,
            $sessionYearId
        );

        $totalFailStudents = $this->examMarksRepository->getTotalFailStudents(
            $examTimetableIds,
            $sessionYearId
        );

        $this->examStatisticsRepository->updateOrCreate(
            [
                'exam_id' => $examClassSection->exam_id,
                'class_section_id' => $examClassSection->class_section_id,
                'session_year_id' => $sessionYearId,
            ],
            [
                'total_student' => $totalStudents,
                'total_attempt_student' => $totalAttemptStudents,
                'pass' => $totalAttemptStudents - $totalFailStudents,
            ]
        );
    }

    public function storeTimetable(array $data) {
        try {
            $session_year_id = Exam::with('session_year')->where('id', $data['exam_id'])->pluck('session_year_id')->first();

            foreach ($data['timetable'] as $timetable) {
                $exam_timetable_exists = ExamTimetable::checkIfSlotAvailable($data['class_section_id'], $timetable['date'], $timetable['start_time'], $timetable['end_time'])->count();
                if ($exam_timetable_exists) {
                    return [
                        'error'   => true,
                        'message' => "Other Exam already exists between " . $timetable['start_time'] . " - " . $timetable['end_time']
                    ];
                }
                $exam_timetable[] = array(
                    'exam_id'          => $data['exam_id'],
                    'class_section_id' => $data['class_section_id'],
                    'subject_id'       => $timetable['subject_id'],
                    'total_marks'      => 20,
                    'passing_marks'    => 10,
                    'start_time'       => $timetable['start_time'],
                    'end_time'         => $timetable['end_time'],
                    'date'             => date('Y-m-d', strtotime($timetable['date'])),
                    'session_year_id'  => $session_year_id
                );
            }
            ExamTimetable::insert($exam_timetable);
            return [
                'error'   => false,
                'message' => trans('data_store_successfully'),
            ];
        } catch (Throwable $e) {
            return [
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            ];
        }
    }

    public function deleteTimetable($id) {
        try {
            $exam_timetable = ExamTimetable::find($id);
            $exam_timetable->delete();
            return [
                'error'   => false,
                'message' => trans('data_delete_successfully'),
                'status'  => 200
            ];
        } catch (Throwable $e) {
            return [
                'error'   => true,
                'message' => trans('error_occurred')
            ];
        }
    }

    public function updateTimetable(array $data) {
        try {
            if ($data['edit_timetable'] != null && count($data['edit_timetable']) > 0) {
                foreach ($data['edit_timetable'] as $timetable) {
                    if (isset($timetable['timetable_id']) && $timetable['timetable_id'] != null) {
                        $exam_timetable_exists = ExamTimetable::checkIfSlotAvailable($data['class_section_id'], $timetable['date'], $timetable['start_time'], $timetable['end_time'], $timetable['timetable_id'])->count();
                        if ($exam_timetable_exists) {
                            return [
                                'error'   => true,
                                'message' => "Other Exam already exists between " . $timetable['start_time'] . " - " . $timetable['end_time']
                            ];
                        }
                        $timetable_db = ExamTimetable::find($timetable['timetable_id']);
                        $timetable_db->subject_id = $timetable['subject_id'];
                        $timetable_db->total_marks = $timetable['total_marks'];
                        $timetable_db->passing_marks = $timetable['passing_marks'];
                        $timetable_db->start_time = $timetable['start_time'];
                        $timetable_db->end_time = $timetable['end_time'];
                        $date = date('Y-m-d', strtotime($timetable['date']));
                        $timetable_db->date = $date;
                        $timetable_db->save();
                        $response = [
                            'error'   => false,
                            'message' => trans('data_update_successfully'),
                            'status'  => 200
                        ];
                    } else {
                        $exam_timetable_exists = ExamTimetable::checkIfSlotAvailable($data['class_section_id'], $timetable['date'], $timetable['start_time'], $timetable['end_time'])->count();
                        if ($exam_timetable_exists) {
                            return [
                                'error'   => true,
                                'message' => "Other Exam already exists between " . $timetable['start_time'] . " - " . $timetable['end_time']
                            ];
                        }
                        $date = date('Y-m-d', strtotime($timetable['date']));
                        $insert_data[] = array(
                            'exam_id'          => $data['exam_id'],
                            'class_section_id' => $data['class_section_id'],
                            'subject_id'       => $timetable['subject_id'],
                            'total_marks'      => 20,
                            'passing_marks'    => 10,
                            'start_time'       => $timetable['start_time'],
                            'end_time'         => $timetable['end_time'],
                            'session_year_id'  => $data['session_year_id'],
                            'date'             => $date,
                        );
                    }
                }
            }
            if (isset($insert_data)) {
                ExamTimetable::insert($insert_data);
                return [
                    'error'   => false,
                    'message' => trans('data_store_successfully'),
                    'status'  => 200
                ];
            }
        } catch (Throwable $e) {
            return [
                'error'   => true,
                'message' => trans('error_occurred')
            ];
        }
    }

    public function updateTotalMarks(array $data) {
        try {
            $timetable_db = ExamTimetable::find($data['exam_timetable_id']);
            $timetable_db->total_marks = $data['total_marks'];
            $timetable_db->passing_marks = $data['passing_marks'];
            $timetable_db->save();
            return [
                'error'   => false,
                'message' => trans('data_update_successfully'),
                'status'  => 200
            ];
        } catch (Throwable $e) {
            return [
                'error'   => true,
                'message' => trans('error_occurred')
            ];
        }
    }

    public function getTimetables(array $data) {
        $offset = $data['offset'] ?? 0;
        $limit = $data['limit'] ?? 10;
        $sort = $data['sort'] ?? 'id';
        $order = $data['order'] ?? 'ASC';

        $sql = ExamClassSection::owner()->with(['exam.session_year:id,name', 'class_timetable.subject', 'class_section.class'])
            ->whereHas('class_section.class', function ($q) {
                $q->activeMediumOnly();
            })->whereHas('exam', function ($q) {
                $q->where('type', 2);
            });

        if (!empty($data['search'])) {
            $search = $data['search'];

            $sql->where(function ($q) use ($search) {
                $q->whereHas('class_timetable', function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%$search%")
                        ->orWhere('total_marks', 'LIKE', "%$search%")
                        ->orWhere('passing_marks', 'LIKE', "%$search%")
                        ->orWhere('start_time', 'LIKE', "%$search%")
                        ->orWhere('end_time', 'LIKE', "%$search%")
                        ->orWhere('date', 'LIKE', "%$search%")
                        ->orWhere('created_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                        ->orWhere('updated_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%");
                })->orWhereHas('exam', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhereHas('session_year', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%$search%");
                        });
                })->orWhereHas('class_section.class', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })->orWhereHas('class_timetable.subject', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                });
            });
        }
        if (!empty($data['exam_id'])) {
            $sql->where('exam_id', $data['exam_id']);
        }
        if (!empty($data['class_section_id'])) {
            $sql->where('class_section_id', $data['class_section_id']);
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        foreach ($res as $row) {
            $class_subjects = ClassSubject::with('subject')->where('class_id', $row->class_section->class->id)->get();
            $operate = '<a href="#" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['exam_name'] = $row->exam->name;
            $tempRow['class_name'] = $row->class_section->full_name;
            $tempRow['test'] = $row->class_section;
            $tempRow['exam_id'] = $row->exam_id;
            $tempRow['class_section_id'] = $row->class_section_id;
            $tempRow['subjects'] = null;
            foreach ($class_subjects as $subjects) {
                $tempRow['subjects'][] = array(
                    'id'   => $subjects->subject->id,
                    'name' => $subjects->subject->name,
                    'type' => $subjects->subject->type
                );
            }
            $tempRow['timetable'] = $row->class_timetable;
            $tempRow['session_year_id'] = $row->exam->session_year->id;
            $tempRow['session_year'] = $row->exam->session_year->name;
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;

            if ($row->publish == 0) {
                $tempRow['operate'] = $operate;
            } else {
                $tempRow['operate'] = '';
            }

            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return $bulkData;
    }
}
