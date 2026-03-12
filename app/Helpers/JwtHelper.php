<?php

namespace App\Helpers;

class JwtHelper
{
    /**
     * Encode a payload into a JWT string using HMAC-SHA256.
     *
     * @param array $payload
     * @return string
     */
    public static function encode(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $segments = [];

        $segments[] = self::urlSafeEncode(json_encode($header));
        $segments[] = self::urlSafeEncode(json_encode($payload));

        $signing_input = implode('.', $segments);
        $signature = hash_hmac('sha256', $signing_input, env('JWT_SECRET', 'secret'), true);
        $segments[] = self::urlSafeEncode($signature);

        return implode('.', $segments);
    }

    /**
     * Decode a JWT string and return the payload if valid, else throw.
     *
     * @param string $token
     * @return array
     * @throws \Exception
     */
    public static function decode(string $token): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \Exception('Invalid token format');
        }

        list($header64, $payload64, $sig64) = $parts;

        $header = json_decode(self::urlSafeDecode($header64), true);
        $payload = json_decode(self::urlSafeDecode($payload64), true);
        $signature = self::urlSafeDecode($sig64);

        $valid = hash_hmac('sha256', $header64 . '.' . $payload64, env('JWT_SECRET', 'secret'), true);
        if (!hash_equals($valid, $signature)) {
            throw new \Exception('Signature verification failed');
        }

        // optional expiration check
        if (isset($payload['exp']) && time() >= $payload['exp']) {
            throw new \Exception('Token expired');
        }

        return $payload;
    }

    private static function urlSafeEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function urlSafeDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
