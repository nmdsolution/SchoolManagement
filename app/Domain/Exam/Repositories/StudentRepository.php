<?php

namespace App\Domain\Exam\Repositories;

use App\Models\Students;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class StudentRepository extends BaseRepository
{
    public function __construct(Students $students)
    {
        parent::__construct($students);
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
