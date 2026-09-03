<?php

namespace Aether;

/**
 * REST Class
 * ==========
 * PSR-4 Namespace: Aether\REST
 * Upgraded with payload JSON parsing, automated serialization, and selective inputs cleaning.
 */
class REST
{
    public array $_request = [];
    public string $_content_type = 'application/json';
    private int $_code = 200;

    public function __construct() { $this->inputs(); }

    public function response($data, int $status = 200): void
    {
        $this->_code = $status;
        $this->setHeaders();
        if (is_array($data) || is_object($data)) {
            echo json_encode($data);
        } else {
            echo $data;
        }
        exit;
    }

    /**
     * Get JSON payload from request body.
     */
    public function getJsonPayload(): array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }

    /**
     * Require the user to be authenticated.
     */
    public function requireAuth(): void
    {
        if (!\Aether\Session::isAuthenticated()) {
            $this->response(['error' => 'Authentication required'], 401);
        }
    }

    public function get_request_method(): string { return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'); }

    private function inputs(): void
    {
        switch ($this->get_request_method()) {
            case 'POST':
                $this->_request = $this->cleanInputs(array_merge($_GET, $_POST));
                break;
            case 'GET':
            case 'DELETE':
                $this->_request = $this->cleanInputs($_GET);
                break;
            case 'PUT':
            case 'PATCH':
                parse_str(file_get_contents('php://input'), $input);
                $this->_request = $this->cleanInputs($input);
                break;
            default:
                $this->response('', 406);
        }
    }

    /**
     * Clean inputs recursively.
     * Skips strip_tags for sensitive fields like passwords, tokens, or fingerprints.
     */
    private function cleanInputs($data, $key = null)
    {
        if (is_array($data)) {
            $cleaned = [];
            foreach ($data as $k => $v) {
                $cleaned[$k] = $this->cleanInputs($v, $k);
            }
            return $cleaned;
        }

        // Sensitive fields that should NOT be strip_tagged
        $skipStrip = ['password', 'fingerprint', 'token'];
        if (in_array($key, $skipStrip)) {
            return trim($data);
        }

        return trim(strip_tags($data));
    }

    private function setHeaders(): void
    {
        $messages = [
            200 => 'OK', 201 => 'Created', 204 => 'No Content',
            400 => 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden',
            404 => 'Not Found', 405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ];

        $msg = $messages[$this->_code] ?? 'Unknown';
        header("HTTP/1.1 {$this->_code} {$msg}");
        header("Content-Type: {$this->_content_type}");
    }
}
