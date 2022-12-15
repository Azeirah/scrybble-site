<?php

namespace App\Http\Controllers;

use App\Services\RMapi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RMFiletreeController extends Controller {

    public function index(Request $request, RMapi $rmapi): JsonResponse {
        $path = $request->get('path') ?? '/';

        return response()->json([
            "items" => $rmapi->list($path),
            "cwd" => $path
        ]);
    }
}
