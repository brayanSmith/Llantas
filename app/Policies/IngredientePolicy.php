<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Ingrediente;
use Illuminate\Auth\Access\HandlesAuthorization;

class IngredientePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Ingrediente');
    }

    public function view(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('View:Ingrediente');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Ingrediente');
    }

    public function update(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('Update:Ingrediente');
    }

    public function delete(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('Delete:Ingrediente');
    }

    public function restore(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('Restore:Ingrediente');
    }

    public function forceDelete(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('ForceDelete:Ingrediente');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Ingrediente');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Ingrediente');
    }

    public function replicate(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('Replicate:Ingrediente');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Ingrediente');
    }

}