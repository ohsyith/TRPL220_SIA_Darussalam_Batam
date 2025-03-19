<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\BukuBesarController;
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
Route::get('/input-transaksi', [JurnalUmumController::class, 'create']); // Hlaman Input Transaksi
Route::post('/store-jurnal_umum', [JurnalUmumController::class, 'store']);;


//Pencatatan
Route::get('/jurnal-umum', [JurnalUmumController::class, 'index'])->name('jurnal-umum.index'); //Halaman Jurnal Umum

Route::get('/buku-besar', [BukuBesarController::class, 'index'])->name('buku-besar.index');

