<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTechnician
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'technician') {
            return redirect()->route('dashboard')
                ->with('error', 'فقط تکنسین‌ها اجازه دسترسی دارند.');
        }

        return $next($request);
    }
}