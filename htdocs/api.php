<?php

/**
 * api.php — Modern API Entry Point
 * Routes: /api/{method} or /api/{namespace}/{method}
 */

require_once 'libs/load.php';

use Aether\API;
use Aether\Session;

$api = new API();

// 1. Register Global Middleware (Framework header & CORS if needed)
$api->addMiddleware(function ($api, $next) {
    header('X-Framework: Aether-Catalyst');
    return $next();
});

// 2. Auth Middleware – Allow 'auth' and 'verify' public endpoints, protect administrative ones
$api->addMiddleware(function ($api, $next) {
    $namespace = $_GET['namespace'] ?? '';
    $publicNamespaces = ['auth', 'verify'];
    
    if (!in_array($namespace, $publicNamespaces) && !Session::isAuthenticated()) {
        $api->response($api->json(['error' => 'Unauthorized Access']), 401);
    }
    return $next();
});

// 3. Process the API request
try {
    $api->processApi();
} catch (Exception $e) {
    $api->die($e);
}
