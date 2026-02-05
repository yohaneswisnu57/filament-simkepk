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
        // return $authUser->can('ViewAny:Protocol');

        return $authUser->hasRole(['admin', 'super_admin', 'sekertaris', 'reviewer', 'user']);
    }

    public function view(AuthUser $authUser, Protocol $protocol): bool
    {
        // return $authUser->can('View:Protocol');

        // 1. Admin & Super Admin selalu boleh
        if ($authUser->hasRole(['admin', 'super_admin', 'sekertaris', 'reviewer', 'user'])) {
            return true;
        }

        // 2. Pemilik data (Peneliti) boleh melihat miliknya sendiri
        if ($authUser->id === $protocol->user_id) {
            return true;
        }

        // 3. Reviewer boleh melihat JIKA satu kelompok dengan protokol
        if ($authUser->hasRole('reviewer')) {
            // Pastikan reviewer punya kelompok & kelompoknya sama dengan protokol
            return $authUser->reviewer_kelompok_id == $protocol->reviewer_kelompok_id;
        }

        return false;
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
