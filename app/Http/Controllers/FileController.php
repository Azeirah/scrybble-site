<?php

namespace App\Http\Controllers;

use App\Services\RMapi;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function show(Request $request, RMapi $RMapi) {
        $file = $request->query('path');

        $RMapi->get($file);
    }
}
