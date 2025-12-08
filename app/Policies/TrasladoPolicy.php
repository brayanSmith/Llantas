<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Traslado;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrasladoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TrasladoResource');
    }

    public function view(AuthUser $authUser, Traslado $traslado): bool
    {
        return $authUser->can('View:TrasladoResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TrasladoResource');
    }

    public function update(AuthUser $authUser, Traslado $traslado): bool
    {
        return $authUser->can('Update:TrasladoResource');
    }

    public function delete(AuthUser $authUser, Traslado $traslado): bool
    {
        return $authUser->can('Delete:TrasladoResource');
    }

    public function restore(AuthUser $authUser, Traslado $traslado): bool
    {
        return $authUser->can('Restore:TrasladoResource');
    }

    public function forceDelete(AuthUser $authUser, Traslado $traslado): bool
    {
        return $authUser->can('ForceDelete:TrasladoResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TrasladoResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TrasladoResource');
    }

    public function replicate(AuthUser $authUser, Traslado $traslado): bool
    {
        return $authUser->can('Replicate:TrasladoResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TrasladoResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:TrasladoResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:TrasladoResource');
    }

}