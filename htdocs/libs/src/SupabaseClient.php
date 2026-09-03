<?php

namespace Aether;

/**
 * SupabaseClient — Reusable client interface for Supabase REST APIs.
 *
 * Credentials are loaded via get_config() / .env variables:
 *   - SUPABASE_URL
 *   - SUPABASE_ANON_KEY
 *   - SUPABASE_SERVICE_KEY (Optional, for administrative API operations)
 */
class SupabaseClient
{
    /**
     * Send an HTTP REST request to Supabase endpoints.
     *
     * @param string $method           HTTP verb (GET, POST, PATCH, DELETE)
     * @param string $path             REST resource route (e.g. 'reports' or 'rpc/my_func')
     * @param array|null $body         Optional payload body array
     * @param bool $useServiceRole     Use administrative service role key instead of anon key
     * @return array
     */
    public static function request(string $method, string $path, ?array $body = null, bool $useServiceRole = false): array
    {
        $url = get_config('supabase_url');
        $key = $useServiceRole ? get_config('supabase_service_key') : get_config('supabase_anon_key');
        
        if (empty($key)) {
            $key = get_config('supabase_anon_key');
        }

        if (empty($url) || empty($key)) {
            error_log("SupabaseClient: Missing configuration (url/key).");
            return ['error' => 'Unconfigured Client'];
        }

        $endpoint = rtrim($url, '/') . '/rest/v1/' . ltrim($path, '/');
        $ch = curl_init($endpoint);
        
        $headers = [
            "apikey: {$key}",
            "Authorization: Bearer {$key}",
            "Content-Type: application/json"
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        
        if ($body !== null) {
            $json = json_encode($body);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            $headers[] = "Content-Length: " . strlen($json);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true) ?? [];
        }

        error_log("SupabaseClient Request Failure: HTTP {$httpCode} - {$response}");
        return ['error' => "HTTP {$httpCode}", 'details' => $response];
    }
}
