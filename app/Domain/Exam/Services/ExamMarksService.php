<?php

namespace App\Domain\Exam\Services;

use App\Domain\Competency\Repositories\SubjectCompetencyRepository;
use App\Domain\Exam\Repositories\ExamMarksRepository;
use App\Domain\Exam\Repositories\ExamResultRepository;
use App\Domain\Exam\Repositories\ExamTimetableRepository;
use App\Domain\Student\Repositories\StudentsRepository;
use App\Events\MarksUploadedEvent;
use App\Exceptions\ExamNotCompletedException;
use App\Exceptions\GradeNotFoundException;
use App\Models\ExamTimetable;
use App\Models\Students;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ExamMarksService
{
    public function __construct(
        private ExamTimetableRepository $examTimetableRepository,
        private StudentsRepository $studentsRepository,
        private SubjectCompetencyRepository $subjectCompetencyRepository,
        private ExamMarksRepository $marksRepository,
        private ExamResultRepository $resultRepository
    )
    {}

    public function getMarksList(array $params): array
    {
        $classSectionId = $params['class_section_id'];
        $examId = $params['exam_id'];
        $subjectId = $params['subject_id'];
        
        $timetable = $this->examTimetableRepository->findByExamAndSection(
            $examId,
            $classSectionId,
            $subjectId
        );


        if (!$this->isExamCompleted($examId, $classSectionId)) {
            throw new ExamNotCompletedException(trans('exam_not_completed_yet'));
        }

        $this->ensureDefaultMarks($timetable);

        $students = $this->studentsRepository->getStudentListForMarks(
            $classSectionId,
            $timetable->id,
            $subjectId,
            $params
        )->get();

        // dd($students->toArray());

        $competency = $this->subjectCompetencyRepository->findByExamAndSubject(
            $examId,
            $subjectId
        );

        return [
            'total' => $students->count(),
            'rows' => $this->formatStudentsData($students, $timetable),
            'subject_competency' => $competency,
            'marks_upload_status' => $timetable->marks_upload_status,
            'total_marks' => $timetable->total_marks,
            'passing_marks' => $timetable->passing_marks,
            'timetable_id' => $timetable->id
        ];
    }

    private function isExamCompleted(int $examId, int $classSectionId): bool
    {
        $dates = $this->examTimetableRepository->getExamDates($examId, $classSectionId);
        $currentDate = Carbon::now()->toDateString();

        if ($currentDate > $dates['start_date'] && $currentDate < $dates['end_date']) {
            return false; // On Going
        } elseif ($currentDate < $dates['start_date']) {
            return false; // Upcoming
        }

        return true; // Completed
    }

    private function ensureDefaultMarks(ExamTimetable $timetable): void
    {
        if ($timetable->total_marks == 0) {
            $this->examTimetableRepository->update([
                'total_marks' => 20,
                'passing_marks' => 10
            ], $timetable->id);
        }
    }

    private function formatStudentsData(Collection $students, ExamTimetable $timetable): array
    {
        $rows = [];
        $no = 1;

        foreach ($students as $student) {
            $rows[] = [
                'id' => $student->id,
                'no' => $no++,
                'student_name' => $student->user->first_name . ' ' . $student->user->last_name,
                'student_id' => $student->id,
                'total_marks' => $timetable->total_marks,
                'exam_marks_id' => $student->exam_marks->first()?->id ?? '',
                'obtained_marks' => $this->formatObtainedMarks($student->exam_marks->first()?->obtained_marks),
                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at,
                'operate' => $this->generateOperateButtons($student)
            ];
        }

        return $rows;
    }

    private function formatObtainedMarks(?float $marks): string
    {
        if ($marks === null) return '';
        return $marks < 0 ? '/' : (string) $marks;
    }

    private function generateOperateButtons(Students $student): string
    {
        $editButton = sprintf(
            '<a href="%s" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" 
                data-id="%d" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal">
                <i class="fa fa-edit"></i>
            </a>',
            route('exams.edit', $student->id),
            $student->id
        );

        $deleteButton = sprintf(
            '<a href="%s" class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" 
                data-id="%d">
                <i class="fa fa-trash"></i>
            </a>',
            route('exams.destroy', $student->id),
            $student->id
        );

        return $editButton . '&nbsp;&nbsp;' . $deleteButton;
    }

    public function submitMarks(array $data): void
    {
        DB::transaction(function () use ($data) {
            $timetable = $this->examTimetableRepository->findByExamAndSection(
                $data['exam_id'],
                $data['class_section_id'],
                $data['subject_id']
            );

            $this->handleSubjectCompetency($data);
            
            $examResults = [];
            foreach ($data['exam_marks'] as $examMark) {
                if ($examMark['obtained_marks'] === '/') {
                    $this->handleAbsentStudent($examMark, $timetable);
                    continue;
                }

                $status = $this->calculatePassingStatus($examMark, $timetable);
                $percentage = $this->calculatePercentage($examMark);
                $grade = $this->findGrade($percentage);

                $this->marksRepository->updateOrCreateMarks(
                    $timetable,
                    $examMark,
                    $status,
                    $grade,
                    $data['subject_id']
                );

                if ($this->shouldPublishResults($data, $timetable)) {
                    $examResults[] = $this->prepareExamResult(
                        $data,
                        $examMark,
                        $percentage,
                        $grade,
                        $timetable
                    );
                }
            }

            $this->examTimetableRepository->updateTimetableStatus($timetable->id, $data['marks_upload_status']);
            
            if (!empty($examResults)) {
                $this->resultRepository->upsertResults($examResults);
            }

            event(new MarksUploadedEvent(request()->merge($data)));
        });
    }

    private function handleSubjectCompetency(array $data): void
    {
        if (!empty($data['subject_competency'])) {
            $this->subjectCompetencyRepository->updateOrCreateCompetency([
                'exam_sequence_id' => $data['sequence_id'],
                'exam_id' => $data['exam_id'],
                'subject_id' => $data['subject_id'],
                'class_section_id' => $data['class_section_id'],
                'competence' => $data['subject_competency']
            ]);
        }
    }

    private function handleAbsentStudent(array $mark, ExamTimetable $timetable): void
    {
        $this->marksRepository->updateOrCreateMarks(
            $timetable,
            $mark,
            0,
            null,
            -1 // Marks for absent student
        );
    }

    private function calculatePassingStatus(array $mark, ExamTimetable $timetable): int
    {
        return $mark['obtained_marks'] >= $timetable->passing_marks ? 1 : 0;
    }

    private function calculatePercentage(array $mark): float
    {
        if ($mark['total_marks'] == 0) return 0;
        return ($mark['obtained_marks'] / $mark['total_marks']) * 100;
    }

    private function findGrade(float $percentage): string
    {
        $grade = findExamGrade($percentage);
        if (!$grade) {
            throw new GradeNotFoundException(trans('grades_data_does_not_exists'));
        }
        return $grade;
    }

    private function shouldPublishResults(array $data, ExamTimetable $timetable): bool
    {
        if ($data['marks_upload_status'] != 1) return false;

        $autoPublish = getSettings('auto_publish_exams', get_center_id())['auto_publish_exams'] ?? 0;
        return $timetable->exam->type == 1 && $autoPublish;
    }

    private function prepareExamResult(
        array $data,
        array $mark,
        float $percentage,
        string $grade,
        ExamTimetable $timetable
    ): array {
        return [
            'exam_id' => $data['exam_id'],
            'class_section_id' => $data['class_section_id'],
            'student_id' => $mark['student_id'],
            'total_marks' => $mark['total_marks'],
            'obtained_marks' => $mark['obtained_marks'],
            'percentage' => round($percentage, 2),
            'grade' => $grade,
            'session_year_id' => $timetable->session_year_id
        ];
    }
}