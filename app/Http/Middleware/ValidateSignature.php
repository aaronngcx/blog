<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ValidateSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! URL::hasValidSignature($request)) {
            abort(403, 'Invalid signature.');
        }

        return $next($request);
    }
}


