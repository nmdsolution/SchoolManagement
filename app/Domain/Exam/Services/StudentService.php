<?php

namespace App\Domain\Exam\Services;

use App\Domain\Exam\Repositories\StudentRepository;

class StudentService
{
    protected StudentRepository $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function getStudentsWithMarks(int $classSectionId, int $sequenceId, int $sessionYear, int $offset, int $limit, string $sort, string $order, ?string $search = null): array
    {
        $total = $this->studentRepository->countStudentsWithMarks($classSectionId, $sequenceId, $sessionYear, $search);
        $students = $this->studentRepository->getStudentsWithMarks($classSectionId, $sequenceId, $sessionYear, $offset, $limit, $sort, $order, $search);

        $rows = [];
        $no = 1;
        foreach ($students as $student) {
            $totalMarks = array_sum(array_column(array_column($student->exam_marks->toArray(), 'timetable'), 'total_marks'));
            $obtainedMarks = $student->exam_marks->sum('obtained_marks');
            foreach ($student->exam_marks as $mark) {
                if ($mark->obtained_marks < 0) {
                    $mark->obtained_marks = '/';
                    $obtainedMarks += 1;
                }
            }
            $rows[] = [
                'id' => $student->id,
                'no' => $no++,
                'student_name' => $student->user->first_name,
                'total_marks' => $totalMarks,
                'avg_marks' => $student->avg,
                'obtained_marks' => $obtainedMarks,
                'exam_marks' => $student->exam_marks,
                'operate' => '<div class="actions"><a class="btn btn-sm bg-success-light edit-data btn-rounded" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal"><i class="feather-edit"></i></a></div>&nbsp;&nbsp;',
            ];
        }

        return [
            'total' => $total,
            'rows' => $rows,
        ];
    }
}
