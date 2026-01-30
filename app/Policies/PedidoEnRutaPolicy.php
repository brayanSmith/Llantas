<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PedidoEnRutaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PedidoEnRutaResource');
    }

    public function view(User $user, Pedido $pedido): bool
    {
        return $user->can('View:PedidoEnRutaResource');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:PedidoEnRutaResource');
    }

    public function update(User $user, Pedido $pedido): bool
    {
        return $user->can('Update:PedidoEnRutaResource');
    }

    public function delete(User $user, Pedido $pedido): bool
    {
        return $user->can('Delete:PedidoEnRutaResource');
    }

    public function restore(User $user, Pedido $pedido): bool
    {
        return $user->can('Restore:PedidoEnRutaResource');
    }

    public function forceDelete(User $user, Pedido $pedido): bool
    {
        return $user->can('ForceDelete:PedidoEnRutaResource');
    }

    public function replicate(User $user, Pedido $pedido): bool
    {
        return $user->can('Replicate:PedidoEnRutaResource');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PedidoEnRutaResource');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PedidoEnRutaResource');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PedidoEnRutaResource');
    }
}
