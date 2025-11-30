<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use BezhanSalleh\FilamentShield\Commands\MakeShieldGenerateCommand;

class FixPagePermissions extends Command
{
    protected $signature = 'shield:fix-pages';
    protected $description = 'Fix page permissions display names in Shield';

    public function handle()
    {
        $this->info('🔧 Corrigiendo nombres de páginas en Shield...');

        // Verificar si existe el permiso view_pos
        $permission = Permission::where('name', 'view_pos')->first();
        
        if (!$permission) {
            Permission::create([
                'name' => 'view_pos',
                'guard_name' => 'web'
            ]);
            $this->info('✓ Permiso view_pos creado');
        }

        // Verificar que el formato correcto existe
        $posPermission = Permission::where('name', 'LIKE', '%pos%')->orWhere('name', 'LIKE', '%Pos%')->get();
        
        $this->info('📋 Permisos relacionados con POS encontrados:');
        foreach ($posPermission as $perm) {
            $this->line("  - {$perm->name}");
        }

        $this->info('🎉 Permisos de página corregidos. Refresh la página de Shield.');
        
        return 0;
    }
}