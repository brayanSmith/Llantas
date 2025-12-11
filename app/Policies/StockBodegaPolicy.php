<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StockBodega;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockBodegaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockBodegaResource');
    }

    public function view(AuthUser $authUser, StockBodega $stockBodega): bool
    {
        return $authUser->can('View:StockBodegaResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockBodegaResource');
    }

    public function update(AuthUser $authUser, StockBodega $stockBodega): bool
    {
        return $authUser->can('Update:StockBodegaResource');
    }

    public function delete(AuthUser $authUser, StockBodega $stockBodega): bool
    {
        return $authUser->can('Delete:StockBodegaResource');
    }

    public function restore(AuthUser $authUser, StockBodega $stockBodega): bool
    {
        return $authUser->can('Restore:StockBodegaResource');
    }

    public function forceDelete(AuthUser $authUser, StockBodega $stockBodega): bool
    {
        return $authUser->can('ForceDelete:StockBodegaResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StockBodegaResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StockBodegaResource');
    }

    public function replicate(AuthUser $authUser, StockBodega $stockBodega): bool
    {
        return $authUser->can('Replicate:StockBodegaResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StockBodegaResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:StockBodegaResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:StockBodegaResource');
    }

}