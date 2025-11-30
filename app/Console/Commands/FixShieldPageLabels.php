<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FixShieldPageLabels extends Command
{
    protected $signature = 'shield:fix-page-labels';
    protected $description = 'Fix page labels in Shield interface';

    public function handle()
    {
        $this->info('🔧 Corrigiendo etiquetas de páginas en Shield...');

        // Crear el permiso con el formato que Shield espera para mostrar labels
        $permissions = [
            'view_pos' => 'POS - Punto de Venta',
            'View:Pos' => 'POS - Punto de Venta',
        ];

        foreach ($permissions as $permissionName => $label) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);

            $this->info("✓ Permiso configurado: {$permissionName}");
        }

        // Asegurar que los roles tengan estos permisos
        $comercial = Role::where('name', 'Comercial')->first();
        $superAdmin = Role::where('name', 'super_admin')->first();

        if ($comercial && $superAdmin) {
            foreach (array_keys($permissions) as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission) {
                    $comercial->givePermissionTo($permission);
                    $superAdmin->givePermissionTo($permission);
                }
            }
            $this->info('✓ Permisos asignados a roles');
        }

        $this->info('🎉 Etiquetas de páginas corregidas');
        $this->info('💡 Refresca la página de Shield para ver los cambios');
        
        return 0;
    }
}