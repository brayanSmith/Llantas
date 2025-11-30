<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PedidoGeneralPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PedidoResource');
    }

    public function view(User $user, Pedido $pedido): bool
    {
        return $user->can('View:PedidoResource');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:PedidoResource');
    }

    public function update(User $user, Pedido $pedido): bool
    {
        return $user->can('Update:PedidoResource');
    }

    public function delete(User $user, Pedido $pedido): bool
    {
        return $user->can('Delete:PedidoResource');
    }

    public function restore(User $user, Pedido $pedido): bool
    {
        return $user->can('Restore:PedidoResource');
    }

    public function forceDelete(User $user, Pedido $pedido): bool
    {
        return $user->can('ForceDelete:PedidoResource');
    }

    public function replicate(User $user, Pedido $pedido): bool
    {
        return $user->can('Replicate:PedidoResource');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PedidoResource');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PedidoResource');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PedidoResource');
    }
}