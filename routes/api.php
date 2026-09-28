<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\AlatController;  
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\LogAktivitasController;
use App\Http\Controllers\API\LaporanController;
use App\Http\Controllers\API\PeminjamanController;
use App\Http\Controllers\API\PengembalianController;

// Public Routes (Tidak perlu token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Wajib membawa Bearer Token dari Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role.admin')->group(function () {
    Route::apiResource('kategori', KategoriController::class);
    Route::apiResource('alat', AlatController::class);
    Route::get('/katalog', [AlatController::class, 'katalog']);
    Route::apiResource('users', UserController::class);
    Route::get('/peminjaman', [LaporanController::class, 'index']);
    Route::get('/peminjaman/{id}', [LaporanController::class, 'show']);
    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index']); 
    });

    Route::middleware('role.petugas')->group(function () {
        // Route untuk hak akses petugas
         Route::get('/katalog', [AlatController::class, 'katalog']);
         Route::get('/laporan-peminjaman', [LaporanController::class, 'index']); 
         Route::get('/peminjaman/{id}', [LaporanController::class, 'show']);
         Route::post('/pengembalian', [PengembalianController::class, 'store']);
         Route::post('/peminjaman/{id}/approve', [PeminjamanController::class, 'approve']);
    });

    Route::middleware('role.peminjam')->group(function () {
        // Route untuk hak akses peminjam
        Route::post('/peminjaman', [PeminjamanController::class, 'store']);
        Route::get('/riwayat-pinjam', [PeminjamanController::class, 'riwayat']);
        Route::get('/katalog', [AlatController::class, 'katalog']);
        
    });

});