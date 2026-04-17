<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\About;
use Illuminate\Auth\Access\HandlesAuthorization;

class AboutPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:About');
    }

    public function view(AuthUser $authUser, About $about): bool
    {
        return $authUser->can('View:About');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:About');
    }

    public function update(AuthUser $authUser, About $about): bool
    {
        return $authUser->can('Update:About');
    }

    public function delete(AuthUser $authUser, About $about): bool
    {
        return $authUser->can('Delete:About');
    }

    public function restore(AuthUser $authUser, About $about): bool
    {
        return $authUser->can('Restore:About');
    }

    public function forceDelete(AuthUser $authUser, About $about): bool
    {
        return $authUser->can('ForceDelete:About');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:About');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:About');
    }

    public function replicate(AuthUser $authUser, About $about): bool
    {
        return $authUser->can('Replicate:About');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:About');
    }

}