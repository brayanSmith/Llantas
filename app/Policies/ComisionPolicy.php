<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Comision;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComisionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ComisionResource');
    }

    public function view(AuthUser $authUser, Comision $comision): bool
    {
        return $authUser->can('View:ComisionResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ComisionResource');
    }

    public function update(AuthUser $authUser, Comision $comision): bool
    {
        return $authUser->can('Update:ComisionResource');
    }

    public function delete(AuthUser $authUser, Comision $comision): bool
    {
        return $authUser->can('Delete:ComisionResource');
    }

    public function restore(AuthUser $authUser, Comision $comision): bool
    {
        return $authUser->can('Restore:ComisionResource');
    }

    public function forceDelete(AuthUser $authUser, Comision $comision): bool
    {
        return $authUser->can('ForceDelete:ComisionResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ComisionResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ComisionResource');
    }

    public function replicate(AuthUser $authUser, Comision $comision): bool
    {
        return $authUser->can('Replicate:ComisionResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ComisionResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:ComisionResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:ComisionResource');
    }

}