<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\LokasiGarasiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\KendaraanController;
use App\Http\Controllers\API\PemesananController;
use App\Http\Controllers\API\PembayaranController;
use App\Models\Pembayaran;

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello World']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/adminlogin', [AdminController::class, 'login']);

// User (no middleware)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/pemesanan', [PemesananController::class, 'index']);
    Route::post('/pemesanan', [PemesananController::class, 'store']);
    Route::get('/pemesanan/{id}', [PemesananController::class, 'show']);
    Route::post('/pemesanan/{id}/cancel', [PemesananController::class, 'cancel']);
    Route::post('/pemesanan/{id}/bayar', [PembayaranController::class, 'createPayment']);
    Route::get('/pemesanan/{id}/pembayaran', [PemesananController::class, 'getPembayaranByPemesanan']);
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'getPaymentDetail']);
    Route::post('/pembayaran/{id}/upload-bukti', [PembayaranController::class, 'uploadBuktiPembayaran']);
});

// Admin (yes middleware)
Route::middleware(['auth:sanctum', 'admin'])->group(function() {
    // Admin
    Route::get('/admin/akusiapa', [AdminController::class, 'me']);
    Route::post('/admin/adminlogout', [AdminController::class, 'logout']);
    Route::get('/admin/getusers', [AuthController::class, 'getData']);
    Route::put('/admin/user/{id}', [AuthController::class, 'update']);

    // Riwayat Sewa Atmin
    Route::get('/admin/ceksewa', [PemesananController::class, 'getAllPemesanan']);
    Route::put('/admin/updatestatus/{id}', [PemesananController::class, 'updatePemesanan']);

    // CRUD Kendaraan
    Route::post('/admin/kendaraan', [KendaraanController::class, 'store']);
    Route::put('/admin/kendaraan/{id}', [KendaraanController::class, 'update']);
    Route::delete('/admin/kendaraan/{id}', [KendaraanController::class, 'destroy']);
});

Route::get('/kendaraan', [KendaraanController::class, 'index']);
Route::get('/kendaraan/{id}', [KendaraanController::class, 'show']);
Route::get('/garasi', [LokasiGarasiController::class, 'index']);
