<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OnetimecodeController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [HomeController::class, "index"]);


Route::group(['middleware' => ['auth']], static function () {
    Route::get('/dashboard/', [DashboardController::class, "index"])
         ->name('dashboard');

    Route::post(
        '/onetimecode',
        [OnetimecodeController::class, 'create']);
});

require __DIR__ . '/auth.php';
