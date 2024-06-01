<?php
declare(strict_types=1);

use App\Http\Controllers\SyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

$syncMiddleware = [];
if (config('scrybble.deployment_environment') !== 'self-hosted') {
  $syncMiddleware []= "auth:api";
}
Route::group(['middleware' => $syncMiddleware], static function () {
    Route::get('sync/delta', [SyncController::class, 'index']);
});
