<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Bodega;
use Illuminate\Auth\Access\HandlesAuthorization;

class BodegaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BodegaResource');
    }

    public function view(AuthUser $authUser, Bodega $bodega): bool
    {
        return $authUser->can('View:BodegaResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BodegaResource');
    }

    public function update(AuthUser $authUser, Bodega $bodega): bool
    {
        return $authUser->can('Update:BodegaResource');
    }

    public function delete(AuthUser $authUser, Bodega $bodega): bool
    {
        return $authUser->can('Delete:BodegaResource');
    }

    public function restore(AuthUser $authUser, Bodega $bodega): bool
    {
        return $authUser->can('Restore:BodegaResource');
    }

    public function forceDelete(AuthUser $authUser, Bodega $bodega): bool
    {
        return $authUser->can('ForceDelete:BodegaResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BodegaResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BodegaResource');
    }

    public function replicate(AuthUser $authUser, Bodega $bodega): bool
    {
        return $authUser->can('Replicate:BodegaResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BodegaResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:BodegaResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:BodegaResource');
    }

}