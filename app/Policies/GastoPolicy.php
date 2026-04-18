<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Gasto;
use Illuminate\Auth\Access\HandlesAuthorization;

class GastoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GastoResource');
    }

    public function view(AuthUser $authUser, Gasto $gasto): bool
    {
        return $authUser->can('View:GastoResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GastoResource');
    }

    public function update(AuthUser $authUser, Gasto $gasto): bool
    {
        return $authUser->can('Update:GastoResource');
    }

    public function delete(AuthUser $authUser, Gasto $gasto): bool
    {
        return $authUser->can('Delete:GastoResource');
    }

    public function restore(AuthUser $authUser, Gasto $gasto): bool
    {
        return $authUser->can('Restore:GastoResource');
    }

    public function forceDelete(AuthUser $authUser, Gasto $gasto): bool
    {
        return $authUser->can('ForceDelete:GastoResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GastoResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GastoResource');
    }

    public function replicate(AuthUser $authUser, Gasto $gasto): bool
    {
        return $authUser->can('Replicate:GastoResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GastoResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:GastoResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:GastoResource');
    }

}