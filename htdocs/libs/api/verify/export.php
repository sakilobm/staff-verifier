<?php
use App\Verification;

$export = function () {
    $pending = (bool)($_GET['pending'] ?? false);
    $rows = Verification::getExportData($pending);

    $filename = $pending ? "staff_pending_" . date('Ymd_His') . ".csv" : "staff_verification_complete_" . date('Ymd_His') . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');
    fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM

    if (!empty($rows)) {
        fputcsv($output, array_keys($rows[0]));
        foreach ($rows as $r) {
            fputcsv($output, $r);
        }
    }
    fclose($output);
    exit;
};
