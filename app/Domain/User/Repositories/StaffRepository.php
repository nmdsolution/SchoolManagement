<?php

namespace App\Domain\User\Repositories;

use App\Models\Staff;
use App\Models\StaffRole;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class StaffRepository extends BaseRepository
{
    public function __construct(Staff $staff)
    {
        parent::__construct($staff);
    }

    public function findStaffByCenterAndUser(int $centerId, int $userId): ?Staff
    {
        return Staff::where('center_id', $centerId)
            ->where('user_id', $userId)
            ->first();
    }

    public function getStaffRoles(int $staffId): Collection
    {
        return StaffRole::where('staff_id', $staffId)->get();
    }

    public function findStaff(?int $centerId, int $userId): ?Staff
    {
        return Staff::where('center_id', $centerId)
            ->where('user_id', $userId)
            ->first();
    }

    
    public function getUserRoles(int $userId): Collection
    {
        return StaffRole::where('user_id', $userId)->get();
    }
}