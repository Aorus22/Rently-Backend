<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\LokasiGarasiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\KendaraanController;
use App\Http\Controllers\API\PemesananController;
use App\Http\Controllers\API\PembayaranController;
use App\Http\Controllers\API\DynamicCrudController;
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

    // Dynamic CRUD
    Route::get('/admin/infotabeldong', [DynamicCrudController::class, 'fetchTableConfig']);
    Route::get('/admin/{table}', [DynamicCrudController::class, 'index']);
    Route::post('/admin/{table}', [DynamicCrudController::class, 'store']);
    Route::get('/admin/{table}/{id}', [DynamicCrudController::class, 'show']);
    Route::post('/admin/{table}/{id}', [DynamicCrudController::class, 'update']);
    Route::delete('/admin/{table}/{id}', [DynamicCrudController::class, 'destroy']);
});

Route::get('/kendaraan', [KendaraanController::class, 'index']);
Route::get('/kendaraan/{id}', [KendaraanController::class, 'show']);
Route::get('/garasi', [LokasiGarasiController::class, 'index']);
