<?php

/**
 * Modern Framework Loader (PSR-4)
 * ===============================
 * Bootstraps the framework using Composer autoloader.
 */

// Define absolute path to project root
define('HTDOCS_ROOT', __DIR__ . '/..');

// Load Composer autoloader (PSR-4 + Dependencies) if present, or fallback to core autoloader
if (file_exists(__DIR__ . "/../vendor/autoload.php")) {
    $loader = require_once __DIR__ . "/../vendor/autoload.php";
} else {
    // Custom Fallback PSR-4 Autoloader for core framework namespaces (Aether\ and App\)
    spl_autoload_register(function ($class) {
        $prefixAether = "Aether\\";
        $baseDirAether = __DIR__ . "/src/";
        $lenAether = strlen($prefixAether);
        if (strncmp($prefixAether, $class, $lenAether) === 0) {
            $relativeClass = substr($class, $lenAether);
            $file = $baseDirAether . str_replace("\\", "/", $relativeClass) . ".php";
            if (file_exists($file)) {
                require $file;
                return;
            }
        }

        $prefixApp = "App\\";
        $baseDirApp = __DIR__ . "/app/";
        $lenApp = strlen($prefixApp);
        if (strncmp($prefixApp, $class, $lenApp) === 0) {
            $relativeClass = substr($class, $lenApp);
            $file = $baseDirApp . str_replace("\\", "/", $relativeClass) . ".class.php";
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    });
}

// --- Vendor-style Class Aliasing (Global access) ---
class_alias('Aether\Session', 'Session');
class_alias('Aether\User', 'User');
class_alias('Aether\UserSession', 'UserSession');
class_alias('Aether\Database', 'Database');
class_alias('Aether\WebAPI', 'WebAPI');
class_alias('Aether\SupabaseClient', 'SupabaseClient');
class_alias('Aether\Mailer', 'Mailer');

// Global config singleton (Legacy support)
global $__site_config;

// Bootstrap: Initialize WebAPI (Loads .env, validates, connects DB, initiates session)
use Aether\WebAPI;

$wapi = new WebAPI();
$wapi->initiateSession();

/**
 * Universal config reader – Checks .env first, then config.json.
 * Auto-detects base_path if not explicitly forced.
 *
 * @param string $key     The key to read (can be ENV_CONSTANT or json_key)
 * @param mixed  $default Default value if not found
 * @return mixed
 */
function get_config(string $key, $default = null)
{
    // Auto-detect base_path based on current request environment
    if ($key === 'base_path') {
        if (!empty($_ENV['BASE_PATH'])) return $_ENV['BASE_PATH'];
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
        $base = ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') ? '/' : rtrim($scriptDir, '/') . '/';
        return $base;
    }

    // 1. Try Environment Variables
    $envValue = $_ENV[strtoupper($key)] ?? $_SERVER[strtoupper($key)] ?? false;
    if ($envValue !== false) return $envValue;

    // 2. Try global config JSON string ($__site_config)
    global $__site_config;
    if (!empty($__site_config)) {
        $array = json_decode($__site_config, true);
        if (isset($array[$key])) return $array[$key];
    }

    return $default;
}

/**
 * Direct include helper for small partials.
 * For full views, use Aether\Session::renderView().
 */
function load_template(string $name): void
{
    include HTDOCS_ROOT . "/_templates/{$name}.php";
}
