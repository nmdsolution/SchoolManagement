<?php

namespace App\Repositories;

use App\Models\Mediums;

class MediumRepository extends BaseRepository
{
    public function __construct(Mediums $medium)
    {
        parent::__construct($medium);
    }

}