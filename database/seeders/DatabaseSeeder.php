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
        AdminUserSeeder::class,
        RolesSeeder::class,  // Crear roles primero
        //ComercialPermissionsSeeder::class, // Permisos para Comercial
        BodegaSeeder::class,
        CategoriaSeeder::class,
        SubCategoriaSeeder::class,
        MedidaSeeder::class,
        RutaSeeder::class,
        ProveedorSeeder::class,
        EmpresaSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        //Producto::factory(10)->create();
        //Cliente::factory(50)->create();

        /*Pedido::factory()
            ->count(20)
            ->has(
                DetallePedido::factory()->count(10),
                'detalles'
            )
            ->create()
            ->each(function ($pedido) {
                // Recalcular subtotal del pedido según sus detalles
                $pedido->update([
                    'subtotal' => $pedido->detalles->sum('subtotal')
                ]);
            });*/

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
