<?php

namespace App\Services;

use App\Exceptions\MissingRMApiAuthenticationTokenException;
use Illuminate\Support\Facades\Auth;

class OnboardingStateService
{
    public function __construct(public RMapi $rmapi)
    {
    }

    public function getState(): string
    {
        $user = Auth::user();

        if (!$user) {
            return "unauthenticated";
        }

        if (!$user->gumroadLicense()->exists()) {
            return "setup-gumroad";
        }

        try {
            if (!$this->rmapi->isAuthenticated()) {
                return "setup-one-time-code";
            }
        } catch (MissingRMApiAuthenticationTokenException $e) {
            return "setup-one-time-code-again";
        }

        return "ready";
    }

}
