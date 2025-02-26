<?php

namespace App\Domain\User\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\Domain\User\Repositories\StaffRepository;
use App\Models\Staff;

class UserCenterAssignmentService
{
    public function __construct(
        private StaffRepository $staffRepository
    )
    {
        
    }

    public function setUserCenter(Request $request, ?int $id): void
    {
        /** @var User */
        $user = auth()->user();
        
        $this->removeExistingRoles($user);
        
        if ($id === -1) {
            $this->assignSuperAdminRoles($user);
            $this->setSessionCenterId($request, -1);
        } else {
            $this->assignCenterRoles($user, $id);
            $this->setSessionCenterId($request, $id);
        }
    }

    private function removeExistingRoles(User $user): void
    {
        $userRoles = $this->staffRepository->getUserRoles($user->id);
        
        foreach ($userRoles as $role) {
            $user->removeRole($role->role->id);
        }
        
        $user->removeRole('Teacher');
    }

    private function assignSuperAdminRoles(User $user): void
    {
        $staff = $this->staffRepository->findStaff(null, $user->id);
        $this->assignStaffRoles($user, $staff);
    }

    private function assignCenterRoles(User $user, int $centerId): void
    {
        $staff = $this->staffRepository->findStaff($centerId, $user->id);
        $this->assignStaffRoles($user, $staff);
    }

    private function assignStaffRoles(User $user, ?Staff $staff): void
    {
        if (!$staff) {
            return;
        }

        $staffRoles = $this->staffRepository->getStaffRoles($staff->id);
        
        foreach ($staffRoles as $role) {
            $user->assignRole([$role->role->id]);
        }
    }

    private function setSessionCenterId(Request $request, int $centerId): void
    {
        $request->session()->put('center_id', $centerId);
    }
}