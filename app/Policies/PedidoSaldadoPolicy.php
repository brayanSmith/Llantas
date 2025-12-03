<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PedidoSaldadoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PedidosEstadoPagoSaldadoResource');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pedido $pedido): bool
    {
        return $user->can('View:PedidosEstadoPagoSaldadoResource');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:PedidosEstadoPagoSaldadoResource');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pedido $pedido): bool
    {
        return $user->can('Update:PedidosEstadoPagoSaldadoResource');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pedido $pedido): bool
    {
        return $user->can('Delete:PedidosEstadoPagoSaldadoResource');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pedido $pedido): bool
    {
        return $user->can('Restore:PedidosEstadoPagoSaldadoResource');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pedido $pedido): bool
    {
        return $user->can('ForceDelete:PedidosEstadoPagoSaldadoResource');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Pedido $pedido): bool
    {
        return $user->can('Replicate:PedidosEstadoPagoSaldadoResource');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PedidosEstadoPagoSaldadoResource');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PedidosEstadoPagoSaldadoResource');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PedidosEstadoPagoSaldadoResource');
    }
}