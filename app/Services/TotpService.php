<?php

declare(strict_types=1);

namespace App\Services;

/**
 * TOTP RFC 6238 (compatível com Google Authenticator).
 */
class TotpService
{
    private const int PERIOD = 30;

    private const int DIGITS = 6;

    public function generateSecret(int $length = 16): string
    {
        $bytes = random_bytes($length);

        return $this->base32Encode($bytes);
    }

    public function getQrUri(string $secret, string $label, string $issuer = 'Precifique'): string
    {
        $encoded = rawurlencode($issuer.':'.$label);
        $secret = rawurlencode($secret);
        $issuer = rawurlencode($issuer);

        return "otpauth://totp/{$encoded}?secret={$secret}&issuer={$issuer}&digits=".self::DIGITS.'&period='.self::PERIOD;
    }

    public function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\s+/', '', $code) ?? '';
        if (! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $timestamp = time();
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->getCode($secret, $timestamp + ($i * self::PERIOD)), $code)) {
                return true;
            }
        }

        return false;
    }

    public function getCode(string $secret, ?int $timestamp = null): string
    {
        $timestamp ??= time();
        $counter = intdiv($timestamp, self::PERIOD);
        $secretKey = $this->base32Decode($secret);
        $binary = pack('N*', 0, $counter);
        $hash = hash_hmac('sha1', $binary, $secretKey, true);
        $offset = ord($hash[19]) & 0x0F;
        $value = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        return str_pad((string) ($value % (10 ** self::DIGITS)), self::DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        foreach (str_split($data) as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }
        $chunks = str_split($binary, 5);
        $output = '';
        foreach ($chunks as $chunk) {
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            }
            $output .= $alphabet[bindec($chunk)];
        }

        return $output;
    }

    private function base32Decode(string $secret): string
    {
        $secret = strtoupper(preg_replace('/\s+/', '', $secret) ?? '');
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        foreach (str_split($secret) as $char) {
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                continue;
            }
            $binary .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }
        $bytes = str_split($binary, 8);
        $output = '';
        foreach ($bytes as $byte) {
            if (strlen($byte) < 8) {
                break;
            }
            $output .= chr(bindec($byte));
        }

        return $output;
    }
}
