<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Medida;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedidaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MedidaResource');
    }

    public function view(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('View:MedidaResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MedidaResource');
    }

    public function update(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('Update:MedidaResource');
    }

    public function delete(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('Delete:MedidaResource');
    }

    public function restore(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('Restore:MedidaResource');
    }

    public function forceDelete(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('ForceDelete:MedidaResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MedidaResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MedidaResource');
    }

    public function replicate(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('Replicate:MedidaResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MedidaResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:MedidaResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:MedidaResource');
    }

}