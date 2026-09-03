<?php
/**
 * error.php — Smart & Modern Error Gateway (403, 404, 500)
 */
require_once __DIR__ . '/libs/load.php';

use Aether\Session;

// Detect actual HTTP error code from Apache redirect or query or default to 404
$statusCode = (int)($_SERVER['REDIRECT_STATUS'] ?? $_GET['code'] ?? 404);
if (!in_array($statusCode, [400, 401, 403, 404, 500, 503])) {
    $statusCode = 404;
}

http_response_code($statusCode);
Session::$isError = true;

// Pass dynamic error data into master layout
Session::renderPage();
