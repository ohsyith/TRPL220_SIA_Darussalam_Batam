<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\JurnalUmumController;
use App\Http\Controllers\KategoriAkunController;
use App\Http\Controllers\SubKategoriAkunController;

// Authentication
Route::get('/login', function () {
    return view('login');
});

Route::get('/register', function () {
    return view('register');
});

// dahsboard
Route::get('/', function () {
    return view('index');
});

Route::get('/dashboard', function () {
    return view('index');
});


// Akun

Route::get('/kategori-akun', [KategoriAkunController::class, 'index'])->name('kategori-akun.index');

Route::get('/sub-kategori-akun', [SubKategoriAkunController::class, 'index'])->name('sub-kategori-akun.index');

Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');



// Transaksi
// Route::get('/input-transaksi', function () {
//     return view('input-transaksi');
// });

Route::get('/input-transaksi', [JurnalUmumController::class, 'create']);
Route::post('/store-jurnal_umum', [JurnalUmumController::class, 'store']);;


Route::get('/jurnal-umum', function () {
    return view('jurnal-umum');
});
