<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Compra;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompraPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CompraResource');
    }

    public function view(AuthUser $authUser, Compra $compra): bool
    {
        return $authUser->can('View:CompraResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CompraResource');
    }

    public function update(AuthUser $authUser, Compra $compra): bool
    {
        return $authUser->can('Update:CompraResource');
    }

    public function delete(AuthUser $authUser, Compra $compra): bool
    {
        return $authUser->can('Delete:CompraResource');
    }

    public function restore(AuthUser $authUser, Compra $compra): bool
    {
        return $authUser->can('Restore:CompraResource');
    }

    public function forceDelete(AuthUser $authUser, Compra $compra): bool
    {
        return $authUser->can('ForceDelete:CompraResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CompraResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CompraResource');
    }

    public function replicate(AuthUser $authUser, Compra $compra): bool
    {
        return $authUser->can('Replicate:CompraResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CompraResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:CompraResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:CompraResource');
    }

}