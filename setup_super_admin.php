<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "=== CONFIGURACIÓN DE ROL SUPER ADMIN PARA USER ID 1 ===\n\n";

// Buscar el usuario con ID 1
$user = User::find(1);

if (!$user) {
    echo "❌ Usuario con ID 1 no encontrado.\n";
    echo "Usuarios disponibles:\n";
    User::all(['id', 'name', 'email'])->each(function($u) {
        echo "  ID: {$u->id} - {$u->name}: {$u->email}\n";
    });
    exit;
}

echo "👤 Usuario encontrado: {$user->name} ({$user->email})\n\n";

// Crear o encontrar el rol super_admin
$role = Role::firstOrCreate(['name' => 'super_admin']);
echo "📋 Rol 'super_admin' configurado\n";

// Asignar el rol al usuario
if (!$user->hasRole('super_admin')) {
    $user->assignRole($role);
    echo "✅ Rol 'super_admin' asignado al usuario ID 1\n";
} else {
    echo "ℹ️ El usuario ID 1 ya tiene el rol 'super_admin'\n";
}

// Asignar TODOS los permisos al rol super_admin
$allPermissions = Permission::all();
$role->syncPermissions($allPermissions);

echo "🔑 Se asignaron {$allPermissions->count()} permisos al rol super_admin\n";

// Verificar roles y permisos del usuario
echo "\n📊 RESUMEN PARA USER ID 1:\n";
echo "  - Nombre: {$user->name}\n";
echo "  - Email: {$user->email}\n";
echo "  - Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
echo "  - Total permisos: " . $user->getAllPermissions()->count() . "\n";

echo "\n🎉 Configuración completada! El usuario ID 1 ahora tiene permisos de super_admin.\n";