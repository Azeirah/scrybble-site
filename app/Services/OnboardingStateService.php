<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class OnboardingStateService {
    public function __construct(public RMapi $rmapi) {}

    public function getState(): string {
        $user = Auth::user();

        if (!$user) {
            return "unauthenticated";
        }

        if (!$user->gumroadLicense()->exists()) {
            return "setup-gumroad";
        }

        if (!$this->rmapi->isAuthenticated()) {
            return "setup-one-time-code";
        }

        return "ready";
    }

}
