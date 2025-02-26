<?php

namespace App\Domain\User\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function assignRoleByName(User $user, string $roleName): void
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $user->assignRole([$role->id]);
    }

    public function removeAllRoles(User $user): void
    {
        foreach ($user->getRoleNames() as $role) {
            $user->removeRole($role);
        }
    }

    public function assignRole(User $user, string|int $roleId): void
    {
        $user->assignRole([$roleId]);
    }

    public function removeRole(User $user, string $roleName): void
    {
        $user->removeRole($roleName);
    }

    public function findRoleByName(string $name): ?Role
    {
        return Role::where('name', $name)->first();
    }

    public function getSuperTeachers(): Collection
    {
        return $this->model->whereHas('roles', function ($q) {
            $q->where('name', 'Super Teacher');
        })->get()->pluck('full_name', 'id');
    }
}