<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprover
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRoles = ['reception', 'supply', 'ceo'];
        
        if (!auth()->check() || !in_array(auth()->user()->role, $allowedRoles)) {
            return redirect()->route('dashboard')
                ->with('error', 'شما اجازه تایید/رد ندارید.');
        }

        return $next($request);
    }
}