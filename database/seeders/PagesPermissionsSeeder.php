<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PagesPermissionsSeeder extends Seeder
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

        // Definir permisos de páginas por rol (usando formato Shield automático)
        $comercialPages = [
            'view_App\Filament\Pages\Pos',          // Permiso generado automáticamente por Shield
        ];

        $superAdminOnlyPages = [
            // Páginas que solo super admin puede ver
            // Puedes agregar más páginas aquí según necesites
        ];

        $allPages = array_merge($comercialPages, $superAdminOnlyPages);

        // Asignar permisos de páginas a Comercial
        foreach ($comercialPages as $permission) {
            $perm = Permission::where('name', $permission)->first();
            if ($perm) {
                $comercial->givePermissionTo($perm);
                $this->command->info("✓ Comercial: {$permission}");
            } else {
                $this->command->warn("⚠ Permiso no encontrado: {$permission}");
            }
        }

        // Asignar todos los permisos de páginas a Super Admin
        foreach ($allPages as $permission) {
            $perm = Permission::where('name', $permission)->first();
            if ($perm) {
                $superAdmin->givePermissionTo($perm);
                $this->command->info("✓ SuperAdmin: {$permission}");
            }
        }

        $this->command->info("\n🎉 Permisos de Páginas configurados correctamente:");
        $this->command->info("👨‍💼 Comercial: Acceso a " . count($comercialPages) . " páginas");
        $this->command->info("👑 SuperAdmin: Acceso a " . count($allPages) . " páginas");
    }
}
