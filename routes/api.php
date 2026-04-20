<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BerusahaStatistikApiController;
use App\Http\Controllers\Api\NonBerusahaStatistikApiController;
use App\Support\ApiTokenAbility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/login', [AuthApiController::class, 'login'])->name('api.auth.login');

Route::middleware(['auth:sanctum', 'abilities:'.ApiTokenAbility::AUTH_SESSION])->prefix('auth')->group(function () {
    Route::get('/me', [AuthApiController::class, 'me'])->name('api.auth.me');
    Route::post('/refresh', [AuthApiController::class, 'refresh'])->name('api.auth.refresh');
    Route::post('/logout', [AuthApiController::class, 'logout'])->name('api.auth.logout');
});

Route::middleware(['auth:sanctum', 'abilities:'.ApiTokenAbility::STATISTIK_NON_BERUSAHA_READ])->prefix('statistik/non-berusaha')->group(function () {
    Route::get('/simpel', [NonBerusahaStatistikApiController::class, 'simpel'])->name('api.statistik.non-berusaha.simpel');
    Route::get('/simbg', [NonBerusahaStatistikApiController::class, 'simbg'])->name('api.statistik.non-berusaha.simbg');
    Route::get('/pbg', [NonBerusahaStatistikApiController::class, 'simbg'])->name('api.statistik.non-berusaha.pbg');
    Route::get('/sicantik', [NonBerusahaStatistikApiController::class, 'sicantik'])->name('api.statistik.non-berusaha.sicantik');
    Route::get('/mppd', [NonBerusahaStatistikApiController::class, 'mppd'])->name('api.statistik.non-berusaha.mppd');
});

Route::middleware(['auth:sanctum', 'abilities:'.ApiTokenAbility::STATISTIK_BERUSAHA_READ])->prefix('statistik/berusaha')->group(function () {
    Route::get('/proyek', [BerusahaStatistikApiController::class, 'proyek'])->name('api.statistik.berusaha.proyek');
    Route::get('/nib', [BerusahaStatistikApiController::class, 'nib'])->name('api.statistik.berusaha.nib');
    Route::get('/izin', [BerusahaStatistikApiController::class, 'izin'])->name('api.statistik.berusaha.izin');
});
