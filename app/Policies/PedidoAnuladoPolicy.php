<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PedidoAnuladoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PedidosAnuladosResource');
    }

    public function view(User $user, Pedido $pedido): bool
    {
        return $user->can('View:PedidosAnuladosResource');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:PedidosAnuladosResource');
    }

    public function update(User $user, Pedido $pedido): bool
    {
        return $user->can('Update:PedidosAnuladosResource');
    }

    public function delete(User $user, Pedido $pedido): bool
    {
        return $user->can('Delete:PedidosAnuladosResource');
    }

    public function restore(User $user, Pedido $pedido): bool
    {
        return $user->can('Restore:PedidosAnuladosResource');
    }

    public function forceDelete(User $user, Pedido $pedido): bool
    {
        return $user->can('ForceDelete:PedidosAnuladosResource');
    }

    public function replicate(User $user, Pedido $pedido): bool
    {
        return $user->can('Replicate:PedidosAnuladosResource');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PedidosAnuladosResource');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PedidosAnuladosResource');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PedidosAnuladosResource');
    }
}