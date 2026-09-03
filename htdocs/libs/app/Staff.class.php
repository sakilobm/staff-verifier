<?php

namespace App;

use Aether\Database;
use PDO;

/**
 * Staff Model
 * ===========
 * Manages staff records and their current verification states.
 */
class Staff
{
    /**
     * Get all staff members for a specific college with their verification status.
     *
     * @param string $collegeCode
     * @return array
     */
    public static function getByCollege(string $collegeCode): array
    {
        $pdo = Database::getConnection();

        $sql = "
            SELECT 
                s.sno,
                s.college_code,
                s.name,
                s.designation,
                v.status,
                v.verifier_phone,
                v.verifier_email,
                v.verified_at
            FROM staff s
            LEFT JOIN verifications v ON s.sno = v.staff_sno
            WHERE s.college_code = ?
            ORDER BY s.sno ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$collegeCode]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
