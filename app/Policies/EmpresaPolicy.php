<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Empresa;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmpresaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EmpresaResource');
    }

    public function view(AuthUser $authUser, Empresa $empresa): bool
    {
        return $authUser->can('View:EmpresaResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EmpresaResource');
    }

    public function update(AuthUser $authUser, Empresa $empresa): bool
    {
        return $authUser->can('Update:EmpresaResource');
    }

    public function delete(AuthUser $authUser, Empresa $empresa): bool
    {
        return $authUser->can('Delete:EmpresaResource');
    }

    public function restore(AuthUser $authUser, Empresa $empresa): bool
    {
        return $authUser->can('Restore:EmpresaResource');
    }

    public function forceDelete(AuthUser $authUser, Empresa $empresa): bool
    {
        return $authUser->can('ForceDelete:EmpresaResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EmpresaResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EmpresaResource');
    }

    public function replicate(AuthUser $authUser, Empresa $empresa): bool
    {
        return $authUser->can('Replicate:EmpresaResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EmpresaResource');
    }

}