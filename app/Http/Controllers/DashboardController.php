<?php

namespace App\Http\Controllers;

use App\Services\RMapi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(RMapi $rmapi) {
        return view('dashboard', [
            'isRmApiAuthenticated' => $rmapi->isAuthenticated()
        ]);
    }
}
