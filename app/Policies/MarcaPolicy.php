<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Marca;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarcaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MarcaResource');
    }

    public function view(AuthUser $authUser, Marca $marca): bool
    {
        return $authUser->can('View:MarcaResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MarcaResource');
    }

    public function update(AuthUser $authUser, Marca $marca): bool
    {
        return $authUser->can('Update:MarcaResource');
    }

    public function delete(AuthUser $authUser, Marca $marca): bool
    {
        return $authUser->can('Delete:MarcaResource');
    }

    public function restore(AuthUser $authUser, Marca $marca): bool
    {
        return $authUser->can('Restore:MarcaResource');
    }

    public function forceDelete(AuthUser $authUser, Marca $marca): bool
    {
        return $authUser->can('ForceDelete:MarcaResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MarcaResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MarcaResource');
    }

    public function replicate(AuthUser $authUser, Marca $marca): bool
    {
        return $authUser->can('Replicate:MarcaResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MarcaResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:MarcaResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:MarcaResource');
    }

}