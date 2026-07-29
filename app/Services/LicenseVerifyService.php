<?php

namespace App\Services;

class LicenseVerifyService
{
    protected string $publicKeyB64 = 'PUBLIC_KEY_BASE64_شما_اینجا';

    public function verify(string $licenseKeyB64, string $currentFingerprint): ?array
    {
        try {
            $signed = base64_decode($licenseKeyB64, true);
            $publicKey = base64_decode($this->publicKeyB64, true);

            if ($signed === false || $publicKey === false) {
                return null;
            }

            $payload = sodium_crypto_sign_open($signed, $publicKey);

            if ($payload === false) {
                return null; 
            }

            $data = json_decode($payload, true);

            if (!isset($data['machine_fingerprint']) || $data['machine_fingerprint'] !== $currentFingerprint) {
                return null; 
            }

            if (isset($data['expires_at']) && now()->gt($data['expires_at'])) {
                return null; 
            }

            return $data;
        } catch (\Throwable $e) {
            return null;
        }
    }
}