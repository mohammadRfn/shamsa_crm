<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ActivationClientService
{
    protected string $relayBaseUrl = 'https://mohammadentertainment.ir';

    public function sendFingerprint(string $rawFingerprint, ?string $note = null): ?string
    {
        $uuid = (string) Str::uuid();

        try {
            $response = Http::timeout(10)->post("{$this->relayBaseUrl}/activation-requests", [
                'request_uuid'    => $uuid,
                'raw_fingerprint' => $rawFingerprint,
                'customer_note'   => $note,
            ]);

            if ($response->successful()) {
                return $uuid;
            }
        } catch (\Throwable $e) {
            // سرور در دسترس نیست - در فرانت پیام مناسب نشون داده میشه
        }

        return null;
    }

    public function checkStatus(string $requestUuid): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->relayBaseUrl}/activation-requests/{$requestUuid}/status");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            return ['status' => 'unreachable'];
        }

        return ['status' => 'unreachable'];
    }
}
