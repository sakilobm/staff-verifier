<?php
/**
 * files.php — Secure Dynamic Media & File Streamer
 * =================================================
 * Serves user-uploaded media (photos, documents, private PDFs) safely
 * from a protected storage directory outside webroot without exposing
 * physical server paths or risking directory traversal attacks.
 *
 * Routing (.htaccess):
 *   /files/{filename} → files.php?name={filename}
 */

require_once __DIR__ . '/libs/load.php';

try {
    if (!empty($_GET['name'])) {
        // 1. Sanitize filename against Directory Traversal attacks (e.g. ../../etc/passwd)
        $fname = basename($_GET['name']);

        // 2. Resolve target storage directory (from config.json or fallback)
        $upload_dir = get_config('upload_path', HTDOCS_ROOT . '/../uploads/');
        $filePath   = rtrim($upload_dir, '/') . '/' . $fname;

        // 3. Verify file exists and is a valid regular file
        if (is_file($filePath) && file_exists($filePath)) {
            $mime = mime_content_type($filePath) ?: 'application/octet-stream';

            // Clean previous output buffers
            if (ob_get_level()) {
                ob_end_clean();
            }

            // HTTP Response Headers
            header("Content-Type: " . $mime);
            header("Content-Length: " . filesize($filePath));
            header("Cache-Control: public, max-age=31536000, immutable");
            header_remove("Pragma");

            // Zero-RAM high performance direct binary streaming
            readfile($filePath);
            exit;
        } else {
            http_response_code(404);
            header('Content-Type: text/plain');
            die('404 Not Found');
        }
    } else {
        http_response_code(400);
        die('Bad Request: File identifier missing');
    }
} catch (\Throwable $e) {
    http_response_code(500);
    error_log("Aether File Streamer Error: " . $e->getMessage());
    die('Internal Server Error');
}
