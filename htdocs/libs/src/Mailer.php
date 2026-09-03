<?php

namespace Aether;

/**
 * Mailer — Lightweight SMTP socket client for sending transactional emails.
 *
 * Connects to SMTP servers using streams.
 * Configuration variables are loaded via get_config() / .env.
 */
class Mailer
{
    private static string $smtpHost  = 'smtp.gmail.com';
    private static int    $smtpPort  = 587;
    private static string $smtpUser  = '';
    private static string $smtpPass  = '';
    private static string $fromName  = 'System Admin';
    private static string $fromEmail = '';

    /**
     * Initialize Mailer configuration from environment / configuration.
     */
    private static function init(): void
    {
        self::$smtpHost  = get_config('smtp_host', 'smtp.gmail.com');
        self::$smtpPort  = (int)get_config('smtp_port', 587);
        self::$smtpUser  = get_config('smtp_user', '');
        self::$smtpPass  = get_config('smtp_pass', '');
        self::$fromName  = get_config('smtp_from_name', get_config('project_title', 'Aether') . ' Security');
        self::$fromEmail = get_config('smtp_from_email', self::$smtpUser);
    }

    /**
     * Send HTML email using native stream socket connection.
     *
     * @param string $toEmail Recipient Email
     * @param string $toName  Recipient Name
     * @param string $subject Email Subject
     * @param string $htmlBody HTML Body
     * @return bool
     */
    public static function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody
    ): bool {
        self::init();

        if (empty(self::$smtpUser) || empty(self::$smtpPass)) {
            error_log("Mailer: SMTP credentials are not configured in environment.");
            return false;
        }

        // Disable strict peer verification if needed for localhost self-signed certificates
        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ]);

        $sock = @stream_socket_client(
            'tcp://' . self::$smtpHost . ':' . self::$smtpPort,
            $errno, $errstr, 15,
            STREAM_CLIENT_CONNECT,
            $ctx
        );

        if (!$sock) {
            error_log("Mailer: connect failed — $errstr ($errno)");
            return false;
        }

        stream_set_timeout($sock, 15);

        try {
            // ── Greeting ────────────────────────────────────────────────────
            $line = self::readLine($sock);
            if (!self::is($line, 220)) return self::quit($sock, false);

            // ── EHLO ────────────────────────────────────────────────────────
            self::writeLine($sock, 'EHLO ' . (gethostname() ?: 'localhost'));
            self::readMultiLine($sock);

            // ── STARTTLS ────────────────────────────────────────────────────
            self::writeLine($sock, 'STARTTLS');
            $line = self::readLine($sock);
            if (!self::is($line, 220)) return self::quit($sock, false);

            // Upgrade connection to TLS
            if (!@stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                error_log("Mailer: STARTTLS upgrade failed.");
                return self::quit($sock, false);
            }

            // ── EHLO (after TLS) ─────────────────────────────────────────────
            self::writeLine($sock, 'EHLO ' . (gethostname() ?: 'localhost'));
            self::readMultiLine($sock);

            // ── AUTH LOGIN ───────────────────────────────────────────────────
            self::writeLine($sock, 'AUTH LOGIN');
            $line = self::readLine($sock);
            if (!self::is($line, 334)) return self::quit($sock, false);

            // Remove spaces from credentials (common in gmail app passwords)
            $pass = str_replace(' ', '', self::$smtpPass);

            self::writeLine($sock, base64_encode(self::$smtpUser));
            $line = self::readLine($sock);
            if (!self::is($line, 334)) return self::quit($sock, false);

            self::writeLine($sock, base64_encode($pass));
            $line = self::readLine($sock);
            if (!self::is($line, 235)) {
                error_log("Mailer: SMTP Authentication failed — $line");
                return self::quit($sock, false);
            }

            // ── MAIL FROM ────────────────────────────────────────────────────
            self::writeLine($sock, 'MAIL FROM:<' . self::$fromEmail . '>');
            $line = self::readLine($sock);
            if (!self::is($line, 250)) return self::quit($sock, false);

            // ── RCPT TO ──────────────────────────────────────────────────────
            self::writeLine($sock, 'RCPT TO:<' . $toEmail . '>');
            $line = self::readLine($sock);
            if (!self::is($line, 250)) return self::quit($sock, false);

            // ── DATA ─────────────────────────────────────────────────────────
            self::writeLine($sock, 'DATA');
            $line = self::readLine($sock);
            if (!self::is($line, 354)) return self::quit($sock, false);

            // Build MIME payload
            $fromEncoded    = '=?UTF-8?B?' . base64_encode(self::$fromName) . '?=';
            $toEncoded      = $toName
                ? '=?UTF-8?B?' . base64_encode($toName) . '?= <' . $toEmail . '>'
                : $toEmail;
            $subjectEncoded = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $bodyB64        = chunk_split(base64_encode($htmlBody));

            $message = "From: {$fromEncoded} <" . self::$fromEmail . ">\r\n"
                     . "To: {$toEncoded}\r\n"
                     . "Subject: {$subjectEncoded}\r\n"
                     . "Date: " . date('r') . "\r\n"
                     . "MIME-Version: 1.0\r\n"
                     . "Content-Type: text/html; charset=UTF-8\r\n"
                     . "Content-Transfer-Encoding: base64\r\n"
                     . "\r\n"
                     . $bodyB64
                     . "\r\n.\r\n";

            fwrite($sock, $message);
            $line = self::readLine($sock);

            $ok = self::is($line, 250);
            return self::quit($sock, $ok);

        } catch (\Throwable $e) {
            error_log("Mailer: SMTP Exception — " . $e->getMessage());
            self::quit($sock, false);
            return false;
        }
    }

    private static function writeLine($sock, string $line): void
    {
        fwrite($sock, $line . "\r\n");
    }

    private static function readLine($sock): string
    {
        return (string) fgets($sock, 1024);
    }

    private static function readMultiLine($sock): string
    {
        $last = '';
        do {
            $last = self::readLine($sock);
        } while (strlen($last) > 3 && $last[3] === '-');
        return $last;
    }

    private static function is(string $line, int $code): bool
    {
        return strncmp($line, (string) $code, 3) === 0;
    }

    private static function quit($sock, bool $result): bool
    {
        if (is_resource($sock)) {
            @fwrite($sock, "QUIT\r\n");
            @fclose($sock);
        }
        return $result;
    }
}
