<?php

namespace App\Domain\Exam\Repositories;

use App\Models\ExamStatistics;
use App\Repositories\BaseRepository;
use Awobaz\Compoships\Database\Eloquent\Model;

class ExamStatisticsRepository extends BaseRepository
{
    public function __construct(ExamStatistics $examStatistics)
    {
        parent::__construct($examStatistics);
    }

    public function updateOrCreate(array $attributes, array $values = []): ExamStatistics
    {
        return $this->model->updateOrCreate($attributes, $values);
    }
}