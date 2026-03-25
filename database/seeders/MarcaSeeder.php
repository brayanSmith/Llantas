<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marcas = [
            'Michelin',
            'Bridgestone',
            'Goodyear',
            'Continental',
            'Pirelli',
            'Dunlop',
            'Vredestein',
            'Toyo',
            'Hankook',
            'Kumho',
            'Yokohama',
            'Cooper',
            'General Tire',
            'Falken',
            'Maxxis',
            'BBS',
            'Enkei',
            'OZ Racing',
        ];

        foreach ($marcas as $marca) {
            Marca::create([
                'marca' => $marca,
            ]);
        }
    }
}
