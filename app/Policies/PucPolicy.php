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
        return $authUser->can('ViewAny:Puc');
    }

    public function view(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('View:Puc');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Puc');
    }

    public function update(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('Update:Puc');
    }

    public function delete(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('Delete:Puc');
    }

    public function restore(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('Restore:Puc');
    }

    public function forceDelete(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('ForceDelete:Puc');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Puc');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Puc');
    }

    public function replicate(AuthUser $authUser, Puc $puc): bool
    {
        return $authUser->can('Replicate:Puc');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Puc');
    }

}