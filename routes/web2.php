<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SopController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PRRAController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArusKasController;
use App\Http\Controllers\AuditorController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\JurnalUmumController;
use App\Http\Controllers\AkuntanUnitController;
use App\Http\Controllers\LogActivityController;
use App\Http\Controllers\NeracaSaldoController;
use App\Http\Controllers\KategoriAkunController;
use App\Http\Controllers\AkuntanDivisiController;
use App\Http\Controllers\BudgetRapbsAkunController;
use App\Http\Controllers\SubKategoriAkunController;
use App\Http\Controllers\PerubahanAsetNetoController;
use App\Http\Controllers\BudgetRapbsKegiatanController;
use App\Http\Controllers\LaporanKomprehensifController;
use App\Http\Controllers\DashboardAdminController;

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
// Route::get('/admin', function () {
//     return view('admin.index');
// });
Route::get('/admin', [DashboardAdminController::class, 'index'])->name('admin-dashboard.index');


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
Route::post('/akun/import', [AkunController::class, 'import'])->name('akun.import');
Route::delete('/akun/reset', [AkunController::class, 'resetByUnit'])->name('akun.reset');

// Budget Rapbs AKun
Route::get('/budget-rapbs-akun', [BudgetRapbsAkunController::class, 'index'])->name('budget-rapbs-akun.index');
Route::post('/budget-rapbs-akun/store-or-update', [BudgetRapbsAkunController::class, 'storeOrUpdate'])->name('budget-rapbs-akun.storeOrUpdate');
Route::post('/budget-rapbs-akun/import', [BudgetRapbsAkunController::class, 'importExcel'])->name('budget-rapbs-akun.import');

// Budget Rapbs AKun
Route::get('/budget-rapbs-kegiatan', [BudgetRapbsKegiatanController::class, 'index'])->name('budget-rapbs-kegiatan.index');
Route::post('/budget-rapbs-kegiatan/store-or-update', [BudgetRapbsKegiatanController::class, 'storeOrUpdate'])->name('budget-rapbs-kegiatan.storeOrUpdate');
Route::post('/budget-rapbs-kegiatan/import', [BudgetRapbsKegiatanController::class, 'importExcel'])->name('budget-rapbs-kegiatan.import');



// Kegiatan
Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
Route::post('/kegiatan', [KegiatanController::class, 'store'])->name('kegiatan.store');
Route::put('/kegiatan', [KegiatanController::class, 'update'])->name('kegiatan.update');
Route::delete('/kegiatan', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');
Route::post('/kegiatan/import', [KegiatanController::class, 'import'])->name('kegiatan.import');
Route::delete('/kegiatan/reset', [KegiatanController::class, 'resetByUnit'])->name('kegiatan.reset');



// sop
Route::get('/sop', [SopController::class, 'index'])->name('sop.index');
Route::post('/sop', [SopController::class, 'store'])->name('sop.store');
Route::put('/sop/{id}', [SopController::class, 'update'])->name('sop.update');
Route::delete('/sop/{id}', [SopController::class, 'destroy'])->name('sop.destroy');
Route::post('/sop/sort', [SopController::class, 'sort'])->name('sop.sort');




// log-aktivitas
Route::get('/log-aktivitas', [LogActivityController::class, 'index'])->name('log-aktivitas.index');





// User 
Route::get('/admin/buat-akun', [UserController::class, 'register_form'])
    // ->middleware('role:admin')
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
Route::get('/akun-by-unit/{id}', [AkunController::class, 'akunByUnit']);

Route::post('/jurnal-umum', [JurnalUmumController::class, 'store']);;
Route::post('/jurnal-umum/import', [JurnalUmumController::class, 'import'])->name('jurnal-umum.import');
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
Route::get('/prra', [PRRAController::class, 'index'])->name('prra.index');









