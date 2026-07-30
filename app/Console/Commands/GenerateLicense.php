<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateLicense extends Command
{
    protected $signature = 'license:generate {request_id}';
    protected $description = 'تولید لایسنس امضاشده برای یک درخواست تایید شده';

    public function handle()
    {
        $id = $this->argument('request_id');
        $row = DB::table('activation_requests')->find($id);

        if (!$row) {
            $this->error('درخواست پیدا نشد.');
            return 1;
        }

        if ($row->status !== 'approved') {
            $this->error('این درخواست هنوز approved نشده.');
            return 1;
        }

        $privateKeyB64 = env('LICENSE_PRIVATE_KEY');

        if (!$privateKeyB64) {
            $this->error('LICENSE_PRIVATE_KEY در .env تنظیم نشده.');
            return 1;
        }

        $payload = json_encode([
            'machine_fingerprint' => $row->raw_fingerprint,
            'customer_note'       => $row->customer_note,
            'issued_at'           => now()->toDateTimeString(),
        ]);

        $signed = sodium_crypto_sign($payload, base64_decode($privateKeyB64));
        $licenseKey = base64_encode($signed);

        DB::table('issued_licenses')->updateOrInsert(
            ['machine_fingerprint' => $row->raw_fingerprint],
            [
                'activation_request_id' => $row->id,
                'customer_note'         => $row->customer_note,
                'revoked'               => 0,
                'created_at'            => now(),
            ]
        );

        $this->info('کد لایسنس نهایی (این رو برای مشتری بفرست):');
        $this->newLine();
        $this->line($licenseKey);
        $this->newLine();

        return 0;
    }
}
