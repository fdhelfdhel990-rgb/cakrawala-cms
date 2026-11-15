<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class FirebaseIdTokenVerifier
{
    /**
     * @return array<string, mixed>|null
     */
    public function verify(string $idToken): ?array
    {
        $segments = explode('.', $idToken);
        if (count($segments) !== 3) {
            return null;
        }

        $header = $this->decodeJsonSegment($segments[0]);
        $claims = $this->decodeJsonSegment($segments[1]);
        $signature = $this->decodeBase64Url($segments[2]);
        $projectId = config('services.firebase.project_id');

        if (! $header || ! $claims || $signature === null || ($header['alg'] ?? null) !== 'RS256'
            || ! is_string($projectId) || $projectId === '') {
            return null;
        }

        $keyId = $header['kid'] ?? null;
        if (! is_string($keyId) || $keyId === '') {
            return null;
        }

        $publicKey = $this->publicKeyFor($keyId);
        if ($publicKey === null || openssl_verify($segments[0].'.'.$segments[1], $signature, $publicKey, OPENSSL_ALGO_SHA256) !== 1) {
            return null;
        }

        $now = now()->timestamp;
        $audience = $claims['aud'] ?? null;

        if ($audience !== $projectId
            || ($claims['iss'] ?? null) !== 'https://securetoken.google.com/'.$projectId
            || ! is_numeric($claims['exp'] ?? null) || (int) $claims['exp'] <= $now
            || ! is_numeric($claims['iat'] ?? null) || (int) $claims['iat'] > $now
            || ! is_numeric($claims['auth_time'] ?? null) || (int) $claims['auth_time'] > $now
            || ! is_string($claims['sub'] ?? null) || $claims['sub'] === '' || strlen($claims['sub']) > 128
            || ! filter_var($claims['email'] ?? null, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $claims;
    }

    private function publicKeyFor(string $keyId): ?string
    {
        try {
            $keys = Cache::remember('firebase_securetoken_x509', now()->addHour(), function (): array {
                $response = Http::timeout(5)->get('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');

                return $response->successful() && is_array($response->json()) ? $response->json() : [];
            });
        } catch (ConnectionException) {
            return null;
        }

        $certificate = $keys[$keyId] ?? null;

        return is_string($certificate) && openssl_pkey_get_public($certificate) !== false ? $certificate : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJsonSegment(string $segment): ?array
    {
        $decoded = $this->decodeBase64Url($segment);
        if ($decoded === null) {
            return null;
        }

        $json = json_decode($decoded, true);

        return is_array($json) ? $json : null;
    }

    private function decodeBase64Url(string $value): ?string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/').str_repeat('=', (4 - strlen($value) % 4) % 4), true);

        return $decoded === false ? null : $decoded;
    }
}
