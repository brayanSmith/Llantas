<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
            User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password123'),
                //'role' => Role::firstOrCreate(['name' => 'super_admin']),
                'bodega_id' => 1, // Asignar a la bodega 1
            ]
        );
            User::updateOrCreate(
            ['email' => 'comercialoutlet@admin.com'],
            [
                'name' => 'Comercial',
                'password' => bcrypt('password123'),
                //'role' => Role::firstOrCreate(['name' => 'comercial']),
                'bodega_id' => 1, // Asignar a la bodega 1
            ]
        );

        User::updateOrCreate(
            ['email' => 'comercialeonomi@admin.com'],
            [
                'name' => 'Cliente',
                'password' => bcrypt('password123'),
                //'role' => Role::firstOrCreate(['name' => 'comercial']),
                'bodega_id' => 2, // Asignar a la bodega 2
            ]
        );

            User::updateOrCreate(
                ['email' => 'comercialxmayor@admin.com'],
                [
                    'name' => 'Comercial x Mayor',
                    'password' => bcrypt('password123'),
                    //'role' => Role::firstOrCreate(['name' => 'comercial_x_mayor']),
                    'bodega_id' => 1, // Asignar a la bodega 1
                ]
            );
    }
}
