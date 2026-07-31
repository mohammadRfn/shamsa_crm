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
            'hostname'            => 'nullable|string|max:255',
            'os_info'             => 'nullable|string|max:255',
            'windows_username'    => 'nullable|string|max:255',
        ]);

        $fingerprint = $request->machine_fingerprint;
        $ip = $request->ip();
        $country = $this->lookupCountry($ip);

        $license = DB::table('issued_licenses')->where('machine_fingerprint', $fingerprint)->first();

        if (!$license) {
            $newId = DB::table('issued_licenses')->insertGetId([
                'machine_fingerprint' => $fingerprint,
                'customer_note'       => null,
                'hostname'            => $request->hostname,
                'os_info'             => $request->os_info,
                'windows_username'    => $request->windows_username,
                'auto_registered'     => 1,
                'revoked'             => 0,
                'last_seen_at'        => now(),
                'first_seen_at'       => now(),
                'last_ip'             => $ip,
                'last_country'        => $country,
                'created_at'          => now(),
            ]);

            return response()->json(['status' => 'active']);
        }

        DB::table('issued_licenses')->where('id', $license->id)->update([
            'last_seen_at'     => now(),
            'last_ip'          => $ip,
            'last_country'     => $country,
            'hostname'         => $request->hostname,
            'os_info'          => $request->os_info,
            'windows_username' => $request->windows_username,
            'first_seen_at'    => $license->first_seen_at ?? now(),
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
