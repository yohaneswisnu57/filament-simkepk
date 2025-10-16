<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StatusReview;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusReviewPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StatusReview');
    }

    public function view(AuthUser $authUser, StatusReview $statusReview): bool
    {
        return $authUser->can('View:StatusReview');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StatusReview');
    }

    public function update(AuthUser $authUser, StatusReview $statusReview): bool
    {
        return $authUser->can('Update:StatusReview');
    }

    public function delete(AuthUser $authUser, StatusReview $statusReview): bool
    {
        return $authUser->can('Delete:StatusReview');
    }

    public function restore(AuthUser $authUser, StatusReview $statusReview): bool
    {
        return $authUser->can('Restore:StatusReview');
    }

    public function forceDelete(AuthUser $authUser, StatusReview $statusReview): bool
    {
        return $authUser->can('ForceDelete:StatusReview');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StatusReview');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StatusReview');
    }

    public function replicate(AuthUser $authUser, StatusReview $statusReview): bool
    {
        return $authUser->can('Replicate:StatusReview');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StatusReview');
    }

}