<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PedidoDomiciliarioPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PedidoDomiciliarioResource');
    }

    public function view(User $user, Pedido $pedido): bool
    {
        return $user->can('View:PedidoDomiciliarioResource');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:PedidoDomiciliarioResource');
    }

    public function update(User $user, Pedido $pedido): bool
    {
        return $user->can('Update:PedidoDomiciliarioResource'); 
    }

    public function delete(User $user, Pedido $pedido): bool
    {
        return $user->can('Delete:PedidoDomiciliarioResource');
    }

    public function restore(User $user, Pedido $pedido): bool
    {
        return $user->can('Restore:PedidoDomiciliarioResource');
    }

    public function forceDelete(User $user, Pedido $pedido): bool
    {
        return $user->can('ForceDelete:PedidoDomiciliarioResource');
    }

    public function replicate(User $user, Pedido $pedido): bool
    {
        return $user->can('Replicate:PedidoDomiciliarioResource');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PedidoDomiciliarioResource');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PedidoDomiciliarioResource');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PedidoDomiciliarioResource');
    }
}