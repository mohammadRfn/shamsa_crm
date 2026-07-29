<?php

namespace App\Http\Middleware;

use App\Services\FingerprintService;
use App\Services\LicenseVerifyService;
use Closure;
use Illuminate\Support\Facades\DB;

class CheckLicense
{
    protected array $except = [
        'license/activate',
        'license/waiting',
        'license/poll',
        'license/enter',
    ];

    public function handle($request, Closure $next)
    {
        foreach ($this->except as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        $license = DB::table('licenses')->orderByDesc('id')->first();

        if (!$license) {
            return redirect()->route('license.activate.form');
        }

        $fingerprint = app(FingerprintService::class)->generate();
        $verified = app(LicenseVerifyService::class)->verify($license->license_key, $fingerprint);

        if (!$verified) {
            return redirect()->route('license.activate.form')
                ->with('error', 'لایسنس نامعتبر است یا برای این سیستم صادر نشده.');
        }

        return $next($request);
    }
}
