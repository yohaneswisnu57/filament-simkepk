<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ReviewerKelompok;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewerKelompokPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ReviewerKelompok');
    }

    public function view(AuthUser $authUser, ReviewerKelompok $reviewerKelompok): bool
    {
        return $authUser->can('View:ReviewerKelompok');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ReviewerKelompok');
    }

    public function update(AuthUser $authUser, ReviewerKelompok $reviewerKelompok): bool
    {
        return $authUser->can('Update:ReviewerKelompok');
    }

    public function delete(AuthUser $authUser, ReviewerKelompok $reviewerKelompok): bool
    {
        return $authUser->can('Delete:ReviewerKelompok');
    }

    public function restore(AuthUser $authUser, ReviewerKelompok $reviewerKelompok): bool
    {
        return $authUser->can('Restore:ReviewerKelompok');
    }

    public function forceDelete(AuthUser $authUser, ReviewerKelompok $reviewerKelompok): bool
    {
        return $authUser->can('ForceDelete:ReviewerKelompok');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ReviewerKelompok');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ReviewerKelompok');
    }

    public function replicate(AuthUser $authUser, ReviewerKelompok $reviewerKelompok): bool
    {
        return $authUser->can('Replicate:ReviewerKelompok');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ReviewerKelompok');
    }

}