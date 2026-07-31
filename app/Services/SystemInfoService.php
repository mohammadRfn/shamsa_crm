<?php

namespace App\Services;

class SystemInfoService
{
    protected function runPowerShell(string $command): string
    {
        $escaped = str_replace('"', '\"', $command);
        $output = shell_exec("powershell -NoProfile -Command \"{$escaped}\"");
        return trim($output ?? '');
    }

    public function getHostname(): string
    {
        return gethostname() ?: $this->runPowerShell('$env:COMPUTERNAME');
    }

    public function getOsInfo(): string
    {
        $caption = $this->runPowerShell('(Get-CimInstance -ClassName Win32_OperatingSystem).Caption');
        $version = $this->runPowerShell('(Get-CimInstance -ClassName Win32_OperatingSystem).Version');
        return trim("{$caption} ({$version})");
    }

    public function getWindowsUsername(): string
    {
        return $this->runPowerShell('$env:USERNAME') ?: (getenv('USERNAME') ?: 'unknown');
    }

    public function collect(): array
    {
        return [
            'hostname'         => $this->getHostname(),
            'os_info'          => $this->getOsInfo(),
            'windows_username' => $this->getWindowsUsername(),
        ];
    }
}
