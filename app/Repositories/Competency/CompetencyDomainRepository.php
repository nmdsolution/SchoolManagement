<?php

namespace App\Repositories\Competency;

use App\Models\Competency\CompetencyDomain;
use App\Repositories\BaseRepository;

class CompetencyDomainRepository extends BaseRepository
{
    public function __construct(protected CompetencyDomain $competencyDomain)
    {
        parent::__construct($competencyDomain);
    }

    public function list()
    {
        return $this->model->owner()
            ->withCount('competencies')
            ->where('center_id', get_center_id())
            ->activeMediumOnly()
            ->get();
    }
}