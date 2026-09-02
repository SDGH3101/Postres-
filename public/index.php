<?php

// Silencia los avisos de deprecación de PHP 8.5 (p. ej. PDO::MYSQL_ATTR_SSL_CA)
// que Laravel aún no ha actualizado en su config/database.php por defecto.
// Esto NO oculta errores reales, solo E_DEPRECATED.
error_reporting(E_ALL & ~E_DEPRECATED);

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Illuminate\Http\Request::capture());
