<?php

namespace App\Domain\Exam\Repositories;

use App\Models\ExamTerm;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ExamTermRepository extends BaseRepository
{
    public function __construct(ExamTerm $model)
    {
        parent::__construct($model);
    }

    public function getAllCenter(): ?Collection
    {
        return $this->model
            ->owner()
            ->currentSessionYear()
            ->currentMedium()
            ->get();
    }
}