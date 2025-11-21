<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SwitchDatabaseByDomain
{
    public function handle($request, Closure $next)
    {
        $host = $request->getHost();

        if ($host === 'ferrepint.com' || $host === 'www.ferrepint.com') {
            Config::set('database.default', 'mysql_ferrepint');
        }
        elseif ($host === 'ferredistri.com' || $host === 'www.ferredistri.com') {
            Config::set('database.default', 'mysql_ferredistri');
        }
        else {
            Config::set('database.default', 'mysql_icferreteria');
        }

        DB::purge(); // refresca la conexión
        DB::reconnect();

        return $next($request);
    }
}