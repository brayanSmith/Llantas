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

        // Definir qué recursos puede acceder cada rol
        $comercialResources = [
            'PedidoResource',                      // Pedidos generales
            'PedidosEstadoPagoEnCarteraResource',  // Pedidos en cartera
            'PedidosPendientesResource',           // Pedidos pendientes
            'PedidoDomiciliarioResource',          // Comercial puede ver domiciliarios
        ];

        $superAdminOnlyResources = [
            'PedidosEstadoPagoSaldadoResource',    // Solo admin ve saldados
            'PedidosAnuladosResource',             // Solo admin ve anulados
            'PedidosFacturadosResource',           // Solo admin ve facturados
        ];

        $allPedidoResources = array_merge($comercialResources, $superAdminOnlyResources);

        // Permisos CRUD para cada recurso
        $permissions = [
            'ViewAny', 'View', 'Create', 'Update', 'Delete', 
            'Restore', 'ForceDelete', 'RestoreAny', 'ForceDeleteAny', 
            'Replicate', 'Reorder'
        ];

        // Asignar permisos a Comercial (recursos limitados)
        foreach ($comercialResources as $resource) {
            foreach ($permissions as $permission) {
                $permissionName = "{$permission}:{$resource}";
                $perm = Permission::where('name', $permissionName)->first();
                if ($perm) {
                    $comercial->givePermissionTo($perm);
                    $this->command->info("✓ Comercial: {$permissionName}");
                }
            }
        }

        // Asignar todos los permisos a Super Admin
        foreach ($allPedidoResources as $resource) {
            foreach ($permissions as $permission) {
                $permissionName = "{$permission}:{$resource}";
                $perm = Permission::where('name', $permissionName)->first();
                if ($perm) {
                    $superAdmin->givePermissionTo($perm);
                    $this->command->info("✓ SuperAdmin: {$permissionName}");
                }
            }
        }

        $this->command->info("\n🎉 Permisos de Pedidos configurados correctamente:");
        $this->command->info("👨‍💼 Comercial: Acceso a " . count($comercialResources) . " recursos de pedidos");
        $this->command->info("👑 SuperAdmin: Acceso a " . count($allPedidoResources) . " recursos de pedidos");
    }
}
