<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SelfHostedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (config("scrybble.deployment_environment") === "self-hosted") {
            $user = User::query()->firstOrCreate(["name" => "Admin"], [ "email" => "", "password" => "Admin"]);
            Auth::login($user, ["remember" => true]);
        }
        return $next($request);
    }
}
