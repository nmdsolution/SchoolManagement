<?php

namespace App\Domain\Center\Repositories;

use App\Models\CenterTeacher;
use App\Repositories\BaseRepository;

class CenterTeacherRepository extends BaseRepository
{
    public function __construct(CenterTeacher $centerTeacher)
    {
        parent::__construct($centerTeacher);
    }

    public function findManagingTeacher(int $centerId, int $userId): ?CenterTeacher
    {
        return CenterTeacher::where('center_id', $centerId)
            ->where('user_id', $userId)
            ->where('manage_student_parent', 1)
            ->first();
    }
}