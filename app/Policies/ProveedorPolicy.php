<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Proveedor;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProveedorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProveedorResource');
    }

    public function view(AuthUser $authUser, Proveedor $proveedor): bool
    {
        return $authUser->can('View:ProveedorResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProveedorResource');
    }

    public function update(AuthUser $authUser, Proveedor $proveedor): bool
    {
        return $authUser->can('Update:ProveedorResource');
    }

    public function delete(AuthUser $authUser, Proveedor $proveedor): bool
    {
        return $authUser->can('Delete:ProveedorResource');
    }

    public function restore(AuthUser $authUser, Proveedor $proveedor): bool
    {
        return $authUser->can('Restore:ProveedorResource');
    }

    public function forceDelete(AuthUser $authUser, Proveedor $proveedor): bool
    {
        return $authUser->can('ForceDelete:ProveedorResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProveedorResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProveedorResource');
    }

    public function replicate(AuthUser $authUser, Proveedor $proveedor): bool
    {
        return $authUser->can('Replicate:ProveedorResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProveedorResource');
    }

}