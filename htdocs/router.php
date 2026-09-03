<?php
/**
 * router.php — Development Server Router for php -S
 * =================================================
 * Enables full Aether Catalyst URL rewriting & static assets
 * when running via `php -S 0.0.0.0:8080 router.php`.
 */

$rawUri = $_SERVER['REQUEST_URI'];
$path   = parse_url($rawUri, PHP_URL_PATH);
$file   = __DIR__ . $path;

// 1. If static file exists, let the built-in server serve it
if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// 2. Route API endpoints: /api/{namespace}/{method} or /api/{method}
if (preg_match('#^/api/([^/]+)/(.+)$#', $path, $matches)) {
    $_GET['namespace'] = $matches[1];
    $_GET['rquest']    = $matches[2];
    $_REQUEST['namespace'] = $matches[1];
    $_REQUEST['rquest']    = $matches[2];
    require __DIR__ . '/api.php';
    exit;
}

if (preg_match('#^/api/([^/]+)$#', $path, $matches)) {
    $_GET['rquest']     = $matches[1];
    $_REQUEST['rquest'] = $matches[1];
    require __DIR__ . '/api.php';
    exit;
}

// 3. Clean URLs: /login -> login.php, /signup -> signup.php, /admin -> admin.php
$clean = trim($path, '/');
if ($clean !== '' && file_exists(__DIR__ . '/' . $clean . '.php')) {
    require __DIR__ . '/' . $clean . '.php';
    exit;
}

// 4. Fallback to index.php
require __DIR__ . '/index.php';
