<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PedidoCotizacionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PedidoCotizacionResource');
    }

    public function view(User $user, Pedido $pedido): bool
    {
        return $user->can('View:PedidoCotizacionResource');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:PedidoCotizacionResource');
    }

    public function update(User $user, Pedido $pedido): bool
    {
        return $user->can('Update:PedidoCotizacionResource');
    }

    public function delete(User $user, Pedido $pedido): bool
    {
        return $user->can('Delete:PedidoCotizacionResource');
    }

    public function restore(User $user, Pedido $pedido): bool
    {
        return $user->can('Restore:PedidoCotizacionResource');
    }

    public function forceDelete(User $user, Pedido $pedido): bool
    {
        return $user->can('ForceDelete:PedidoCotizacionResource');
    }

    public function replicate(User $user, Pedido $pedido): bool
    {
        return $user->can('Replicate:PedidoCotizacionResource');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PedidoCotizacionResource');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PedidoCotizacionResource');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PedidoCotizacionResource');
    }
}
