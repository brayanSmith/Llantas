<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Ruta;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pedido;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\DetallePedido;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
        MarcaSeeder::class,
        AdminUserSeeder::class,
        RolesSeeder::class,  // Crear roles primero
        BodegaSeeder::class,
        CategoriaSeeder::class,
        ProveedorSeeder::class,
        EmpresaSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

            $role = Role::firstOrCreate(['name' => 'super_admin']);

    // Darle todos los permisos automáticamente
    $role->syncPermissions(\Spatie\Permission\Models\Permission::all());

    // Asignarlo al usuario 1 o al usuario que quieras
    $user = \App\Models\User::first();
    if ($user) {
        $user->assignRole($role);
    }
    }
}
