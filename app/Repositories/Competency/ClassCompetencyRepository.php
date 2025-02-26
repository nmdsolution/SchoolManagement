<?php

namespace App\Repositories\Competency;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Competency\ClassCompetency;

class ClassCompetencyRepository extends BaseRepository
{
    public function __construct(ClassCompetency $model)
    {
        parent::__construct($model);
    }

    public function getForClass(int $id)
    {
        $qb = $this->model->owner()->with('class')->where('class_id', $id);

        return $qb->get();
    }

    public function getForEdit(int $id)
    {
        $qb = $this->model->with('class')->whereHas('class', function ($query) {
            $query->where('center_id', Auth::user()->center_id);
        });

        return $qb->find($id);
    }
    
    public function listForTable(array $data)
    {
        $query = $this->model->with('class');

        if (isset($data['class_id'])) {
            $query->where('class_id', $data['class_id']);
        }

        if (isset($data['sort'])) {
            $query->orderBy($data['sort'], $data['order']);
        }

        if (isset($data['limit'])) {
            $query->limit($data['limit']);
        }

        if (isset($data['offset'])) {
            $query->offset($data['offset']);
        }

        return $query->get();
    }
}