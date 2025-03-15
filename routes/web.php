<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\KendaraanController;
use App\Http\Controllers\API\PemesananController;
use App\Http\Controllers\API\PembayaranController;
use App\Models\Pembayaran;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello World']);
});

// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/me', [AuthController::class, 'me']);
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/pemesanan', [PemesananController::class, 'index']);
//     Route::post('/pemesanan', [PemesananController::class, 'store']);
//     Route::get('/pemesanan/{id}', [PemesananController::class, 'show']);
//     Route::post('/pemesanan/{id}/cancel', [PemesananController::class, 'cancel']);
//     Route::post('/pemesanan/{id}/bayar', [PembayaranController::class, 'createPayment']);
//     Route::get('/pemesanan/{id}/pembayaran', [PemesananController::class, 'getPembayaranByPemesanan']);
//     Route::get('/pembayaran/{id}', [PembayaranController::class, 'getPaymentDetail']);
//     Route::post('/pembayaran/{id}/upload-bukti', [PembayaranController::class, 'uploadBuktiPembayaran']);
// });

// Route::get('/kendaraan', [KendaraanController::class, 'index']);
// Route::post('/kendaraan', [KendaraanController::class, 'store']);
// Route::get('/kendaraan/{id}', [KendaraanController::class, 'show']);
// Route::put('/kendaraan/{id}', [KendaraanController::class, 'update']);
// Route::delete('/kendaraan/{id}', [KendaraanController::class, 'destroy']);
