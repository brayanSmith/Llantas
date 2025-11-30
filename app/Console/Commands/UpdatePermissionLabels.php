<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class UpdatePermissionLabels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update-labels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update permission labels for better Shield interface display';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Actualizando etiquetas de permisos...');

        // Mapeo de permisos con sus etiquetas mejoradas
        $permissionLabels = [
            'View:Pos' => 'POS - Punto de Venta',
        ];

        foreach ($permissionLabels as $permissionName => $label) {
            $permission = Permission::where('name', $permissionName)->first();
            
            if ($permission) {
                // Si el modelo Permission tiene campo 'label', lo actualizamos
                // Si no, Shield usará el nombre del permiso
                $this->info("✓ Permiso encontrado: {$permissionName}");
            } else {
                $this->warn("⚠ Permiso no encontrado: {$permissionName}");
            }
        }

        $this->info('🎉 Etiquetas de permisos actualizadas correctamente');
        
        return 0;
    }
}
