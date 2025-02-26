<?php

namespace App\Domain\Student\Repositories;

use App\Models\Students;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StudentsRepository extends BaseRepository
{
    public function __construct(Students $model)
    {
        parent::__construct($model);
    }

    public function getTotalStudents(int $sessionYearId, int $classSectionId): int
    {
        return Students::Owner()
            ->whereHas('studentSessions', function ($query) use ($sessionYearId, $classSectionId) {
                $query->where('session_year_id', $sessionYearId)
                    ->where('class_section_id', $classSectionId);
            })->count();
    }
    public function getStudentListBuilder(int $class_section_id): Builder
    {
        return $this->model->select('students.*', 'users.id as user_id', 'users.first_name', 'users.last_name')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->with([
                'user:id,first_name,last_name',
                'class_section'
            ])
            ->where('class_section_id', $class_section_id)
            ->whereHas('studentSessions', function ($q) {
                $q->where('session_year_id', getSettings('session_year')['session_year']);
            });
    }

    public function getStudentListForCompetencyMarks(int $class_section_id): Builder
    {
        return $this->getStudentListBuilder($class_section_id)
            ->with('exam_marks');
    }

    public function getStudentListForMarks(int $class_section_id, int $exam_timetable_id, int $subject_id): Builder
    {
        return $this->getStudentListBuilder($class_section_id) // Cloner la requête de base pour l'étendre
        ->with([
            'class_section.class.allSubjects' => function ($q) use($subject_id) {
                $q->where('subject_id', $subject_id)->with('subject');
            },
            'exam_marks' => function ($q) use ($exam_timetable_id) {
                $q->where('exam_timetable_id', $exam_timetable_id);
            }
        ])
            ->join('users as u', 'u.id', '=', 'students.user_id') // Si un alias est nécessaire pour une jointure supplémentaire
            ->select('students.*', 'u.first_name', 'u.last_name');

    }

    public function getStudentListForApi(int $class_section_id): Builder
    {
        return $this->getStudentListBuilder($class_section_id) // Cloner la requête de base pour l'étendre
        ->with([
            'user:id,first_name,last_name,image,gender,dob,current_address,permanent_address',
            'studentSessions' => function ($q) {
                $q->where('session_year_id', getSettings('session_year')['session_year']);
            }
        ])
            ->orderBy('users.first_name', 'asc');

    }

    public function getStudentsWithMarks(int $classSectionId, int $sequenceId, int $sessionYear, int $offset, int $limit, string $sort, string $order, ?string $search = null): Collection
    {
        $query = $this->model->select(['students.*', 'erss.avg as avg'])
            ->whereHas('studentSessions', function ($query) use ($classSectionId, $sessionYear) {
                $query->where('session_year', $sessionYear)
                    ->where('class_section_id', $classSectionId);
            })
            ->with(['user:id,first_name,last_name', 'exam_marks' => function ($q) use ($sequenceId, $sessionYear) {
                $q->whereHas('exam', function ($q) use ($sequenceId, $sessionYear) {
                    $q->where('exam_sequence_id', $sequenceId)->where('exams.session_year_id', $sessionYear);
                });
            }, 'exam_marks.timetable:id,exam_id,total_marks', 'exam_marks.subject'])
            ->whereHas('exam_marks.exam', function ($q) use ($sequenceId) {
                $q->where('exam_sequence_id', $sequenceId);
            })
            ->join('exam_report_student_sequences as erss', 'erss.student_id', 'students.id')
            ->where(['erss.class_section_id' => $classSectionId, 'erss.exam_sequence_id' => $sequenceId])
            ->orderBy($sort, $order);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")->orWhereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%$search%")->orWhere('last_name', 'LIKE', "%$search%");
                });
            });
        }

        return $query->skip($offset)->take($limit)->get();
    }

    public function countStudentsWithMarks(int $classSectionId, int $sequenceId, int $sessionYear, ?string $search = null): int
    {
        $query = $this->model->whereHas('studentSessions', function ($query) use ($classSectionId, $sessionYear) {
            $query->where('session_year', $sessionYear)
                ->where('class_section_id', $classSectionId);
        })
        ->whereHas('exam_marks.exam', function ($q) use ($sequenceId) {
            $q->where('exam_sequence_id', $sequenceId);
        })
        ->join('exam_report_student_sequences as erss', 'erss.student_id', 'students.id')
        ->where(['erss.class_section_id' => $classSectionId, 'erss.exam_sequence_id' => $sequenceId]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")->orWhereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%$search%")->orWhere('last_name', 'LIKE', "%$search%");
                });
            });
        }

        return $query->count();
    }

    public function getStudentUserIds(int $classSectionId): Collection
    {
        return Students::select('user_id')
            ->where('class_section_id', $classSectionId)
            ->pluck('user_id');
    }
}