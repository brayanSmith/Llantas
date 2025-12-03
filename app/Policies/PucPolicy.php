<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Puc;
use Illuminate\Auth\Access\HandlesAuthorization;

class PucPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PucResource');
    }

    public function view(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('View:PucResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PucResource');
    }

    public function update(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('Update:PucResource');
    }

    public function delete(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('Delete:PucResource');
    }

    public function restore(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('Restore:PucResource');
    }

    public function forceDelete(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('ForceDelete:PucResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PucResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PucResource');
    }

    public function replicate(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('Replicate:PucResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PucResource');
    }

}