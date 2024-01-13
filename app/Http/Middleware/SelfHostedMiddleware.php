<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SelfHostedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') !== 'local') {
            abort(403, "Unauthorized action");
        }
        return $next($request);
    }
}
