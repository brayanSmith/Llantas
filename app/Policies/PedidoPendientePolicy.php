<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PedidoPendientePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PedidosPendientesResource');
    }

    public function view(User $user, Pedido $pedido): bool
    {
        return $user->can('View:PedidosPendientesResource');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:PedidosPendientesResource');
    }

    public function update(User $user, Pedido $pedido): bool
    {
        return $user->can('Update:PedidosPendientesResource');
    }

    public function delete(User $user, Pedido $pedido): bool
    {
        return $user->can('Delete:PedidosPendientesResource');
    }

    public function restore(User $user, Pedido $pedido): bool
    {
        return $user->can('Restore:PedidosPendientesResource');
    }

    public function forceDelete(User $user, Pedido $pedido): bool
    {
        return $user->can('ForceDelete:PedidosPendientesResource');
    }

    public function replicate(User $user, Pedido $pedido): bool
    {
        return $user->can('Replicate:PedidosPendientesResource');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PedidosPendientesResource');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PedidosPendientesResource');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PedidosPendientesResource');
    }
}