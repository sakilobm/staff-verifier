<?php

namespace Aether;

use Exception;
use Dotenv\Dotenv;

/**
 * WebAPI Bootstrap Class
 * =====================
 * PSR-4 Namespace: Aether\WebAPI
 * Bootstraps config load, dotenv variables, database connections, and secure session params.
 */
class WebAPI
{
    /**
     * WebAPI Constructor.
     */
    public function __construct()
    {
        global $__site_config;

        $configPath = $this->resolveConfigPath();

        // 1. Load config.json file (Legacy support)
        if ($configPath && file_exists($configPath)) {
            $__site_config = file_get_contents($configPath);
        }

        // 2. Initialize Dotenv (.env validation layer)
        $dotenvRoot = HTDOCS_ROOT . '/..'; // Root project dir (where .env lives)
        if (file_exists($dotenvRoot . '/.env')) {
            if (class_exists('Dotenv\\Dotenv')) {
                $dotenv = Dotenv::createImmutable($dotenvRoot);
                $dotenv->load();
                $dotenv->required(['DB_HOST', 'DB_USER', 'DB_NAME'])->notEmpty();
            } else {
                // Native .env parser fallback when vlucas/phpdotenv package is not installed
                $lines = file($dotenvRoot . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || str_starts_with($line, '#')) continue;
                    if (str_contains($line, '=')) {
                        list($name, $value) = explode('=', $line, 2);
                        $name = trim($name);
                        $value = trim($value, " \t\n\r\0\x0B\"'");
                        if ($name !== '' && !isset($_ENV[$name])) {
                            $_ENV[$name] = $value;
                            putenv("{$name}={$value}");
                        }
                    }
                }
            }
        }

        // Establish DB connection early
        Database::getConnection();

        // --- Secure Session Cookie Configuration ---
        // Dynamically detects HTTPS to allow session cookies on local HTTP development.
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        $cookieParams = session_get_cookie_params();
        
        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path'     => $cookieParams['path'],
            'domain'   => $cookieParams['domain'],
            'secure'   => $isSecure,
            'httponly' => true,      // Prevent session hijacking via XSS
            'samesite' => 'Lax'       // Protection against CSRF
        ]);
    }

    /**
     * Start the session and authorize if token exists.
     */
    public function initiateSession(): void
    {
        Session::start();

        if (Session::isset('session_token')) {
            try {
                Session::$usersession = UserSession::authorize(Session::get('session_token'));
            } catch (Exception $e) {
                // If authorization fails, clear the invalid token
                Session::delete('session_token');
            }
        }
    }

    /**
     * Resolves the path to config.json.
     */
    private function resolveConfigPath(): ?string
    {
        $candidates = [
            HTDOCS_ROOT . '/../project/config.json',
            HTDOCS_ROOT . '/../config.json',
            HTDOCS_ROOT . '/config.json',
        ];

        foreach ($candidates as $candidate) {
            $real = realpath($candidate);
            if ($real && file_exists($real)) {
                return $real;
            }
        }

        return null;
    }
}
