<?php

namespace App\Domain\Exam\Repositories;

use App\Models\ExamSequence;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class ExamSequenceRepository extends BaseRepository
{
    public function __construct(ExamSequence $examSequence)
    {
        parent::__construct($examSequence);
    }

    public function getByTermId(array|Collection $termIds, ?int $status = null): ?Collection
    {
        $q = $this->model->newInstance()
            ->whereIn('exam_term_id', $termIds);
        if ($status) {
            $q->where('status', $status);
        }

        return $q->get();
    }

    public function getSequences(array $examTermIds, int $offset, int $limit, string $sort, string $order, ?string $search = null): Collection
    {
        $query = $this->model->owner()->whereIn('exam_term_id', $examTermIds)->with('term', 'auto_sequence_exam', 'auto_sequence_exam_class_section.class', 'auto_sequence_exam_class_section.section');

        if ($search) {
            $searchColumns = ['id', 'name'];
            $query->where(function ($q) use ($searchColumns, $search) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'LIKE', "%$search%");
                }
            });
        }

        return $query->orderBy($sort, $order)->skip($offset)->take($limit)->get();
    }

    public function countSequences(array $examTermIds, ?string $search = null): int
    {
        $query = $this->model->owner()->whereIn('exam_term_id', $examTermIds);

        if ($search) {
            $searchColumns = ['id', 'name'];
            $query->where(function ($q) use ($searchColumns, $search) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'LIKE', "%$search%");
                }
            });
        }

        return $query->count();
    }

    public function updateSequence(int $id, array $data): bool
    {
        $sequence = $this->model->owner()->with('auto_sequence_exam')->where('id', $id)->firstOrFail();
        return $sequence->update($data);
    }

    public function findSequence(int $id): ExamSequence
    {
        return $this->model->owner()->with('auto_sequence_exam')->where('id', $id)->firstOrFail();
    }
}