<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GoogleIdTokenVerifier
{
    /**
     * @return array<string, mixed>|null
     */
    public function verify(string $idToken, string $expectedNonce): ?array
    {
        $segments = explode('.', $idToken);
        if (count($segments) !== 3) {
            return null;
        }

        $header = $this->decodeJsonSegment($segments[0]);
        $claims = $this->decodeJsonSegment($segments[1]);
        $signature = $this->decodeBase64Url($segments[2]);

        if (! $header || ! $claims || $signature === null || ($header['alg'] ?? null) !== 'RS256') {
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

        $audience = $claims['aud'] ?? null;
        $allowedAudience = config('services.google.client_id');
        $audiences = is_array($audience) ? $audience : [$audience];

        if (! is_string($allowedAudience) || $allowedAudience === '' || ! in_array($allowedAudience, $audiences, true)) {
            return null;
        }

        if (! in_array($claims['iss'] ?? null, ['accounts.google.com', 'https://accounts.google.com'], true)) {
            return null;
        }

        if (! is_numeric($claims['exp'] ?? null) || (int) $claims['exp'] <= now()->timestamp) {
            return null;
        }

        if (! is_string($claims['sub'] ?? null)
            || ! filter_var($claims['email'] ?? null, FILTER_VALIDATE_EMAIL)
            || ($claims['email_verified'] ?? false) !== true
            || ! is_string($claims['nonce'] ?? null)
            || ! hash_equals($expectedNonce, $claims['nonce'])) {
            return null;
        }

        return $claims;
    }

    private function publicKeyFor(string $keyId): ?string
    {
        $keys = Cache::remember('google_openid_jwks', now()->addHour(), function (): array {
            $response = Http::timeout(5)->get('https://www.googleapis.com/oauth2/v3/certs');

            $keys = $response->successful() ? $response->json('keys') : [];

            return is_array($keys) ? $keys : [];
        });

        foreach ($keys as $key) {
            if (($key['kid'] ?? null) === $keyId && ($key['kty'] ?? null) === 'RSA') {
                return $this->jwkToPem($key);
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $key
     */
    private function jwkToPem(array $key): ?string
    {
        $modulus = isset($key['n']) ? $this->decodeBase64Url($key['n']) : null;
        $exponent = isset($key['e']) ? $this->decodeBase64Url($key['e']) : null;

        if ($modulus === null || $exponent === null) {
            return null;
        }
        if ($modulus === '' || $exponent === '') {
            return null;
        }

        $rsaPublicKey = $this->asn1Sequence(
            $this->asn1Integer($modulus).$this->asn1Integer($exponent)
        );
        $algorithmIdentifier = hex2bin('300d06092a864886f70d0101010500');
        $subjectPublicKeyInfo = $this->asn1Sequence(
            $algorithmIdentifier.$this->asn1BitString($rsaPublicKey)
        );

        return "-----BEGIN PUBLIC KEY-----\n".chunk_split(base64_encode($subjectPublicKeyInfo), 64, "\n")."-----END PUBLIC KEY-----\n";
    }

    private function asn1Integer(string $value): string
    {
        if ((ord($value[0]) & 0x80) !== 0) {
            $value = "\0".$value;
        }

        return $this->asn1Element(0x02, $value);
    }

    private function asn1BitString(string $value): string
    {
        return $this->asn1Element(0x03, "\0".$value);
    }

    private function asn1Sequence(string $value): string
    {
        return $this->asn1Element(0x30, $value);
    }

    private function asn1Element(int $tag, string $value): string
    {
        $length = strlen($value);
        if ($length < 128) {
            $encodedLength = chr($length);
        } else {
            $lengthBytes = '';
            while ($length > 0) {
                $lengthBytes = chr($length & 0xff).$lengthBytes;
                $length >>= 8;
            }
            $encodedLength = chr(0x80 | strlen($lengthBytes)).$lengthBytes;
        }

        return chr($tag).$encodedLength.$value;
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
