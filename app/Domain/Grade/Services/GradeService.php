<?php

namespace App\Domain\Grade\Services;

use App\Domain\Grade\Repositories\GradeRepository;
use App\Exceptions\GradesOverlapException;
use Illuminate\Support\Facades\DB;

class GradeService
{
    public function __construct(private GradeRepository $gradeRepository)
    {
    }

    public function createGrades(array $data): void
    {
        DB::transaction(function () use ($data) {
            $this->gradeRepository->upsertGrades($data['grade']);
        });
    }

    public function validateGradesOverlap(array $grades): void
    {
        $sortedGrades = collect($grades)->sortBy('starting_range');
        
        $previous = null;
        foreach ($sortedGrades as $grade) {
            if ($previous && $grade['starting_range'] <= $previous['ending_range']) {
                throw new GradesOverlapException('Les plages de notes se chevauchent');
            }
            $previous = $grade;
        }
    }
}