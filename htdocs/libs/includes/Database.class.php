<?php

/**
 * Advanced Database Class (Aether Core)
 * =====================================
 * Refactored to use PDO (PHP Data Objects) for security and multi-driver support.
 * Implements a fluent result wrapper and automatic prepared statement handling.
 */
class Database
{
    /** @var PDO|null Shared instance */
    public static $db = null;

    /** @var string|null Last error message */
    public static $error = null;

    /**
     * Get established PDO connection.
     * Reads from .env (via get_config() fallback).
     *
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        if (self::$db === null) {
            $host = get_config('db_server', 'localhost');
            $user = get_config('db_username', 'root');
            $pass = get_config('db_password', '');
            $name = get_config('db_name', 'aether_app');
            $port = get_config('db_port', '3306');

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            try {
                self::$db = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES    => false,
                ]);
            } catch (PDOException $e) {
                self::$error = $e->getMessage();
                error_log("Database Connection Error: " . self::$error);
                http_response_code(503);
                die(json_encode(['error' => 'Database Unavailable']));
            }
        }
        return self::$db;
    }

    /**
     * Execute a safe prepared query.
     *
     * @param string $sql    SQL statement with ? placeholders
     * @param array  $params Optional bind parameters
     * @return DatabaseResult
     */
    public static function query(string $sql, array $params = []): DatabaseResult
    {
        $db = self::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return new DatabaseResult($stmt);
    }
}

/**
 * DatabaseResult Wrapper
 * Facilitates fluent chains like: Database::query($sql)->fetchAll()
 */
class DatabaseResult
{
    private PDOStatement $stmt;

    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    /** @return array|false */
    public function fetch()
    {
        return $this->stmt->fetch();
    }

    /** @return array */
    public function fetchAll(): array
    {
        return $this->stmt->fetchAll();
    }

    /** @return int Number of affected rows */
    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    /** @return string|int Last insert ID */
    public function lastInsertId()
    {
        return Database::getConnection()->lastInsertId();
    }
}
