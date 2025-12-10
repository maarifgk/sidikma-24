<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\TunggakanController;
use App\Http\Controllers\AplikasiController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SnapController;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('backend.auth.login'));
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'login_action'])->name('login.action');
Route::get('/forgetPassword', [AuthController::class, 'forgetPassword'])->name('forgetPassword');
Route::post('/forgetPassword/action', [AuthController::class, 'forgetPasswordAction'])->name('forgetPasswordAction');
Route::get('/resetPassword/{token}', [AuthController::class, 'resetPassword'])->name('resetPassword');
Route::post('/resetPassword/action', [AuthController::class, 'resetPasswordAction'])->name('resetPasswordAction');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED AREA
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD & LOGOUT
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/add', [AdminController::class, 'add'])->name('add');
        Route::post('/add', [AdminController::class, 'addProses'])->name('addProses');
        Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('edit');
        Route::post('/edit', [AdminController::class, 'editProses'])->name('editProses');
        Route::get('/delete/{id}', [AdminController::class, 'delete'])->name('delete');
    });

    /*
    |--------------------------------------------------------------------------
    | SISWA
    |--------------------------------------------------------------------------
    */
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/', [SiswaController::class, 'index'])->name('index');
        Route::get('/add', [SiswaController::class, 'add'])->name('add');
        Route::post('/add', [SiswaController::class, 'addSiswa'])->name('addProses');
        Route::get('/edit/{id}', [SiswaController::class, 'edit'])->name('edit');
        Route::post('/edit', [SiswaController::class, 'editProses'])->name('editProses');
        Route::get('/delete/{id}', [SiswaController::class, 'delete'])->name('delete');
        Route::get('/alumni', [SiswaController::class, 'alumni'])->name('alumni');
        Route::get('/tunggakan/{id}', [SiswaController::class, 'tunggakan'])->name('tunggakan');
    });

    /*
    |--------------------------------------------------------------------------
    | TAHUN AJARAN
    |--------------------------------------------------------------------------
    */
    Route::prefix('tahun')->name('tahun.')->group(function () {
        Route::get('/', [TahunController::class, 'view'])->name('index');
        Route::get('/add', [TahunController::class, 'add'])->name('add');
        Route::post('/add', [TahunController::class, 'addProses'])->name('addProses');
        Route::get('/edit/{id}', [TahunController::class, 'edit'])->name('edit');
        Route::post('/edit', [TahunController::class, 'editProses'])->name('editProses');
        Route::get('/delete/{id}', [TahunController::class, 'delete'])->name('delete');
    });

    /*
    |--------------------------------------------------------------------------
    | TAGIHAN
    |--------------------------------------------------------------------------
    */
    Route::prefix('tagihan')->name('tagihan.')->group(function () {
        Route::get('/', [TagihanController::class, 'view'])->name('index');
        Route::get('/add', [TagihanController::class, 'add'])->name('add');
        Route::post('/add', [TagihanController::class, 'addProses'])->name('addProses');
        Route::get('/delete/{id}', [TagihanController::class, 'delete'])->name('delete');

        Route::get('/jenisPembayaran', [TagihanController::class, 'jenisPembayaran'])->name('jenisPembayaran');
        Route::get('/getSiswa', [TagihanController::class, 'getSiswa'])->name('getSiswa');
        Route::get('/search', [TagihanController::class, 'search'])->name('search');
    });

    /*
    |--------------------------------------------------------------------------
    | PEMBAYARAN
    |--------------------------------------------------------------------------
    */
    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/', [PembayaranController::class, 'view'])->name('index');
        Route::get('/search', [PembayaranController::class, 'search'])->name('search');
        Route::get('/spp/{id}', [PembayaranController::class, 'spp'])->name('spp');
        Route::get('/payment/{id}', [PembayaranController::class, 'payment'])->name('payment');
        Route::get('/siswaByKelas/{id}', [PembayaranController::class, 'siswaByKelas'])->name('siswaByKelas');
        Route::get('/deleteSpp/{id}', [PembayaranController::class, 'deleteSpp'])->name('deleteSpp');

        Route::post('/spp', [PembayaranController::class, 'sppAddProses'])->name('add.spp');
        Route::post('/payment', [PembayaranController::class, 'paymentAddProses'])->name('add.payment');
    });

    /*
    |--------------------------------------------------------------------------
    | KELAS
    |--------------------------------------------------------------------------
    */
    Route::prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/', [KelasController::class, 'view'])->name('index');
        Route::get('/add', [KelasController::class, 'add'])->name('add');
        Route::post('/add', [KelasController::class, 'addkelas'])->name('addProses');
        Route::get('/edit/{id}', [KelasController::class, 'edit'])->name('edit');
        Route::post('/edit', [KelasController::class, 'editProses'])->name('editProses');
        Route::get('/delete/{id}', [KelasController::class, 'delete'])->name('delete');

        Route::get('/move', [KelasController::class, 'movekelas'])->name('move');
        Route::get('/loadFrom', [KelasController::class, 'load_data_moveKelasFrom'])->name('loadFrom');
        Route::get('/loadTo', [KelasController::class, 'load_data_moveKelasTo'])->name('loadTo');
        Route::post('/move', [KelasController::class, 'moveproses'])->name('moveProses');
        Route::post('/back', [KelasController::class, 'backproses'])->name('backProses');

        Route::get('/lulus', [KelasController::class, 'lulus'])->name('lulus');
        Route::post('/lulus', [KelasController::class, 'lulusproses'])->name('lulusProses');
        Route::get('/loadLulus', [KelasController::class, 'load_data_lulus'])->name('loadLulus');
    });

    /*
    |--------------------------------------------------------------------------
    | JENIS PEMBAYARAN (MASTER DATA)
    |--------------------------------------------------------------------------
    */
    Route::prefix('jenisPembayaran')->name('jenisPembayaran.')->group(function () {
        Route::get('/', [JenisPembayaranController::class, 'view'])->name('index');
        Route::get('/add', [JenisPembayaranController::class, 'add'])->name('add');
        Route::post('/add', [JenisPembayaranController::class, 'addProses'])->name('addProses');
        Route::get('/edit/{id}', [JenisPembayaranController::class, 'edit'])->name('edit');
        Route::post('/edit', [JenisPembayaranController::class, 'editProses'])->name('editProses');
        Route::get('/delete/{id}', [JenisPembayaranController::class, 'delete'])->name('delete');
    });

    /*
    |--------------------------------------------------------------------------
    | INVOICE ✅
    |--------------------------------------------------------------------------
    */
    Route::prefix('invoice')->name('invoice.')->group(function () {
        Route::get('/', [InvoiceController::class, 'view'])->name('view');
        Route::get('/add/{id}', [InvoiceController::class, 'add'])->name('add');
    });

    /*
    |--------------------------------------------------------------------------
    | LAPORAN & TUNGGAKAN
    |--------------------------------------------------------------------------
    */
    Route::get('/laporan', [LaporanController::class, 'view'])->name('laporan');
    Route::get('/laporan/load_data', [LaporanController::class, 'load_data'])->name('laporan.load_data');
    Route::get('/laporan/excel', [LaporanController::class, 'cetakExcel'])->name('laporan.excel');
    Route::get('/laporan/excel/{id}', [LaporanController::class, 'cetakExcelById'])->name('laporan.excelById');

    Route::get('/tunggakan', [TunggakanController::class, 'view'])->name('tunggakan');
    Route::get('/tunggakan/load_data', [TunggakanController::class, 'load_data'])->name('tunggakan.load_data');
    Route::get('/tunggakan/pdf', [TunggakanController::class, 'cetakTunggakan'])->name('tunggakan.pdf');

    /*
    |--------------------------------------------------------------------------
    | APLIKASI, PROFILE, BROADCAST
    |--------------------------------------------------------------------------
    */
    Route::get('/aplikasi', [AplikasiController::class, 'view'])->name('aplikasi');
    Route::post('/aplikasi/edit', [AplikasiController::class, 'editProses'])->name('aplikasi.edit');

    Route::get('/profile', [ProfileController::class, 'view'])->name('profile');

    Route::get('/broadcast', [BroadcastController::class, 'view'])->name('broadcast');
    Route::get('/broadcast/sendMessage', [BroadcastController::class, 'sendMessage'])->name('broadcast.sendMessage');

    /*
    |--------------------------------------------------------------------------
    | MIDTRANS
    |--------------------------------------------------------------------------
    */
    Route::post('/midtrans/token', [SnapController::class, 'token'])->name('midtrans.token');
    Route::post('/midtrans/payment', [SnapController::class, 'payment'])->name('midtrans.payment');
});

/*
|--------------------------------------------------------------------------
| MAINTENANCE (DISABLE IN PRODUCTION)
|--------------------------------------------------------------------------
*/
Route::get('/clear-cache', fn () => Artisan::call('cache:clear').' cache cleared');
Route::get('/view-clear', fn () => Artisan::call('view:clear').' view cleared');
Route::get('/route-clear', fn () => Artisan::call('route:clear').' route cleared');
Route::get('/config-clear', fn () => Artisan::call('config:clear').' config cleared');
