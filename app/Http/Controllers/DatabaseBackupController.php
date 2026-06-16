<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupController extends Controller
{
    /**
     * Export کامل دیتابیس به فایل SQL
     */
    public function export(): StreamedResponse
    {
        $dbName   = config('database.connections.mysql.database');
        $dbUser   = config('database.connections.mysql.username');
        $dbPass   = config('database.connections.mysql.password');
        $dbHost   = config('database.connections.mysql.host');
        $dbPort   = config('database.connections.mysql.port', 3306);

        $filename = 'backup_' . now()->format('Y-m-d_H-i') . '.sql';

        // بررسی وجود mysqldump
        $mysqldump = $this->findMysqldump();

        if ($mysqldump) {
            return $this->exportViaMysqldump($mysqldump, $dbHost, $dbPort, $dbUser, $dbPass, $dbName, $filename);
        }

        // Fallback: export دستی از طریق PHP/PDO
        return $this->exportViaPDO($filename);
    }

    /**
     * Import فایل SQL آپلود شده
     */
    public function import(Request $request)
    {
        $request->validate([
            'sql_file' => [
                'required',
                'file',
                'mimes:sql,txt',
                'max:102400', // حداکثر ۱۰۰MB
            ],
        ], [
            'sql_file.required'  => 'فایل SQL الزامی است.',
            'sql_file.mimes'     => 'فقط فایل‌های .sql یا .txt قابل قبول است.',
            'sql_file.max'       => 'حجم فایل نباید بیشتر از ۱۰۰ مگابایت باشد.',
        ]);

        $file    = $request->file('sql_file');
        $content = file_get_contents($file->getRealPath());

        if (empty(trim($content))) {
            return back()->with('backup_error', 'فایل SQL خالی است.');
        }

        // بررسی اولیه اینکه فایل SQL معتبر به نظر میرسه
        if (!$this->looksLikeSql($content)) {
            return back()->with('backup_error', 'فایل آپلود شده یک فایل SQL معتبر نیست.');
        }

        try {
            DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";');
            DB::unprepared('SET time_zone = "+00:00";');

            // اجرای statement به statement
            $statements = $this->splitSqlStatements($content);
            $executed   = 0;

            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    DB::unprepared($statement);
                    $executed++;
                }
            }

            DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');

            Log::info('Database import successful', [
                'user'       => auth()->user()->email,
                'statements' => $executed,
                'filename'   => $file->getClientOriginalName(),
            ]);

            return back()->with('backup_success', "ایمپورت با موفقیت انجام شد. ($executed دستور اجرا شد)");
        } catch (\Throwable $e) {
            DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');

            Log::error('Database import failed', [
                'user'  => auth()->user()->email,
                'error' => $e->getMessage(),
            ]);

            return back()->with('backup_error', 'خطا در اجرای فایل SQL: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    private function findMysqldump(): ?string
    {
        $candidates = [
            'mysqldump',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
        ];

        foreach ($candidates as $cmd) {
            $test = shell_exec('which ' . escapeshellarg($cmd) . ' 2>/dev/null');
            if (!empty(trim((string) $test))) {
                return $cmd;
            }
        }

        // ویندوز
        $winTest = shell_exec('where mysqldump 2>NUL');
        if (!empty(trim((string) $winTest))) {
            return 'mysqldump';
        }

        return null;
    }

    private function exportViaMysqldump(
        string $mysqldump,
        string $host,
        int|string $port,
        string $user,
        string $pass,
        string $dbName,
        string $filename
    ): StreamedResponse {

        $passwordFlag = !empty($pass) ? '--password=' . escapeshellarg($pass) : '';

        $cmd = sprintf(
            '%s --host=%s --port=%s --user=%s %s --single-transaction --routines --triggers --add-drop-table %s',
            escapeshellcmd($mysqldump),
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($user),
            $passwordFlag,
            escapeshellarg($dbName)
        );

        return response()->streamDownload(function () use ($cmd) {
            echo "-- Laravel Database Backup\n";
            echo "-- Generated: " . now()->toDateTimeString() . "\n";
            echo "-- Application: " . config('app.name') . "\n\n";

            passthru($cmd);
        }, $filename, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportViaPDO(string $filename): StreamedResponse
    {
        return response()->streamDownload(function () {
            $pdo    = DB::connection()->getPdo();
            $dbName = config('database.connections.mysql.database');

            echo "-- Laravel Database Backup (PDO)\n";
            echo "-- Generated: " . now()->toDateTimeString() . "\n";
            echo "-- Application: " . config('app.name') . "\n\n";
            echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // لیست جداول
            $tables = $pdo->query("SHOW TABLES FROM `{$dbName}`")->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                // CREATE TABLE
                $createRow = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
                $createSql = $createRow['Create Table'] ?? '';

                echo "DROP TABLE IF EXISTS `{$table}`;\n";
                echo $createSql . ";\n\n";

                // INSERT DATA (batch 500 ردیف)
                $stmt       = $pdo->query("SELECT * FROM `{$table}`");
                $rows       = [];
                $batchSize  = 500;

                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $values = array_map(function ($value) use ($pdo) {
                        if ($value === null) return 'NULL';
                        return $pdo->quote($value);
                    }, array_values($row));

                    $rows[] = '(' . implode(', ', $values) . ')';

                    if (count($rows) >= $batchSize) {
                        echo "INSERT INTO `{$table}` VALUES \n" . implode(",\n", $rows) . ";\n";
                        $rows = [];
                    }
                }

                if (!empty($rows)) {
                    echo "INSERT INTO `{$table}` VALUES \n" . implode(",\n", $rows) . ";\n";
                }

                echo "\n";
            }

            echo "SET FOREIGN_KEY_CHECKS=1;\n";
        }, $filename, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * تقسیم SQL به statement‌های جداگانه
     * با رعایت string literal و comment
     */
    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $current    = '';
        $inString   = false;
        $stringChar = '';
        $length     = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];

            // skip single-line comments
            if (!$inString && $char === '-' && isset($sql[$i + 1]) && $sql[$i + 1] === '-') {
                while ($i < $length && $sql[$i] !== "\n") {
                    $i++;
                }
                $current .= "\n";
                continue;
            }

            // skip block comments
            if (!$inString && $char === '/' && isset($sql[$i + 1]) && $sql[$i + 1] === '*') {
                $i += 2;
                while ($i < $length - 1 && !($sql[$i] === '*' && $sql[$i + 1] === '/')) {
                    $i++;
                }
                $i += 2;
                continue;
            }

            // toggle string mode
            if (($char === '"' || $char === "'") && !$inString) {
                $inString   = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar && ($i === 0 || $sql[$i - 1] !== '\\')) {
                $inString = false;
            }

            $current .= $char;

            // پایان statement
            if (!$inString && $char === ';') {
                $trimmed = trim($current);
                if (!empty($trimmed)) {
                    $statements[] = $trimmed;
                }
                $current = '';
            }
        }

        // اگه آخرین statement بدون semicolon بود
        $trimmed = trim($current);
        if (!empty($trimmed)) {
            $statements[] = $trimmed;
        }

        return $statements;
    }

    /**
     * بررسی اولیه اعتبار SQL
     */
    private function looksLikeSql(string $content): bool
    {
        $keywords = ['CREATE', 'INSERT', 'DROP', 'ALTER', 'SET', 'LOCK', 'UNLOCK', '--'];
        $upper    = strtoupper(substr($content, 0, 2000));

        foreach ($keywords as $kw) {
            if (str_contains($upper, $kw)) {
                return true;
            }
        }

        return false;
    }
}
