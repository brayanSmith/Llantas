<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Producto;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductoResource');
    }

    public function view(AuthUser $authUser, Producto $producto): bool
    {
        return $authUser->can('View:ProductoResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductoResource');
    }

    public function update(AuthUser $authUser, Producto $producto): bool
    {
        return $authUser->can('Update:ProductoResource');
    }

    public function delete(AuthUser $authUser, Producto $producto): bool
    {
        return $authUser->can('Delete:ProductoResource');
    }

    public function restore(AuthUser $authUser, Producto $producto): bool
    {
        return $authUser->can('Restore:ProductoResource');
    }

    public function forceDelete(AuthUser $authUser, Producto $producto): bool
    {
        return $authUser->can('ForceDelete:ProductoResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProductoResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProductoResource');
    }

    public function replicate(AuthUser $authUser, Producto $producto): bool
    {
        return $authUser->can('Replicate:ProductoResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductoResource');
    }

}