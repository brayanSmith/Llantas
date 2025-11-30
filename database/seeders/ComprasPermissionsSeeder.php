<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ComprasPermissionsSeeder extends Seeder
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

        // Definir qué recursos de compras puede acceder cada rol
        $comercialComprasResources = [
            'CompraResource',                    // Compras generales  
            'ComprasPendientesResource',         // Compras pendientes
        ];

        $superAdminOnlyComprasResources = [
            'ComprasEstadoEnCarteraResource',    // Solo admin ve en cartera
            'ComprasFacturadasResource',         // Solo admin ve facturadas
            'ComprasEstadoPagadoResource',       // Solo admin ve pagadas
            'ComprasAnuladasResource',           // Solo admin ve anuladas
        ];

        $allComprasResources = array_merge($comercialComprasResources, $superAdminOnlyComprasResources);

        // Permisos CRUD para cada recurso
        $permissions = [
            'ViewAny', 'View', 'Create', 'Update', 'Delete', 
            'Restore', 'ForceDelete', 'RestoreAny', 'ForceDeleteAny', 
            'Replicate', 'Reorder'
        ];

        // Asignar permisos a Comercial (recursos limitados de compras)
        foreach ($comercialComprasResources as $resource) {
            foreach ($permissions as $permission) {
                $permissionName = "{$permission}:{$resource}";
                $perm = Permission::where('name', $permissionName)->first();
                if ($perm) {
                    $comercial->givePermissionTo($perm);
                    $this->command->info("✓ Comercial: {$permissionName}");
                }
            }
        }

        // Asignar todos los permisos de compras a Super Admin
        foreach ($allComprasResources as $resource) {
            foreach ($permissions as $permission) {
                $permissionName = "{$permission}:{$resource}";
                $perm = Permission::where('name', $permissionName)->first();
                if ($perm) {
                    $superAdmin->givePermissionTo($perm);
                    $this->command->info("✓ SuperAdmin: {$permissionName}");
                }
            }
        }

        $this->command->info("\n🎉 Permisos de Compras configurados correctamente:");
        $this->command->info("👨‍💼 Comercial: Acceso a " . count($comercialComprasResources) . " recursos de compras");
        $this->command->info("👑 SuperAdmin: Acceso a " . count($allComprasResources) . " recursos de compras");
    }
}
