<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LicenseHeartbeatController extends Controller
{
    public function ping(Request $request)
    {
        $request->validate([
            'machine_fingerprint' => 'required|string|max:255',
        ]);

        $fingerprint = $request->machine_fingerprint;
        $ip = $request->ip();
        $country = $this->lookupCountry($ip);

        $license = DB::table('issued_licenses')->where('machine_fingerprint', $fingerprint)->first();

        if (!$license) {
            return response()->json(['status' => 'unknown']);
        }

        DB::table('issued_licenses')->where('id', $license->id)->update([
            'last_seen_at' => now(),
            'last_ip'      => $ip,
            'last_country' => $country,
        ]);

        return response()->json([
            'status' => $license->revoked ? 'revoked' : 'active',
        ]);
    }

    protected function lookupCountry(string $ip): ?string
    {
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            return 'Local';
        }

        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,country',
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                return $response->json('country');
            }
        } catch (\Throwable $e) {
        }

        return null;
    }
}
