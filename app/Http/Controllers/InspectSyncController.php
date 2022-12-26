<?php

namespace App\Http\Controllers;

use App\Models\Sync;
use Illuminate\Support\Facades\Auth;

class InspectSyncController extends Controller {
    public function index() {
        $collection = Sync::select(['filename', 'created_at', 'completed'])
                          ->forUser(Auth::user())
                          ->orderByDesc("created_at")
                          ->limit(10)
                          ->get()
                          ->map(fn(Sync $syncItem) => [
                              'filename' => $syncItem->filename,
                              'created_at' => $syncItem->created_at->diffForHumans(),
                              'completed' => $syncItem->completed,
                              'error' => !$syncItem->completed
                                  && ($syncItem->isOld() || $syncItem->hasError())
                          ]);
        return response()->json(
            $collection
        );
    }
}
