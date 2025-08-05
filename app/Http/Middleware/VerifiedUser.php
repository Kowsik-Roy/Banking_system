<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class verifieduser
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user()->is_verified) {
            return redirect()->route('verify.email.form');
        }

        return $next($request);
    }
}