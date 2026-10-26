<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Only a super_admin may manage another super_admin account.
     */
    private function canManageTarget(AuthUser $authUser, User $target): bool
    {
        return ! $target->hasRole('super_admin') || $authUser->hasRole('super_admin');
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:User');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:User');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:User');
    }

    public function update(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('Update:User') && $this->canManageTarget($authUser, $user);
    }

    public function delete(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('Delete:User') && $this->canManageTarget($authUser, $user);
    }

    public function restore(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('Restore:User') && $this->canManageTarget($authUser, $user);
    }

    public function forceDelete(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('ForceDelete:User') && $this->canManageTarget($authUser, $user);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:User');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:User');
    }

    public function replicate(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('Replicate:User') && $this->canManageTarget($authUser, $user);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:User');
    }
}
