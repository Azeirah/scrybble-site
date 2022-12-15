<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\RMapi;
use Eloquent\Pathogen\Exception\EmptyPathException;
use Illuminate\Http\Request;

/**
 *
 */
class FileController extends Controller {
    /**
     * @param Request $request
     * @param RMapi $RMapi
     * @return void
     * @throws EmptyPathException
     */
    public function show(Request $request, RMapi $RMapi): void {
        $file = $request->get('file');

        $RMapi->get($file);
    }
}
