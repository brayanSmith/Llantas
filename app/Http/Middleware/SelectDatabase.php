<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SelectDatabase
{
    public function handle($request, Closure $next)
    {
        $host = $request->getHost(); // dominio usado en la petición

        $database = match ($host) {
            'ferrepint.com', 'www.ferrepint.com' => 'db_ferrepint',
            'ferredistri.com', 'www.ferredistri.com' => 'db_ferredistri',
            default => 'db_icferreteria', // sistema principal
        };

        // Cambia dinámicamente la conexión por defecto
        Config::set('database.connections.mysql.database', $database);

        // Reconecta para que Laravel use la BD recién asignada
        DB::purge('mysql');
        DB::reconnect('mysql');

        return $next($request);
    }
}