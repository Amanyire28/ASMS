<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| SHARED HOSTING VERSION
| This file goes in public_html/index.php
| The Laravel app folder (asms) sits one level above public_html
|--------------------------------------------------------------------------
*/

if (file_exists($maintenance = __DIR__.'/../asms/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../asms/vendor/autoload.php';

$app = require_once __DIR__.'/../asms/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
