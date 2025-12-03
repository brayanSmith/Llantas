<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PedidoFacturadoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PedidosFacturadosResource');
    }

    public function view(User $user, Pedido $pedido): bool
    {
        return $user->can('View:PedidosFacturadosResource');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:PedidosFacturadosResource');
    }

    public function update(User $user, Pedido $pedido): bool
    {
        return $user->can('Update:PedidosFacturadosResource');
    }

    public function delete(User $user, Pedido $pedido): bool
    {
        return $user->can('Delete:PedidosFacturadosResource');
    }

    public function restore(User $user, Pedido $pedido): bool
    {
        return $user->can('Restore:PedidosFacturadosResource');
    }

    public function forceDelete(User $user, Pedido $pedido): bool
    {
        return $user->can('ForceDelete:PedidosFacturadosResource');
    }

    public function replicate(User $user, Pedido $pedido): bool
    {
        return $user->can('Replicate:PedidosFacturadosResource');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PedidosFacturadosResource');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PedidosFacturadosResource');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PedidosFacturadosResource');
    }
}