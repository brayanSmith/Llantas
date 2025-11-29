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
        return $authUser->can('ViewAny:SubCategoria');
    }

    public function view(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('View:SubCategoria');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SubCategoria');
    }

    public function update(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('Update:SubCategoria');
    }

    public function delete(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('Delete:SubCategoria');
    }

    public function restore(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('Restore:SubCategoria');
    }

    public function forceDelete(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('ForceDelete:SubCategoria');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SubCategoria');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SubCategoria');
    }

    public function replicate(AuthUser $authUser, SubCategoria $subCategoria): bool
    {
        return $authUser->can('Replicate:SubCategoria');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SubCategoria');
    }

}