<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VisitedController;
use App\Http\Controllers\RekomendasiController;

// ====================================================
// 1. LANDING PAGE (Halaman Depan)
// ====================================================
// Saat user membuka website (127.0.0.1:8000), tampilkan file 'welcome.blade.php'
// 1. LANDING PAGE (Halaman Depan)
Route::get('/', function () {
    
    $semuaFoto = [
        'tugu.jpeg',
        'kebun raya.jpeg',        
        'Bekantan.jpeg',    
        'manggasang.jpeg',    
        'bukit batu.jpeg',
        'pasar terapung.jpeg',
        'tahura.jpeg',
        'sabilal.jpeg',
    ];

    $slideshow = collect($semuaFoto)->shuffle()->take(4);

    return view('welcome', ['slides' => $slideshow]);
});

// ====================================================
// 2. APLIKASI UTAMA (Peta & Pencarian)
// ====================================================
// Logika peta kita pindahkan ke alamat '/app'
// Kita beri nama 'app.peta' supaya mudah dipanggil di form & tombol
Route::get('/app', [RekomendasiController::class, 'cari'])->name('app.peta');

// ====================================================
// 3. DETAIL WISATA
// ====================================================
Route::get('/wisata/{id}', [RekomendasiController::class, 'show'])->name('wisata.detail');

// ====================================================
// 4. JALUR ADMIN (VERSI BENAR)
// ====================================================

// Dashboard
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

// Create (Tampil Form & Simpan)
Route::get('/admin/create', [AdminController::class, 'create'])->name('admin.create');
Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store');

// Edit (Tampil Form & Update)
Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
Route::put('/admin/update/{id}', [AdminController::class, 'update'])->name('admin.update');

// Delete (PERBAIKAN PENTING DI SINI)
// Wajib pakai 'delete', bukan 'get'. Dan namanya 'admin.destroy'
Route::delete('/admin/delete/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');

Route::post('/review/store', [App\Http\Controllers\ReviewController::class, 'store'])->name('review.store');

Route::post('/visit/toggle/{id}', [VisitedController::class, 'toggle']);
// Route Simpan Catatan (POST)
Route::post('/visit/save-note/{id}', [VisitedController::class, 'saveNote']);
Route::get('/visit/get-data', [VisitedController::class, 'getVisitedData']);

Route::get('/admin/statistik', [AdminController::class, 'stats'])->name('admin.stats');