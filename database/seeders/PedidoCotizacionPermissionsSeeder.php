<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PedidoCotizacionPermissionsSeeder extends Seeder
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

        // Permisos de PedidoCotizacionsResource
        $cotizacionPermissions = [
            'ViewAny:PedidoCotizacionResource',
            'View:PedidoCotizacionResource',
            'Create:PedidoCotizacionResource',
            'Update:PedidoCotizacionResource',
            'Delete:PedidoCotizacionResource',
            'Restore:PedidoCotizacionResource',
            'ForceDelete:PedidoCotizacionResource',
            'Replicate:PedidoCotizacionResource',
            'Reorder:PedidoCotizacionResource',
            'RestoreAny:PedidoCotizacionResource',
            'ForceDeleteAny:PedidoCotizacionResource',
        ];

        // Asignar permisos a Comercial
        foreach ($cotizacionPermissions as $permission) {
            $perm = Permission::where('name', $permission)->first();
            if ($perm) {
                $comercial->givePermissionTo($perm);
                $this->command->info("✓ Comercial: {$permission}");
            } else {
                $this->command->warn("⚠ Permiso no encontrado: {$permission}");
            }
        }

        // Asignar todos los permisos a Super Admin
        foreach ($cotizacionPermissions as $permission) {
            $perm = Permission::where('name', $permission)->first();
            if ($perm) {
                $superAdmin->givePermissionTo($perm);
                $this->command->info("✓ Super Admin: {$permission}");
            } else {
                $this->command->warn("⚠ Permiso no encontrado: {$permission}");
            }
        }
    }
}
