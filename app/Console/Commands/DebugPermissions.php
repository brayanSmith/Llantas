<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DebugPermissions extends Command
{
    protected $signature = 'debug:permissions {email}';
    protected $description = 'Debug user permissions';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario no encontrado: {$email}");
            return;
        }
        
        $this->info("=== PERMISOS PARA: {$user->name} ({$user->email}) ===");
        
        // Roles
        $roles = $user->getRoleNames();
        $this->info("Roles: " . $roles->implode(', '));
        
        // Permisos directos
        $directPermissions = $user->getDirectPermissions()->pluck('name');
        $this->info("Permisos directos: " . count($directPermissions));
        
        // Permisos vía roles
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name');
        $this->info("Permisos vía roles: " . count($rolePermissions));
        
        // Todos los permisos
        $allPermissions = $user->getAllPermissions()->pluck('name');
        $this->info("Total permisos: " . count($allPermissions));
        
        // Filtrar permisos de pedidos
        $pedidoPermissions = $allPermissions->filter(function($perm) {
            return str_contains(strtolower($perm), 'pedido');
        });
        
        $this->info("\n=== PERMISOS DE PEDIDOS ===");
        foreach($pedidoPermissions as $perm) {
            $this->line("✓ {$perm}");
        }
        
        // Probar permisos específicos
        $this->info("\n=== PRUEBA PERMISOS ===");
        $testPermissions = [
            'ViewAny:PedidoResource',
            'ViewAny:PedidoDomiciliarioResource',
            'ViewAny:PedidosEstadoPagoSaldadoResource'
        ];
        
        foreach($testPermissions as $perm) {
            $hasPermission = $user->can($perm) ? '✓' : '❌';
            $this->line("{$hasPermission} {$perm}");
        }
    }
}
