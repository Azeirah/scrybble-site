<?php

namespace App\Http\Controllers;

use App\Models\Sync;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class InspectSyncController extends Controller {
    public function index() {
        $grace_period_in_minutes = 5;
        $collection = Sync::select(['filename', 'created_at', 'completed'])
                          ->forUser(Auth::user())
                          ->limit(10)
                          ->get()
                          ->map(fn(Sync $syncItem) => [
                              'filename' => $syncItem->filename,
                              'created_at' => $syncItem->created_at->diffForHumans(),
                              'completed' => $syncItem->completed,
                              'error' => !$syncItem->completed
                                  && (Carbon
                                          ::now()
                                          ->addMinutes($grace_period_in_minutes)
                                          ->lessThan($syncItem->created_at) ||
                                      $syncItem->logs()->where('severity', 'error')->count() > 0)
                          ]);
        return response()->json(
            $collection
        );
    }
}
