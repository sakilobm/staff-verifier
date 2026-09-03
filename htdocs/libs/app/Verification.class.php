<?php

namespace App;

use Aether\Database;
use PDO;

/**
 * Verification Model
 * ==================
 * Handles saving, clearing, resetting, and exporting staff verification records.
 */
class Verification
{
    /**
     * Update verification status for a single staff member.
     *
     * @param int $staffSno
     * @param string $collegeCode
     * @param string $status 'yes' | 'no'
     * @param int|null $verifierUserId
     * @param string|null $verifierPhone
     * @param string|null $verifierEmail
     * @return bool
     */
    public static function updateStatus(
        int $staffSno,
        string $collegeCode,
        string $status,
        ?int $verifierUserId = null,
        ?string $verifierPhone = null,
        ?string $verifierEmail = null
    ): bool {
        $pdo = Database::getConnection();

        $sql = "
            INSERT INTO verifications 
                (staff_sno, college_code, status, verifier_user_id, verifier_phone, verifier_email, verified_at)
            VALUES 
                (?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                status = VALUES(status),
                verifier_user_id = VALUES(verifier_user_id),
                verifier_phone = VALUES(verifier_phone),
                verifier_email = VALUES(verifier_email),
                verified_at = NOW()
        ";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $staffSno,
            $collegeCode,
            $status,
            $verifierUserId,
            $verifierPhone,
            $verifierEmail
        ]);
    }

    /**
     * Bulk save answers for a college.
     *
     * @param string $collegeCode
     * @param array $answers Associative array of [staff_sno => 'yes'|'no']
     * @param int|null $verifierUserId
     * @param string|null $verifierPhone
     * @param string|null $verifierEmail
     * @return int Number of updated rows
     */
    public static function bulkSave(
        string $collegeCode,
        array $answers,
        ?int $verifierUserId = null,
        ?string $verifierPhone = null,
        ?string $verifierEmail = null
    ): int {
        $pdo = Database::getConnection();
        $count = 0;

        $pdo->beginTransaction();
        try {
            $sql = "
                INSERT INTO verifications 
                    (staff_sno, college_code, status, verifier_user_id, verifier_phone, verifier_email, verified_at)
                VALUES 
                    (?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    status = VALUES(status),
                    verifier_user_id = VALUES(verifier_user_id),
                    verifier_phone = VALUES(verifier_phone),
                    verifier_email = VALUES(verifier_email),
                    verified_at = NOW()
            ";
            $stmt = $pdo->prepare($sql);

            foreach ($answers as $sno => $status) {
                $sno = (int)$sno;
                $status = strtolower(trim($status));
                if (!in_array($status, ['yes', 'no'])) continue;

                $stmt->execute([
                    $sno,
                    $collegeCode,
                    $status,
                    $verifierUserId,
                    $verifierPhone,
                    $verifierEmail
                ]);
                $count++;
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        return $count;
    }

    /**
     * Clear all verifications for a specific college.
     *
     * @param string $collegeCode
     * @return int
     */
    public static function clearCollege(string $collegeCode): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM verifications WHERE college_code = ?");
        $stmt->execute([$collegeCode]);
        return $stmt->rowCount();
    }

    /**
     * Reset all verification records across the entire system.
     *
     * @return bool
     */
    public static function resetAll(): bool
    {
        $pdo = Database::getConnection();
        return (bool)$pdo->exec("TRUNCATE TABLE verifications");
    }

    /**
     * Fetch all records for CSV export.
     *
     * @param bool $pendingOnly
     * @return array
     */
    public static function getExportData(bool $pendingOnly = false): array
    {
        $pdo = Database::getConnection();

        $where = $pendingOnly ? "WHERE v.status IS NULL" : "";

        $sql = "
            SELECT 
                s.sno AS `Sno`,
                c.code AS `College Code`,
                c.name AS `College Name`,
                s.name AS `Name of the Staff member`,
                COALESCE(s.designation, '') AS `Designation`,
                COALESCE(v.status, 'pending') AS `Status`,
                COALESCE(v.verifier_phone, '') AS `Verifier Phone`,
                COALESCE(v.verifier_email, '') AS `Verifier Email`,
                COALESCE(v.verified_at, '') AS `Verified At`
            FROM staff s
            JOIN colleges c ON s.college_code = c.code
            LEFT JOIN verifications v ON s.sno = v.staff_sno
            {$where}
            ORDER BY c.code ASC, s.sno ASC
        ";

        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
