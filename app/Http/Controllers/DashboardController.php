<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\RMapi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 *
 */
class DashboardController extends Controller {
    /**
     * @param Request $request
     * @param RMapi $rmapi
     * @return JsonResponse
     */
    public function index(Request $request, RMapi $rmapi): JsonResponse {
        $path = $request->query('path') ?? '/';

        return response()->json([
            "items" => $rmapi->list($path),
            "cwd" => $path
        ]);
    }
}
