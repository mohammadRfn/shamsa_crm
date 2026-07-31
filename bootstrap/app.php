<?php

use App\Http\Middleware\CheckLicense;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureApprover;
use App\Http\Middleware\EnsureCEO;
use App\Http\Middleware\EnsureTechnician;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'checkrole' => CheckRole::class,
            'technician' => EnsureTechnician::class,
            'approver' => EnsureApprover::class,
            'ceo' => EnsureCEO::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'activation-requests',
            'activation-requests/*',
            'license-heartbeat',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
