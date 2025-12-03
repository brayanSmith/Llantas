<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PedidoCarteraPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PedidosEstadoPagoEnCarteraResource');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pedido $pedido): bool
    {
        return $user->can('View:PedidosEstadoPagoEnCarteraResource');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:PedidosEstadoPagoEnCarteraResource');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pedido $pedido): bool
    {
        return $user->can('Update:PedidosEstadoPagoEnCarteraResource');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pedido $pedido): bool
    {
        return $user->can('Delete:PedidosEstadoPagoEnCarteraResource');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pedido $pedido): bool
    {
        return $user->can('Restore:PedidosEstadoPagoEnCarteraResource');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pedido $pedido): bool
    {
        return $user->can('ForceDelete:PedidosEstadoPagoEnCarteraResource');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Pedido $pedido): bool
    {
        return $user->can('Replicate:PedidosEstadoPagoEnCarteraResource');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PedidosEstadoPagoEnCarteraResource');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PedidosEstadoPagoEnCarteraResource');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PedidosEstadoPagoEnCarteraResource');
    }
}