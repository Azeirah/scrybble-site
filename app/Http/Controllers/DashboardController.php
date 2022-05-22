<?php

namespace App\Http\Controllers;

use App\Services\RMapi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, RMapi $rmapi) {
        $path = urldecode($request->query('path'));
        if ($path === '') {
            $path = "/";
        }

        $rmApiIsAuthenticated = $rmapi->isAuthenticated();

        return view('dashboard', [
            'isRmApiAuthenticated' => $rmApiIsAuthenticated,
            'ls' => $rmApiIsAuthenticated ? $rmapi->list($path) : [],
            'currentWorkingDirectory' => $path
        ]);
    }
}
