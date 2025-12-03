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
        return $authUser->can('ViewAny:ProduccionResource');
    }

    public function view(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('View:ProduccionResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProduccionResource');
    }

    public function update(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('Update:ProduccionResource');
    }

    public function delete(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('Delete:ProduccionResource');
    }

    public function restore(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('Restore:ProduccionResource');
    }

    public function forceDelete(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('ForceDelete:ProduccionResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProduccionResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProduccionResource');
    }

    public function replicate(AuthUser $authUser, Produccion $produccion): bool
    {
        return $authUser->can('Replicate:ProduccionResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProduccionResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:ProduccionResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:ProduccionResource');
    }

}