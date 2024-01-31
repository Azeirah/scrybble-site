<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->id === 1 || config('app.env') == "local") {
            return $next($request);
        }
        abort(403, "Unauthorized action");
    }
}
