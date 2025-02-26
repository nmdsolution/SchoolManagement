<?php

namespace App\Domain\User\Services;

use App\Domain\Center\Repositories\CenterTeacherRepository;
use App\Domain\Class\Repositories\ClassSectionRepository;
use App\Domain\User\Repositories\StaffRepository;
use App\Domain\User\Repositories\UserRepository;
use App\Models\User;

class UserCenterService
{
    public function __construct(
        private UserRepository $userRepository,
        private StaffRepository $staffRepository,
        private ClassSectionRepository $classSectionRepository,
        private CenterTeacherRepository $centerTeacherRepository
    ) {
    }

    public function setUserCenter(User $user, ?int $centerId): void
    {
        $this->userRepository->removeAllRoles($user);
        session()->put('center_id', $centerId);

        $this->assignTeacherRole($user);
        $this->assignStaffRoles($user, $centerId);
        $this->handleClassTeacherRole($user, $centerId);
        $this->handleStudentParentManagementRole($user, $centerId);
    }

    private function assignTeacherRole(User $user): void
    {
        $this->userRepository->assignRoleByName($user, 'Teacher');
    }

    private function assignStaffRoles(User $user, int $centerId): void
    {
        $staff = $this->staffRepository->findStaffByCenterAndUser($centerId, $user->id);
        
        if ($staff) {
            $staffRoles = $this->staffRepository->getStaffRoles($staff->id);
            foreach ($staffRoles as $staffRole) {
                $this->userRepository->assignRole($user, $staffRole->role->id);
            }
        }
    }

    private function handleClassTeacherRole(User $user, int $centerId): void
    {
        if (!$user->teacher) {
            return;
        }

        $classSection = $this->classSectionRepository->findByClassTeacher(
            $user->teacher->id,
            $centerId
        );

        if ($classSection) {
            $classTeacherRole = $this->userRepository->findRoleByName('Class Teacher');
            $this->userRepository->assignRole($user, $classTeacherRole->id);
        } else {
            $this->userRepository->removeRole($user, 'Class Teacher');
        }
    }

    private function handleStudentParentManagementRole(User $user, int $centerId): void
    {
        $centerTeacher = $this->centerTeacherRepository->findManagingTeacher(
            $centerId,
            $user->id
        );

        if ($centerTeacher) {
            $manageRole = $this->userRepository->findRoleByName('Manage Student & Parent');
            $this->userRepository->assignRole($user, $manageRole->id);
        } else {
            $this->userRepository->removeRole($user, 'Manage Student & Parent');
        }
    }
}