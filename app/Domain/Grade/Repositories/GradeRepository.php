<?php

namespace App\Domain\Grade\Repositories;

use App\Models\Grade;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class GradeRepository extends BaseRepository
{
    public function __construct(Grade $grade)
    {
        parent::__construct($grade);
    }

    public function upsertGrades(array $grades): void
    {
        $gradesData = array_map(function ($grade) {
            return [
                'id' => $grade['id'] ?? null,
                'starting_range' => $grade['starting_range'],
                'ending_range' => $grade['ending_range'],
                'grade' => $grade['grades'],
                'remarks' => $grade['remarks'],
                'center_id' => auth()->user()->center->id,
                'medium_id' => getCurrentMedium()->id
            ];
        }, $grades);

        $this->model->upsert(
            $gradesData,
            ['id'],
            [
                'starting_range',
                'ending_range',
                'grade',
                'remarks',
                'center_id',
                'medium_id'
            ]
        );
    }

    public function getGradesByRange(float $percentage): ?Grade
    {
        return $this->model
            ->where('starting_range', '<=', $percentage)
            ->where('ending_range', '>=', $percentage)
            ->where('center_id', auth()->user()->center->id)
            ->where('medium_id', getCurrentMedium()->id)
            ->first();
    }

    public function getAllGrades(): Collection
    {
        return $this->model
            ->where('center_id', auth()->user()->center->id)
            ->where('medium_id', getCurrentMedium()->id)
            ->orderBy('starting_range')
            ->get();
    }

}