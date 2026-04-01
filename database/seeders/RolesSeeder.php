<?php

namespace Database\Seeders;

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
        $roles = [
            'super_admin',
            'Comercial',
            'Comercial x Mayor',
            'Asesor Patio',
            'Consultor',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Asignar todos los permisos al super_admin si existen permisos
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $allPermissions = Permission::all();
        if ($allPermissions->count() > 0) {
            $superAdmin->syncPermissions($allPermissions);
            $this->command->info("Assigned {$allPermissions->count()} permissions to super_admin");
        }
    }
}
