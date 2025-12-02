<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Creando roles del sistema...');

        // Crear rol Comercial
        $comercial = Role::firstOrCreate(['name' => 'Comercial']);
        $this->command->info('✅ Rol Comercial creado/encontrado');

        // Crear rol super_admin
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $this->command->info('✅ Rol super_admin creado/encontrado');

        // Asignar TODOS los permisos al super_admin
        $allPermissions = Permission::all();
        if ($allPermissions->count() > 0) {
            $superAdmin->syncPermissions($allPermissions);
            $this->command->info("🔑 Se asignaron {$allPermissions->count()} permisos al rol super_admin");
        } else {
            $this->command->warn('⚠️ No se encontraron permisos para asignar. Ejecuta primero shield:generate');
        }

        $this->command->info('🎉 Roles configurados exitosamente:');
        $this->command->info("  - Comercial: Rol básico (sin permisos automáticos)");
        $this->command->info("  - super_admin: Todos los permisos del sistema");
    }
}
