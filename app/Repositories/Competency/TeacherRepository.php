<?php

namespace App\Repositories\Competency;

use App\Models\Teacher;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TeacherRepository extends BaseRepository
{
    public function __construct(protected Teacher $teacher)
    {
        parent::__construct($teacher);
    }

    public function getByCenter(): Builder
    {
        return $this->model->owner()->with('user');
    }
}