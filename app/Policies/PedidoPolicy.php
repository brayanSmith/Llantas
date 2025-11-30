<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Pedido;
use Illuminate\Auth\Access\HandlesAuthorization;

class PedidoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PedidoResource');
    }

    public function view(AuthUser $authUser, Pedido $pedido): bool
    {
        return $authUser->can('View:PedidoResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PedidoResource');
    }

    public function update(AuthUser $authUser, Pedido $pedido): bool
    {
        return $authUser->can('Update:PedidoResource');
    }

    public function delete(AuthUser $authUser, Pedido $pedido): bool
    {
        return $authUser->can('Delete:PedidoResource');
    }

    public function restore(AuthUser $authUser, Pedido $pedido): bool
    {
        return $authUser->can('Restore:PedidoResource');
    }

    public function forceDelete(AuthUser $authUser, Pedido $pedido): bool
    {
        return $authUser->can('ForceDelete:PedidoResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PedidoResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PedidoResource');
    }

    public function replicate(AuthUser $authUser, Pedido $pedido): bool
    {
        return $authUser->can('Replicate:PedidoResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PedidoResource');
    }

}