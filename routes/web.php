<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArusKasController;
use App\Http\Controllers\AuditorController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\JurnalUmumController;
use App\Http\Controllers\AkuntanUnitController;
use App\Http\Controllers\LogActivityController;
use App\Http\Controllers\NeracaSaldoController;
use App\Http\Controllers\KategoriAkunController;
use App\Http\Controllers\AkuntanDivisiController;
use App\Http\Controllers\SubKategoriAkunController;
use App\Http\Controllers\PerubahanAsetNetoController;
use App\Http\Controllers\LaporanKomprehensifController;

// Authentication
// Route::get('/login', function () {
//     return view('login');
// });
// Tampilkan halaman login
Route::get('/login', [AuthController::class, 'login_form'])->name('login');

// Proses login
Route::post('/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');




Route::get('/register', function () {
    return view('register');
});





// Akuntan
Route::get('/', function () {
    return view('index');
})->middleware('auth');


Route::get('/dashboard', function () {
    return view('index');
});


// Admin
Route::get('/admin', function () {
    return view('/admin/index');
});


// Kategori Akun
Route::get('/kategori-akun', [KategoriAkunController::class, 'index'])->name('kategori-akun.index');
Route::post('/kategori-akun', [KategoriAkunController::class, 'store'])->name('kategori-akun.store');
Route::put('/kategori-akun', [KategoriAkunController::class, 'update'])->name('kategori-akun.update');
Route::delete('/kategori-akun', [KategoriAkunController::class, 'destroy'])->name('kategori-akun.destroy');


// Sub Kategori Akun
Route::get('/sub-kategori-akun', [SubKategoriAkunController::class, 'index'])->name('sub-kategori-akun.index');
Route::post('/sub-kategori-akun', [SubKategoriAkunController::class, 'store'])->name('sub-kategori-akun.store');
Route::put('/sub-kategori-akun', [SubKategoriAkunController::class, 'update'])->name('sub-kategori-akun.update');
Route::delete('/sub-kategori-akun', [SubKategoriAkunController::class, 'destroy'])->name('sub-kategori-akun.destroy');


// Akun
Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
Route::post('/akun', [AkunController::class, 'store'])->name('akun.store');
Route::put('/akun', [AkunController::class, 'update'])->name('akun.update');
Route::delete('/akun', [AkunController::class, 'destroy'])->name('akun.destroy');





// log-aktivitas
Route::get('/log-aktivitas', [LogActivityController::class, 'index'])->name('log-aktivitas.index');





// User 
Route::get('/admin/buat-akun', [UserController::class, 'register_form'])
    ->middleware('role:admin')
    ->name('user.register_form');



//akuntan divisi
Route::get('/akuntan-divisi', [AkuntanDivisiController::class, 'index'])->name('akuntan-divisi.index');
Route::post('/register-akuntan-divisi', [AkuntanDivisiController::class, 'store']);
Route::get('/akuntan-divisi/{id}', [AkuntanDivisiController::class, 'edit'])->name('akuntan-divisi.edit');
Route::put('/akuntan-divisi/{id}', [AkuntanDivisiController::class, 'update'])->name('akuntan-divisi.update');
Route::delete('/akuntan-divisi/{id}', [AkuntanDivisiController::class, 'destroy'])->name('akuntan-divisi.destroy');


//akuntan unit
Route::get('/akuntan-unit', [AkuntanUnitController::class, 'index'])->name('akuntan-unit.index');
Route::post('/register-akuntan-unit', [AkuntanUnitController::class, 'store'])->name('register.akuntan.unit');
Route::get('/akuntan-unit/{id}', [AkuntanUnitController::class, 'edit'])->name('akuntan-unit.edit');
Route::put('/akuntan-unit/{id}', [AkuntanUnitController::class, 'update'])->name('akuntan-unit.update');
Route::delete('/akuntan-unit/{id}', [AkuntanUnitController::class, 'destroy'])->name('akuntan-unit.destroy');


//auditor
Route::post('/register-auditor', [AuditorController::class, 'store'])->name('register.auditor');
Route::get('/auditor', [AuditorController::class, 'index'])->name('auditor.index');
Route::get('/auditor/{id}', [AuditorController::class, 'edit'])->name('auditor.edit');
Route::put('/auditor/{id}', [AuditorController::class, 'update'])->name('auditor.update');
Route::delete('/auditor/{id}', [AuditorController::class, 'destroy'])->name('auditor.destroy');



















// Transaksi
Route::get('/input-transaksi', [JurnalUmumController::class, 'create']); // Hlaman Input Transaksi
Route::post('/jurnal-umum', [JurnalUmumController::class, 'store']);;
Route::get('/jurnal-umum/{id}', [JurnalUmumController::class, 'edit']);
Route::put('/jurnal-umum/{id}', [JurnalUmumController::class, 'update']);
Route::delete('/jurnal-umum/{id}', [JurnalUmumController::class, 'destroy'])->name('jurnal-umum.destroy');





//Pencatatan
Route::get('/jurnal-umum', [JurnalUmumController::class, 'index'])->name('jurnal-umum.index'); //Halaman Jurnal Umum

Route::get('/buku-besar', [BukuBesarController::class, 'index'])->name('buku-besar.index');
Route::post('/buku-besar', [BukuBesarController::class, 'store'])->name('buku-besar.store');
Route::post('/buku-besar/posting-semua', [BukuBesarController::class, 'postingSemua'])->name('buku-besar.postingSemua');






// laporan
Route::get('/neraca-saldo', [NeracaSaldoController::class, 'index'])->name('neraca-saldo.index'); //Halaman Neraca Saldo
Route::get('/laporan-komprehensif', [LaporanKomprehensifController::class, 'index'])->name('laporan-komprehensif.index'); //Halaman Laporan Komprehensif
Route::get('/arus-kas', [ArusKasController::class, 'index'])->name('arus-kas.index'); //Halaman Arus kas
Route::get('/perubahan-aset-neto', [PerubahanAsetNetoController::class, 'index'])->name('perubahan-aset-neto.index');









// Admin
// Route::get('/admin/buat-akun', function () {
//     return view('/admin/buat-akun');
// });

// Route::get('/admin/buat-akun', [UserController::class, 'register_form'])->name('user.register_form'); //Halaman Jurnal Umum
