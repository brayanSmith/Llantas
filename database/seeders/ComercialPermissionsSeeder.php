<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ComercialPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Creando permisos para el rol Comercial...');

        // Lista de permisos de ejemplo para el área Comercial
        $permissions = [
            'pedidos.view',
            'pedidos.create',
            'pedidos.update',
            'pedidos.delete',
            'clientes.view',
            'clientes.create',
            'clientes.update',
            'productos.view',
            'productos.search',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Asignar permisos al rol Comercial
        $role = Role::firstOrCreate(['name' => 'Comercial']);
        $role->syncPermissions($permissions);

        $this->command->info('✅ Permisos asignados a Comercial: ' . implode(', ', $permissions));
    }
}
