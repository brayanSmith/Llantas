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
        return $authUser->can('ViewAny:IngredienteResource');
    }

    public function view(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('View:IngredienteResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:IngredienteResource');
    }

    public function update(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('Update:IngredienteResource');
    }

    public function delete(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('Delete:IngredienteResource');
    }

    public function restore(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('Restore:IngredienteResource');
    }

    public function forceDelete(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('ForceDelete:IngredienteResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:IngredienteResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:IngredienteResource');
    }

    public function replicate(AuthUser $authUser, Ingrediente $ingrediente): bool
    {
        return $authUser->can('Replicate:IngredienteResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:IngredienteResource');
    }

}