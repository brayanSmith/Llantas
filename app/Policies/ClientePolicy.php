<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Cliente;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ClienteResource');
    }

    public function view(AuthUser $authUser, Cliente $cliente): bool
    {
        return $authUser->can('View:ClienteResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ClienteResource');
    }

    public function update(AuthUser $authUser, Cliente $cliente): bool
    {
        return $authUser->can('Update:ClienteResource');
    }

    public function delete(AuthUser $authUser, Cliente $cliente): bool
    {
        return $authUser->can('Delete:ClienteResource');
    }

    public function restore(AuthUser $authUser, Cliente $cliente): bool
    {
        return $authUser->can('Restore:ClienteResource');
    }

    public function forceDelete(AuthUser $authUser, Cliente $cliente): bool
    {
        return $authUser->can('ForceDelete:ClienteResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ClienteResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ClienteResource');
    }

    public function replicate(AuthUser $authUser, Cliente $cliente): bool
    {
        return $authUser->can('Replicate:ClienteResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ClienteResource');
    }

}