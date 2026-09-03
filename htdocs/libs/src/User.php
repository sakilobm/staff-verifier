<?php

namespace Aether;

use Aether\Traits\SQLGetterSetter;
use Exception;

/**
 * User Class
 * ==========
 * PSR-4 Namespace: Aether\User
 * Upgraded to use PDO database calls and Transactions.
 */
class User
{
    use SQLGetterSetter;

    public int $id;
    public string $username;
    public string $email;
    public string $table = 'auth';
    public $conn;

    /**
     * Signup a new user.
     */
    public static function signup(string $username, string $password, string $email, string $phone): bool
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        $conn = Database::getConnection();

        try {
            $conn->beginTransaction();

            // 1. Insert into auth
            $stmt = $conn->prepare(
                "INSERT INTO `auth` (`username`, `password`, `email`, `phone`, `active`) VALUES (?, ?, ?, ?, 1)"
            );
            $stmt->execute([$username, $hash, $email, $phone]);

            $id = $conn->lastInsertId();

            // 2. Insert default profile (maps to `users` profile table in skeleton schema)
            $stmt = $conn->prepare(
                "INSERT INTO `users` (`id`, `firstname`, `lastname`, `created_at`) VALUES (?, ?, ?, NOW())"
            );
            $stmt->execute([$id, $username, '']);

            $conn->commit();
            return true;
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log('User::signup() error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Basic login verification.
     */
    public static function login(string $user, string $password)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT `username`, `password`, `active`, `blocked` FROM `auth`
             WHERE `username` = ? OR `email` = ? LIMIT 1"
        );
        $stmt->execute([$user, $user]);
        $row = $stmt->fetch();

        if ($row) {
            if ((int)$row['blocked'] === 1) {
                return false;
            }
            if ((int)$row['active'] === 0) {
                return false;
            }
            if (password_verify($password, $row['password'])) {
                return $row['username'];
            }
        }

        return false;
    }

    /**
     * User Constructor.
     */
    public function __construct($identifier)
    {
        $this->table = 'auth';
        $this->conn  = Database::getConnection();

        $stmt = $this->conn->prepare(
            "SELECT `id`, `username`, `email` FROM `auth`
             WHERE `username` = ? OR `email` = ? OR `id` = ? LIMIT 1"
        );
        $id = is_numeric($identifier) ? (int)$identifier : -1;
        $stmt->execute([$identifier, $identifier, $id]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new Exception("User '{$identifier}' does not exist.");
        }

        $this->id       = (int)$row['id'];
        $this->username = $row['username'];
        $this->email    = $row['email'];
    }
}
