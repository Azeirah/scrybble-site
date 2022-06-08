<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\RMapi;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 *
 */
class DashboardController extends Controller
{
    /**
     * @param Request $request
     * @param RMapi $rmapi
     * @return Factory|View|Application
     */
    public function index(Request $request, RMapi $rmapi): Factory|View|Application {
        $path = '/';
        if ($request->has('path')) {
            $path = $request->query('path');
        }

        $rm_api_is_authenticated = $rmapi->isAuthenticated();

        return view('dashboard', [
            'isRmApiAuthenticated' => $rm_api_is_authenticated,
            'ls' => $rm_api_is_authenticated ? $rmapi->list($path) : [],
            'currentWorkingDirectory' => $path
        ]);
    }
}
