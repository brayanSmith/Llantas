<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Ruta;
use Illuminate\Auth\Access\HandlesAuthorization;

class RutaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RutaResource');
    }

    public function view(AuthUser $authUser, Ruta $ruta): bool
    {
        return $authUser->can('View:RutaResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RutaResource');
    }

    public function update(AuthUser $authUser, Ruta $ruta): bool
    {
        return $authUser->can('Update:RutaResource');
    }

    public function delete(AuthUser $authUser, Ruta $ruta): bool
    {
        return $authUser->can('Delete:RutaResource');
    }

    public function restore(AuthUser $authUser, Ruta $ruta): bool
    {
        return $authUser->can('Restore:RutaResource');
    }

    public function forceDelete(AuthUser $authUser, Ruta $ruta): bool
    {
        return $authUser->can('ForceDelete:RutaResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RutaResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RutaResource');
    }

    public function replicate(AuthUser $authUser, Ruta $ruta): bool
    {
        return $authUser->can('Replicate:RutaResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RutaResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:RutaResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:RutaResource');
    }

}