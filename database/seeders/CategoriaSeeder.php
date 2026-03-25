<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;
use App\Models\Atributo;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear categoría Llantas
        $llantas = Categoria::create([
            'nombre_categoria' => 'llantas',
            'aplica_inventario' => true,
        ]);

        // Atributos para Llantas
        Atributo::create([
            'categoria_id' => $llantas->id,
            'nombre' => 'Ancho',
            'tipo' => 'NUMERO',
            'valor_por_defecto' => null,
        ]);

        Atributo::create([
            'categoria_id' => $llantas->id,
            'nombre' => 'Separador',
            'tipo' => 'SEPARADOR',
            'valor_por_defecto' => '/',
        ]);

        Atributo::create([
            'categoria_id' => $llantas->id,
            'nombre' => 'Perfil',
            'tipo' => 'NUMERO',
            'valor_por_defecto' => null,
        ]);

        Atributo::create([
            'categoria_id' => $llantas->id,
            'nombre' => 'Construcción',
            'tipo' => 'TEXTO',
            'valor_por_defecto' => 'R',
        ]);

        Atributo::create([
            'categoria_id' => $llantas->id,
            'nombre' => 'Rin',
            'tipo' => 'NUMERO',
            'valor_por_defecto' => null,
        ]);

        $llantas2da = Categoria::create([
            'nombre_categoria' => 'Llantas 2da',
            'aplica_inventario' => true,
        ]);
        // Atributos para Llantas 2da
        Atributo::create([
            'categoria_id' => $llantas2da->id,
            'nombre' => 'Referencia',
            'tipo' => 'TEXTO',
            'valor_por_defecto' => 'R',
        ]);
        Atributo::create([
            'categoria_id' => $llantas2da->id,
            'nombre' => 'Separador',
            'tipo' => 'SEPARADOR',
            'valor_por_defecto' => '-',
        ]);

        Atributo::create([
            'categoria_id' => $llantas2da->id,
            'nombre' => 'Tipo',
            'tipo' => 'SEPARADOR',
            'valor_por_defecto' => 'SEGUNDA',
        ]);

        Atributo::create([
            'categoria_id' => $llantas2da->id,
            'nombre' => 'Separador2',
            'tipo' => 'SEPARADOR',
            'valor_por_defecto' => ' ',
        ]);

        Atributo::create([
            'categoria_id' => $llantas2da->id,
            'nombre' => 'Tipo',
            'tipo' => 'ENUM',
            'opciones' => json_encode(['R', 'O']),
            'valor_por_defecto' => 'R',
        ]);

        // Crear categoría Rines
        $rines = Categoria::create([
            'nombre_categoria' => 'Rines',
            'aplica_inventario' => true,
        ]);

        // Atributos para Rines
        Atributo::create([
            'categoria_id' => $rines->id,
            'nombre' => 'Diámetro',
            'tipo' => 'NUMERO',
            'valor_por_defecto' => null,
        ]);

        Atributo::create([
            'categoria_id' => $rines->id,
            'nombre' => 'Separador',
            'tipo' => 'SEPARADOR',
            'valor_por_defecto' => 'X',
        ]);

        Atributo::create([
            'categoria_id' => $rines->id,
            'nombre' => 'Ancho',
            'tipo' => 'TEXTO',
            'valor_por_defecto' => null,
        ]);

        //Crar categoría Rines de Segunda
        $rines2da = Categoria::create([
            'nombre_categoria' => 'Rines 2da',
            'aplica_inventario' => true,
        ]);

        // Atributos para Rines de Segunda
        Atributo::create([
            'categoria_id' => $rines2da->id,
            'nombre' => 'Referencia',
            'tipo' => 'TEXTO',
            'valor_por_defecto' => null,
        ]);

        // Crear categoría Servicios
        $servicios = Categoria::create([
            'nombre_categoria' => 'Servicios',
            'aplica_inventario' => false,
        ]);

        // Atributos para Servicios
        Atributo::create([
            'categoria_id' => $servicios->id,
            'nombre' => 'Tipo de Servicio',
            'tipo' => 'ENUM',
            'opciones' => json_encode(['Instalación', 'Mantenimiento', 'Reparación', 'Alineación']),
            'valor_por_defecto' => 'Instalación',
        ]);

        // Crear categoría OtrosProductos
        $otros = Categoria::create([
            'nombre_categoria' => 'OtrosProductos',
            'aplica_inventario' => true,
        ]);

        // Atributos para OtrosProductos
        Atributo::create([
            'categoria_id' => $otros->id,
            'nombre' => 'Especificaciones',
            'tipo' => 'TEXTO',
            'valor_por_defecto' => null,
        ]);
    }
}
