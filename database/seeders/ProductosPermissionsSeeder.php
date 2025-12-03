<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProductosPermissionsSeeder extends Seeder
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

        // Definir recursos de productos por rol
        $comercialResources = [
            'CatalogoResource',    // Comercial puede ver catálogo
            'ProductoResource',    // Comercial puede gestionar productos
        ];

        $superAdminOnlyResources = [
            // Recursos que solo super admin puede usar
        ];

        $allResources = array_merge($comercialResources, $superAdminOnlyResources);

        // Asignar permisos de productos a Comercial
        foreach ($comercialResources as $resource) {
            $permissions = [
                "ViewAny:{$resource}",
                "View:{$resource}",
                "Create:{$resource}",
                "Update:{$resource}",
                "Delete:{$resource}",
                "Restore:{$resource}",
                "ForceDelete:{$resource}",
                "ForceDeleteAny:{$resource}",
                "RestoreAny:{$resource}",
                "Replicate:{$resource}",
                "Reorder:{$resource}",
            ];

            foreach ($permissions as $permission) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $comercial->givePermissionTo($perm);
                    $this->command->info("✓ Comercial: {$permission}");
                } else {
                    $this->command->warn("⚠ Permiso no encontrado: {$permission}");
                }
            }
        }

        // Asignar todos los permisos de productos a Super Admin
        foreach ($allResources as $resource) {
            $permissions = [
                "ViewAny:{$resource}",
                "View:{$resource}",
                "Create:{$resource}",
                "Update:{$resource}",
                "Delete:{$resource}",
                "Restore:{$resource}",
                "ForceDelete:{$resource}",
                "ForceDeleteAny:{$resource}",
                "RestoreAny:{$resource}",
                "Replicate:{$resource}",
                "Reorder:{$resource}",
            ];

            foreach ($permissions as $permission) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $superAdmin->givePermissionTo($perm);
                    $this->command->info("✓ SuperAdmin: {$permission}");
                }
            }
        }

        $this->command->info("\n🎉 Permisos de Productos configurados correctamente:");
        $this->command->info("📋 Comercial: Acceso a Catálogo y Gestión de Productos");
        $this->command->info("👑 SuperAdmin: Acceso completo a todos los recursos de productos");
    }
}
