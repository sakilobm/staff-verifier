<?php

namespace App;

use Aether\Database;
use PDO;

/**
 * College Model
 * =============
 * Manages college data and verification statistics from MySQL.
 */
class College
{
    /**
     * Get all colleges with their verification counts and status.
     *
     * @param string $search Search query
     * @param string $filter 'all' | 'pending' | 'yes' | 'no'
     * @return array
     */
    public static function getAllWithStats(string $search = '', string $filter = 'all'): array
    {
        $pdo = Database::getConnection();

        $search = trim($search);
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = "WHERE (c.name LIKE ? OR c.code LIKE ? OR s.name LIKE ? OR s.designation LIKE ?)";
            $q = "%{$search}%";
            $params = [$q, $q, $q, $q];
        }

        $sql = "
            SELECT 
                c.code, 
                c.name,
                COUNT(s.sno) AS total_staff,
                COALESCE(SUM(CASE WHEN v.status = 'yes' THEN 1 ELSE 0 END), 0) AS yes_count,
                COALESCE(SUM(CASE WHEN v.status = 'no' THEN 1 ELSE 0 END), 0) AS no_count,
                (COUNT(s.sno) - COUNT(v.id)) AS pending_count
            FROM colleges c
            LEFT JOIN staff s ON c.code = s.college_code
            LEFT JOIN verifications v ON s.sno = v.staff_sno
            {$where}
            GROUP BY c.code, c.name
            ORDER BY c.code ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Normalize integers
        foreach ($colleges as &$c) {
            $c['total_staff']   = (int)$c['total_staff'];
            $c['yes_count']     = (int)$c['yes_count'];
            $c['no_count']      = (int)$c['no_count'];
            $c['pending_count'] = (int)$c['pending_count'];
        }
        unset($c);

        // Filter post-aggregation if needed
        if ($filter === 'pending') {
            $colleges = array_values(array_filter($colleges, fn($c) => $c['pending_count'] > 0));
        } elseif ($filter === 'yes') {
            $colleges = array_values(array_filter($colleges, fn($c) => $c['yes_count'] > 0));
        } elseif ($filter === 'no') {
            $colleges = array_values(array_filter($colleges, fn($c) => $c['no_count'] > 0));
        }

        return $colleges;
    }

    /**
     * Get overall project verification statistics.
     *
     * @return array
     */
    public static function getOverallStats(): array
    {
        $pdo = Database::getConnection();

        $sql = "
            SELECT
                (SELECT COUNT(*) FROM colleges) AS total_colleges,
                (SELECT COUNT(*) FROM staff) AS total_staff,
                (SELECT COUNT(*) FROM verifications WHERE status = 'yes') AS yes_count,
                (SELECT COUNT(*) FROM verifications WHERE status = 'no') AS no_count,
                ((SELECT COUNT(*) FROM staff) - (SELECT COUNT(*) FROM verifications)) AS pending_count
        ";

        $stmt = $pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'colleges' => (int)($row['total_colleges'] ?? 0),
            'staff'    => (int)($row['total_staff'] ?? 0),
            'yes'      => (int)($row['yes_count'] ?? 0),
            'no'       => (int)($row['no_count'] ?? 0),
            'pending'  => max(0, (int)($row['pending_count'] ?? 0)),
        ];
    }

    /**
     * Get a single college by code.
     *
     * @param string $code
     * @return array|null
     */
    public static function getByCode(string $code): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT code, name FROM colleges WHERE code = ? LIMIT 1");
        $stmt->execute([$code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
