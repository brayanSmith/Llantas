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
        return $authUser->can('ViewAny:Medida');
    }

    public function view(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('View:Medida');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Medida');
    }

    public function update(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('Update:Medida');
    }

    public function delete(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('Delete:Medida');
    }

    public function restore(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('Restore:Medida');
    }

    public function forceDelete(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('ForceDelete:Medida');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Medida');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Medida');
    }

    public function replicate(AuthUser $authUser, Medida $medida): bool
    {
        return $authUser->can('Replicate:Medida');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Medida');
    }

}