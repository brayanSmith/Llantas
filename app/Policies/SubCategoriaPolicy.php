<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SubCategoria;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubCategoriaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SubCategoriaResource');
    }

    public function view(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('View:SubCategoriaResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SubCategoriaResource');
    }

    public function update(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('Update:SubCategoriaResource');
    }

    public function delete(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('Delete:SubCategoriaResource');
    }

    public function restore(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('Restore:SubCategoriaResource');
    }

    public function forceDelete(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('ForceDelete:SubCategoriaResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SubCategoriaResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SubCategoriaResource');
    }

    public function replicate(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('Replicate:SubCategoriaResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SubCategoriaResource');
    }

}