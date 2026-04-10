<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DetallePedido;
use Illuminate\Auth\Access\HandlesAuthorization;

class DetallePedidoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DetallePedidoResource');
    }

    public function view(AuthUser $authUser, DetallePedido $detallePedido): bool
    {
        return $authUser->can('View:DetallePedidoResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DetallePedidoResource');
    }

    public function update(AuthUser $authUser, DetallePedido $detallePedido): bool
    {
        return $authUser->can('Update:DetallePedidoResource');
    }

    public function delete(AuthUser $authUser, DetallePedido $detallePedido): bool
    {
        return $authUser->can('Delete:DetallePedidoResource');
    }

    public function restore(AuthUser $authUser, DetallePedido $detallePedido): bool
    {
        return $authUser->can('Restore:DetallePedidoResource');
    }

    public function forceDelete(AuthUser $authUser, DetallePedido $detallePedido): bool
    {
        return $authUser->can('ForceDelete:DetallePedidoResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DetallePedidoResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DetallePedidoResource');
    }

    public function replicate(AuthUser $authUser, DetallePedido $detallePedido): bool
    {
        return $authUser->can('Replicate:DetallePedidoResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DetallePedidoResource');
    }

    public function import(AuthUser $authUser): bool
    {
        return $authUser->can('Import:DetallePedidoResource');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:DetallePedidoResource');
    }

}