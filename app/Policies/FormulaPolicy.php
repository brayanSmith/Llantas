<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Formula;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormulaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FormulaResource');
    }

    public function view(AuthUser $authUser, Formula $formula): bool
    {
        return $authUser->can('View:FormulaResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FormulaResource');
    }

    public function update(AuthUser $authUser, Formula $formula): bool
    {
        return $authUser->can('Update:FormulaResource');
    }

    public function delete(AuthUser $authUser, Formula $formula): bool
    {
        return $authUser->can('Delete:FormulaResource');
    }

    public function restore(AuthUser $authUser, Formula $formula): bool
    {
        return $authUser->can('Restore:FormulaResource');
    }

    public function forceDelete(AuthUser $authUser, Formula $formula): bool
    {
        return $authUser->can('ForceDelete:FormulaResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FormulaResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FormulaResource');
    }

    public function replicate(AuthUser $authUser, Formula $formula): bool
    {
        return $authUser->can('Replicate:FormulaResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FormulaResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:FormulaResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:FormulaResource');
    }

}