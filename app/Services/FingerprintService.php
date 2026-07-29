<?php

namespace App\Services;

class FingerprintService
{
    protected string $appSecret = 'یک-رشته-طولانی-ثابت-و-غیرقابل‌حدس-مخصوص-اپ-شما';

    public function getRawComponents(): array
    {
        $board = trim(shell_exec('wmic baseboard get serialnumber') ?? '');
        $cpu   = trim(shell_exec('wmic cpu get processorid') ?? '');
        $disk  = trim(shell_exec('wmic diskdrive get serialnumber') ?? '');

        // پاکسازی خروجی wmic (حذف هدر ستون و فاصله‌های اضافه)
        $clean = fn ($val) => preg_replace('/[^A-Za-z0-9]/', '', str_replace(['SerialNumber', 'ProcessorId'], '', $val));

        return [
            'board' => $clean($board),
            'cpu'   => $clean($cpu),
            'disk'  => $clean($disk),
        ];
    }

    public function generate(): string
    {
        $c = $this->getRawComponents();

        $combined = hash('sha256', $c['board'] . $this->appSecret);
        $combined = hash('sha256', $combined . $c['cpu']);
        $combined = hash('sha256', $combined . $c['disk'] . $this->appSecret);

        return $combined;
    }
}