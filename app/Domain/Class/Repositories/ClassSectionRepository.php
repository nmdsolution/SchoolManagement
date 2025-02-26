<?php

namespace App\Domain\Class\Repositories;

use App\Models\ClassSection;
use App\Repositories\BaseRepository;

class ClassSectionRepository extends BaseRepository
{
    public function __construct(ClassSection $classSection)
    {
        parent::__construct($classSection);
    }

    public function findByClassTeacher(int $teacherId, int $centerId): ?ClassSection
    {
        return ClassSection::where('class_teacher_id', $teacherId)
            ->whereHas('class', function ($q) use ($centerId) {
                $q->where('center_id', $centerId);
            })
            ->first();
    }
}