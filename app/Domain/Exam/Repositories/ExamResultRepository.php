<?php

namespace App\Domain\Exam\Repositories;

use App\Models\ExamResult;
use App\Models\ExamTimetable;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ExamResultRepository extends BaseRepository
{
    public function __construct(ExamResult $examResult)
    {
        parent::__construct($examResult);
    }

    public function deleteByExamId(int $examId): void
    {
        ExamResult::where('exam_id', $examId)->delete();
    }

    public function updateOrCreate(array $attributes, array $values = []): ExamResult
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    public function upsertResults(array $results): void
    {
        $uniqueKeys = ['exam_id', 'class_section_id', 'student_id'];
        $updateColumns = [
            'total_marks',
            'obtained_marks',
            'percentage',
            'grade',
            'session_year_id'
        ];

        $this->model->upsert($results, $uniqueKeys, $updateColumns);
    }

    public function updateResult(
        int $examId,
        int $studentId,
        float $obtainedMarks,
        float $percentage,
        string $grade
    ): void {
        $result = $this->model
            ->where([
                'exam_id' => $examId,
                'student_id' => $studentId
            ])
            ->firstOrFail();

        $this->update([
            'obtained_marks' => $obtainedMarks,
            'percentage' => round($percentage, 2),
            'grade' => $grade
        ], $result->id);
    }

    public function getStudentResults(int $studentId, int $examId): Collection
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('exam_id', $examId)
            ->get();
    }

    public function getExamResults(array $params): array
    {
        $query = $this->buildResultsQuery($params);
        
        $total = $query->count();

        $results = $query->orderBy($params['sort'], $params['order'])
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return [
            'total' => $total,
            'rows' => $this->formatResultsData($results)
        ];
    }

    private function buildResultsQuery(array $params): Builder
    {
        $examTimetableIds = ExamTimetable::where('exam_id', $params['exam_id'])
            ->pluck('id');

        $query = $this->model
            ->with([
                'student.user:id,first_name,last_name',
                'session_year:id,name',
                'student.exam_marks' => function ($q) use ($examTimetableIds) {
                    $q->whereIn('exam_timetable_id', $examTimetableIds)
                        ->with('timetable', 'subject:id,name');
                }
            ])
            ->where('exam_id', $params['exam_id']);

        $this->applySearchFilters($query, $params);

        return $query;
    }

    private function applySearchFilters(Builder $query, array $params): void
    {
        if (!empty($params['search'])) {
            $search = $params['search'];
            $searchDate = date('Y-m-d H:i:s', strtotime($search));

            $query->where(function ($q) use ($search, $searchDate) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhere('total_marks', 'LIKE', "%$search%")
                    ->orWhere('grade', 'LIKE', "%$search%")
                    ->orWhere('obtained_marks', 'LIKE', "%$search%")
                    ->orWhere('percentage', 'LIKE', "%$search%")
                    ->orWhere('created_at', 'LIKE', "%$searchDate%")
                    ->orWhere('updated_at', 'LIKE', "%$searchDate%")
                    ->orWhereHas('student.user', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")
                            ->orWhere('last_name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('session_year', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    });
            });
        }
    }

    private function formatResultsData(Collection $results): array
    {
        $rows = [];
        $no = 1;

        foreach ($results as $result) {
            $rows[] = [
                'id' => $result->id,
                'no' => $no++,
                'student_id' => $result->student_id,
                'student_name' => $result->student->user->first_name . ' ' . $result->student->user->last_name,
                'total_marks' => $result->total_marks,
                'obtained_marks' => $result->obtained_marks,
                'percentage' => $result->percentage,
                'grade' => $result->grade,
                'session_year_name' => $result->session_year->name,
                'created_at' => $result->created_at,
                'updated_at' => $result->updated_at,
                'operate' => $this->generateOperateButtons($result),
                'data' => $result->student->exam_marks
            ];
        }

        return $rows;
    }

    private function generateOperateButtons(ExamResult $result): string
    {
        return sprintf(
            '<a href="#" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" 
                data-id="%d" data-student_id="%d" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal">
                <i class="feather-edit"></i>
            </a>&nbsp;&nbsp;',
            $result->id,
            $result->student_id
        );
    }
}