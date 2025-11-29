<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Produccion;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProduccionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Produccion');
    }

    public function view(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('View:Produccion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Produccion');
    }

    public function update(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('Update:Produccion');
    }

    public function delete(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('Delete:Produccion');
    }

    public function restore(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('Restore:Produccion');
    }

    public function forceDelete(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('ForceDelete:Produccion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Produccion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Produccion');
    }

    public function replicate(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('Replicate:Produccion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Produccion');
    }

}