<?php

namespace App\Domain\Exam\Repositories;

use App\Http\Requests\Exam\UpdateExamRequest;
use App\Models\ClassSection;
use App\Models\Exam;
use App\Models\ExamClassSection;
use App\Models\ExamSequence;
use App\Models\ExamTerm;
use App\Models\ExamTimetable;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamRepository extends BaseRepository
{
    public function __construct(Exam $exam)
    {
        parent::__construct($exam);
    }

    public function create(array|Model $data): Model
    {
        /** @var Exam $exam */
        $exam = $this->newInstance();
        $exam->name = $data['subject_name'];
        $exam->type = $data['type'] ?? 1;
        $exam->description = $data['description'];
        $exam->session_year_id = getSessionYearData()->id;
        $exam->exam_term_id = $data['exam_term_id'] ?? null;
        $exam->exam_sequence_id = $data['exam_sequence_id'] ?? null;
        $exam->teacher_status = 1;
        $exam->student_status = 1;
        $exam->center_id = Auth::user()->center->id;

        return parent::create($exam->toArray());
    }

    public function associateClassSection(Exam $exam, $class_section_ids)
    {
        if ($class_section_ids) {
            $exam_class_section = [];
            foreach ($class_section_ids as $class_section_id) {
                $data = ClassSection::find($class_section_id);
                if ($data) {
                    $exam_class_section[] = array(
                        'exam_id'          => $exam->id,
                        'class_section_id' => $data->id,
                    );
                }
            }
            ExamClassSection::insert($exam_class_section);
        }
    }

    /**
     * @param \App\Http\Requests\Exam\UpdateExamRequest $request
     * @param int $id
     * @param $attributes
     * @return bool
     * @throws \Exception
     */
    public function update($request, $id = 0, $attributes = 'id'): bool
    {
        // Mise à jour de l'examen
        $exam = Exam::findOrFail($id);
        $exam->update([
            'teacher_status' => $request->teacher_status ?? 0,
            'student_status' => $request->student_status ?? 0
        ]);

        // Traitement des emplois du temps
        if ($request->filled('edit_timetable')) {
            $this->processTimetables($request);
        }
        return true;
    }

    public function getExamWithMarksAndTimetable(
        int $examId,
        int $studentId,
        int $classSectionId
    ): Exam {
        return $this->model
            ->with([
                'marks' => function ($query) use ($studentId) {
                    $query->with('student:id,class_section_id')
                        ->selectRaw('SUM(obtained_marks) as total_obtained_marks, student_id')
                        ->where('student_id', $studentId)
                        ->groupBy('student_id');
                },
                'timetable' => function ($query) use ($examId, $classSectionId) {
                    $query->selectRaw('exam_id, SUM(total_marks) as total_marks')
                        ->where([
                            'exam_id' => $examId,
                            'class_section_id' => $classSectionId
                        ]);
                }
            ])
            ->findOrFail($examId);
    }

    private function processTimetables(UpdateExamRequest $request): void
    {
        $insertData = [];

        foreach ($request->edit_timetable as $timetable) {
            if ($this->hasConflictingExam($request, $timetable)) {
                throw new \Exception("Other Exam already exists between {$timetable['start_time']} - {$timetable['end_time']}");
            }

            if (isset($timetable['timetable_id'])) {
                $this->updateExistingTimetable($timetable);
            } else {
                $insertData[] = $this->prepareNewTimetableData($request, $timetable);
            }
        }

        if (!empty($insertData)) {
            ExamTimetable::insert($insertData);
        }
    }

    private function hasConflictingExam(UpdateExamRequest $request, array $timetable): bool
    {
        if ($timetable['start_time'] == "00:00:00" || $timetable['end_time'] == "00:00:00") {
            return false;
        }

        $query = ExamTimetable::checkIfSlotAvailable(
            $request->class_section_id,
            $timetable['date'],
            $timetable['start_time'],
            $timetable['end_time']
        );

        if (isset($timetable['timetable_id'])) {
            $query->where('id', '!=', $timetable['timetable_id']);
        }

        return $query->count() > 0;
    }

    private function updateExistingTimetable(array $timetable): void
    {
        $timetableDb = ExamTimetable::findOrFail($timetable['timetable_id']);

        $timetableDb->update([
            'subject_id' => $timetable['subject_id'],
            'total_marks' => $timetable['total_marks'],
            'passing_marks' => $timetable['passing_marks'],
            'start_time' => $timetable['start_time'] != "00:00:00" ? $timetable['start_time'] : null,
            'end_time' => $timetable['end_time'] != "00:00:00" ? $timetable['end_time'] : null,
            'date' => !empty($timetable['date']) ? date('Y-m-d', strtotime($timetable['date'])) : null
        ]);
    }

    private function prepareNewTimetableData(UpdateExamRequest $request, array $timetable): array
    {
        return [
            'exam_id' => $request->exam_id,
            'class_section_id' => $request->class_section_id,
            'subject_id' => $timetable['subject_id'],
            'total_marks' => 20, // Valeurs par défaut comme dans votre code
            'passing_marks' => 10,
            'start_time' => $timetable['start_time'],
            'end_time' => $timetable['end_time'],
            'session_year_id' => $request->session_year_id,
            'date' => date('Y-m-d', strtotime($timetable['date']))
        ];
    }

    public function getWithRelations(int $examClassSectionId): ?Exam
    {
        return $this->model->with([
            'marks' => function ($query) {
                $query->with('student:id,class_section_id')
                    ->selectRaw('SUM(obtained_marks) as total_obtained_marks, student_id')
                    ->groupBy('student_id');
            },
            'timetable' => function ($query) {
                $query->selectRaw('exam_id, SUM(total_marks) as total_marks')
                    ->groupBy('class_section_id');
            },
            'exam_class_section'
        ])->whereHas('exam_class_section', function ($query) use ($examClassSectionId) {
            $query->where('id', $examClassSectionId);
        })->first();
    }

    public function getUnpublishedCompletedExams()
    {
        return ExamClassSection::owner()
            ->with([
                'exam.timetable',
                'class_section.class.stream',
                'class_section.section'
            ])
            ->whereHas('exam', function ($q) {
                $q->where('teacher_status', 1)
                    ->where('type', 2);
            })
            ->whereHas('exam.timetable', function ($q) {
                $q->where('date', '!=', '0000-00-00');
            })
            ->whereHas('class_section.class', function ($q) {
                $q->activeMediumOnly();
            })
            ->where('publish', 0)
            ->get();
    }

    public function getExamDates(int $examId, int $classSectionId): array
    {
        $dates = ExamTimetable::select([
            DB::raw('MIN(date) as start_date'),
            DB::raw('MAX(date) as end_date')
        ])
            ->where([
                'exam_id' => $examId,
                'class_section_id' => $classSectionId
            ])
            ->first();

        return [
            'start_date' => $dates->start_date,
            'end_date' => $dates->end_date
        ];
    }

    public function getSequentialExams(array $filters = []): Collection
    {
        $query = ExamClassSection::owner()
            ->with([
                'exam.timetable.subject',
                'class_section',
                'class_section.subject_teachers'
            ]);

        if (isset($filters['teacher_id'])) {
            $query->whereHas('exam.timetable', function ($q) use ($filters) {
                $q->whereHas('class_section.subject_teachers', function ($sq) use ($filters) {
                    $sq->where('teacher_id', $filters['teacher_id'])
                        ->whereColumn('subject_teachers.subject_id', 'exam_timetables.subject_id');
                });
            });
        }

        if (isset($filters['sequence_id'])) {
            $query->whereHas('exam', function ($q) use ($filters) {
                $q->where('exam_sequence_id', $filters['sequence_id'])
                    ->owner()
                    ->where('type', 1);
            });
        } else {
            $query->whereHas('exam', function ($q) {
                $q->owner()->where('type', 1);
            });
        }

        if (isset($filters['class_id'])) {
            $query->where('class_section_id', $filters['class_id'])
                ->whereHas('class_section.class', function ($q) {
                    $q->activeMediumOnly();
                });
        } else {
            $query->whereHas('class_section.class', function ($q) {
                $q->activeMediumOnly();
            });
        }

        return $query->get();
    }

    public function getClassSections(): Collection
    {
        return ClassSection::owner()
            ->with('class.stream', 'section')
            ->whereHas('class', function ($q) {
                $q->activeMediumOnly();
            })
            ->get();
    }

    public function getActiveSequences(): Collection
    {
        $termIds = ExamTerm::owner()
            ->currentSessionYear()
            ->currentMedium()
            ->pluck('id');

        return ExamSequence::owner()
            ->whereIn('exam_term_id', $termIds)
            ->where('status', 1)
            ->get();
    }
}
