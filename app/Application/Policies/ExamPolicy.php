<?php

namespace App\Application\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        dd($user);
    }

    public function view(User $user, Exam $exam): bool
    {
    }

    public function create(User $user): bool
    {
        return $user->can('create-specific-exam');
    }

    public function update(User $user, Exam $exam): bool
    {
    }

    public function delete(User $user, Exam $exam): bool
    {
    }

    public function restore(User $user, Exam $exam): bool
    {
    }

    public function forceDelete(User $user, Exam $exam): bool
    {
    }

    public function listSequential(User $user): bool
    {
        dd($user);
        return $user->can('list-sequential-exam');
    }
}
