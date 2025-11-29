<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PedidosPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles existentes
        $comercial = Role::where('name', 'Comercial')->first();
        $superAdmin = Role::where('name', 'super_admin')->first();

        if (!$comercial || !$superAdmin) {
            $this->command->error('Los roles Comercial o super_admin no existen.');
            return;
        }

        // Permisos básicos de Pedido (ya asignados)
        $pedidoPermissions = [
            'view_any::pedido',
            'view::pedido', 
            'create::pedido',
            'update::pedido',
            'delete::pedido',
            'restore::pedido',
            'force_delete::pedido',
            'restore_any::pedido',
            'force_delete_any::pedido',
            'replicate::pedido',
            'reorder::pedido',
        ];

        // Permisos específicos para Pedidos En Cartera
        $pedidosCarteraPermissions = [
            'view_any:_pedidos_en_cartera',
            'view:_pedidos_en_cartera',
            'create:_pedidos_en_cartera',
            'update:_pedidos_en_cartera',
            'delete:_pedidos_en_cartera',
            'restore:_pedidos_en_cartera',
            'force_delete:_pedidos_en_cartera',
            'restore_any:_pedidos_en_cartera',
            'force_delete_any:_pedidos_en_cartera',
            'replicate:_pedidos_en_cartera',
            'reorder:_pedidos_en_cartera',
        ];

        // Permisos específicos para Pedidos Saldados
        $pedidosSaldadosPermissions = [
            'view_any:_pedidos_saldados',
            'view:_pedidos_saldados',
            'create:_pedidos_saldados',
            'update:_pedidos_saldados',
            'delete:_pedidos_saldados',
            'restore:_pedidos_saldados',
            'force_delete:_pedidos_saldados',
            'restore_any:_pedidos_saldados',
            'force_delete_any:_pedidos_saldados',
            'replicate:_pedidos_saldados',
            'reorder:_pedidos_saldados',
        ];

        // Asignar permisos al rol Comercial
        // Solo pueden ver/gestionar pedidos generales y en cartera, NO saldados
        foreach (array_merge($pedidoPermissions, $pedidosCarteraPermissions) as $permission) {
            $perm = Permission::where('name', $permission)->first();
            if ($perm) {
                $comercial->givePermissionTo($perm);
                $this->command->info("Permiso '{$permission}' asignado a Comercial");
            }
        }

        // Super Admin tiene todos los permisos
        foreach (array_merge($pedidoPermissions, $pedidosCarteraPermissions, $pedidosSaldadosPermissions) as $permission) {
            $perm = Permission::where('name', $permission)->first();
            if ($perm) {
                $superAdmin->givePermissionTo($perm);
                $this->command->info("Permiso '{$permission}' asignado a super_admin");
            }
        }

        $this->command->info('Permisos de Pedidos configurados correctamente.');
    }
}
