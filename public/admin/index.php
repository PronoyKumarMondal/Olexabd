<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// DEBUG: Check if we are hitting this file and what the host is
// die("Admin Entry Point Hit! Host: " . $_SERVER['HTTP_HOST']);

// Adjust paths to point to the main project root (Go up 2 levels: public/admin -> root)
if (file_exists($maintenance = __DIR__.'/../../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register availability of the Composer autoloader...
require __DIR__.'/../../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../../bootstrap/app.php')
    ->handleRequest(Request::capture());
