<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Protocol;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProtocolPolicy
{
    use HandlesAuthorization;

    private function canAccessProtocol(AuthUser $authUser, Protocol $protocol): bool
    {
        if ($authUser->hasRole(['super_admin', 'admin', 'sekertaris'])) {
            return true;
        }

        // Check ownership
        if ($protocol->user_id === $authUser->id) {
            return true;
        }

        // Check if assigned reviewer
        if ($authUser->hasRole(['reviewer', 'panel_reviewer', 'Ketua Reviewer'])) {
            if ($protocol->reviewers()->where('users.id', $authUser->id)->exists()) {
                return true;
            }
            if ($protocol->reviewer_kelompok_id && $protocol->reviewer_kelompok_id === $authUser->reviewer_kelompok_id) {
                return true;
            }
        }

        return false;
    }

    private function canModifyProtocol(AuthUser $authUser, Protocol $protocol): bool
    {
        if ($authUser->hasRole(['super_admin', 'admin', 'sekertaris'])) {
            return true;
        }

        if ($authUser->hasRole(['reviewer', 'panel_reviewer', 'Ketua Reviewer'])) {
            return $this->canAccessProtocol($authUser, $protocol);
        }

        return $protocol->user_id === $authUser->id;
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Protocol') || $authUser->hasRole(['reviewer', 'panel_reviewer', 'Ketua Reviewer']);
    }

    public function view(AuthUser $authUser, Protocol $protocol): bool
    {
        return ($authUser->can('View:Protocol') || $authUser->hasRole(['reviewer', 'panel_reviewer', 'Ketua Reviewer']))
            && $this->canAccessProtocol($authUser, $protocol);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Protocol');
    }

    public function update(AuthUser $authUser, Protocol $protocol): bool
    {
        if ($authUser->hasRole(['reviewer', 'panel_reviewer', 'Ketua Reviewer'])) {
            return $this->canAccessProtocol($authUser, $protocol);
        }

        return $authUser->can('Update:Protocol') && $this->canModifyProtocol($authUser, $protocol);
    }

    public function delete(AuthUser $authUser, Protocol $protocol): bool
    {
        return $authUser->can('Delete:Protocol') && $this->canModifyProtocol($authUser, $protocol);
    }

    public function restore(AuthUser $authUser, Protocol $protocol): bool
    {
        return $authUser->can('Restore:Protocol') && $this->canModifyProtocol($authUser, $protocol);
    }

    public function forceDelete(AuthUser $authUser, Protocol $protocol): bool
    {
        return $authUser->can('ForceDelete:Protocol') && $this->canModifyProtocol($authUser, $protocol);
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
        return $authUser->can('Replicate:Protocol') && $this->canModifyProtocol($authUser, $protocol);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Protocol');
    }
}
