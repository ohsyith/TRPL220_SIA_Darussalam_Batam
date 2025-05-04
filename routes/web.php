<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\JurnalUmumController;
use App\Http\Controllers\NeracaSaldoController;
use App\Http\Controllers\KategoriAkunController;
use App\Http\Controllers\SubKategoriAkunController;
use App\Http\Controllers\LaporanKomprehensifController;

// Authentication
Route::get('/login', [AuthController::class, 'login_form'])->name('login');

// Proses login
Route::post('/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
Route::post('/jurnal-umum', [JurnalUmumController::class, 'store']);;


//Pencatatan
Route::get('/jurnal-umum', [JurnalUmumController::class, 'index'])->name('jurnal-umum.index'); //Halaman Jurnal Umum

Route::get('/buku-besar', [BukuBesarController::class, 'index'])->name('buku-besar.index');

Route::get('/neraca-saldo', [NeracaSaldoController::class, 'index'])->name('neraca-saldo.index'); //Halaman Neraca Saldo



// laporan
Route::get('/laporan-komprehensif', [LaporanKomprehensifController::class, 'index']);
