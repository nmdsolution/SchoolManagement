<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassSchool;

class ClassSchoolRepository extends BaseRepository
{
    public function __construct(ClassSchool $model)
    {
        parent::__construct($model);
    }

    public function list()
    {
        return $this->model->owner()
            ->with(['class_section.teacher'])
            ->where('center_id', get_center_id())
            ->get();
    }

    public function getWithCompetencies(int $id)
    {
        $qb = $this->model->owner()->whereHas('competencies', function ($query) use ($id) {
            $query->where('class_id', $id)->whereHas('competency_domain', function ($query) {
                $query->where('center_id', get_center_id())->currentMediumOnly();
            });
        });

        return $qb->get();
    }

}