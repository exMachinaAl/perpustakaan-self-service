<?php

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

Route::post('/v1/reader/scan', [\App\Http\Controllers\ReaderScanController::class,'scan'])
    ->middleware(['reader.signature','throttle:60,1']);

// PATCH 002: Heartbeat perangkat. Tidak mengungkapkan anggota maupun kredensial.
Route::post('/v1/reader/heartbeat', [\App\Http\Controllers\ReaderHeartbeatController::class,'store'])
    ->middleware(['reader.signature','throttle:12,1']);

// PATCH 003: diagnostic events signed with the SAME HMAC middleware; never accept UID here.
Route::post('/v1/reader/diagnostic', [\App\Http\Controllers\ReaderDiagnosticController::class, 'store'])
    ->middleware(['reader.signature','throttle:20,1']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// PATCH 004: Optional ESP32-CAM QR bridge; registered HMAC reader only.
Route::post('/v1/kiosk/qr', [\App\Http\Controllers\KioskOmnichannelController::class,'deviceQr'])
    ->middleware(['reader.signature','throttle:30,1']);
