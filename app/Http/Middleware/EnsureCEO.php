<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCEO
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'ceo') {
            abort(403, 'فقط مدیر عامل اجازه دسترسی دارد.');
        }

        return $next($request);
    }
}