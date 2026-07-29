<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateLicense extends Command
{
    protected $signature = 'license:generate {request_id}';
    protected $description = 'تولید لایسنس امضاشده بر اساس درخواست تایید شده';

    public function handle()
    {
        $id = $this->argument('request_id');
        $row = DB::table('activation_requests')->find($id);

        if (!$row || $row->status !== 'approved') {
            $this->error('درخواست پیدا نشد یا تایید نشده است.');
            return 1;
        }

        // Private Key رو از یه محل امن بخون (نه داخل کد، نه در git)
        // مثلا از یه فایل خارج از public/ یا از .env
        $privateKeyB64 = env('LICENSE_PRIVATE_KEY');

        $payload = json_encode([
            'machine_fingerprint' => $row->raw_fingerprint,
            'customer_note'       => $row->customer_note,
            'issued_at'           => now()->toDateTimeString(),
        ]);

        $signed = sodium_crypto_sign($payload, base64_decode($privateKeyB64));
        $licenseKey = base64_encode($signed);

        $this->info('کد لایسنس نهایی (این رو برای مشتری بفرست):');
        $this->line($licenseKey);

        return 0;
    }
}