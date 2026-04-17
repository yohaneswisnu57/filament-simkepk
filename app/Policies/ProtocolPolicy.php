<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Protocol;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProtocolPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Protocol');
    }

    public function view(AuthUser $authUser, Protocol $protocol): bool
    {
        return $authUser->can('View:Protocol');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Protocol');
    }

    public function update(AuthUser $authUser, Protocol $protocol): bool
    {
        return $authUser->can('Update:Protocol');
    }

    public function delete(AuthUser $authUser, Protocol $protocol): bool
    {
        return $authUser->can('Delete:Protocol');
    }

    public function restore(AuthUser $authUser, Protocol $protocol): bool
    {
        return $authUser->can('Restore:Protocol');
    }

    public function forceDelete(AuthUser $authUser, Protocol $protocol): bool
    {
        return $authUser->can('ForceDelete:Protocol');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Protocol');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Protocol');
    }

    public function replicate(AuthUser $authUser, Protocol $protocol): bool
    {
        return $authUser->can('Replicate:Protocol');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Protocol');
    }

}