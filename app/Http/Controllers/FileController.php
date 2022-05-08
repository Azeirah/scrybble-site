<?php

namespace App\Http\Controllers;

use App\Services\RMapi;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function show(RMapi $RMapi, string $file) {
        $RMapi->get($file);
    }
}
