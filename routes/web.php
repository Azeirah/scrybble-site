<?php

use App\Http\Controllers\ConnectedGumroadLicenseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GumroadPurchasedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OnboardingStateController;
use App\Http\Controllers\OnetimecodeController;
use App\Http\Controllers\RoadmapController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index']);

Route::middleware(['middleware' => 'auth:sanctum'])->get('/sanctum/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth']], static function () {
    Route::get('/app/', [DashboardController::class, 'index'])
         ->name('dashboard');

    Route::get('/purchased', [GumroadPurchasedController::class, 'index'])->name('gumroad-purchased');

    Route::get('/file', [FileController::class, 'show'])
         ->name('download');

    Route::post(
        '/onetimecode',
        [OnetimecodeController::class, 'create']);

    Route::post(
        '/connect-license',
        [ConnectedGumroadLicenseController::class, 'store']
    )->name('connect-license');
});

Route::group(['middleware' => ['auth'], 'prefix' => "api"], static function () {
    Route::get('onboardingState', OnboardingStateController::class);

    Route::post('gumroadLicense', [ConnectedGumroadLicenseController::class, "store"]);
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::fallback([HomeController::class, 'index']);
