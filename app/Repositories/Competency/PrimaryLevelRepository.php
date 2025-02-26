<?php

namespace App\Repositories\Competency;

use App\Models\Competency\PrimaryLevel;
use App\Repositories\BaseRepository;

class PrimaryLevelRepository extends BaseRepository
{
    public function __construct(protected PrimaryLevel $primaryLevel)
    {
        parent::__construct($primaryLevel);
    }
}