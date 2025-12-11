<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SnapController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TenagaController;
use App\Http\Controllers\KesiswaanController;
use App\Http\Controllers\SarprasController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\AplikasiController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TunggakanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileLembagaController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\IdentitasController;
use App\Http\Controllers\StrukturController;
use App\Http\Controllers\SkController;
use App\Http\Controllers\UsulanController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\BatikMaarifController;
use App\Http\Controllers\ProfileSekolahController;
use App\Http\Controllers\PersuratanController;
use App\Http\Controllers\AktivasiController;
use App\Http\Controllers\SkJanuariController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UpdateSipinterController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\LaporanTahunanController;
use App\Http\Controllers\AgendaKesekretariatanController;
use App\Http\Controllers\BendaharaController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\UploadSKController;
use App\Http\Controllers\ModulController;
use App\Http\Controllers\DataSiswaController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('backend.auth.login');
});
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'login_action'])->name('login.action');
Route::get('/forgetPassword', [AuthController::class, 'forgetPassword'])->name('forgetPassword');
Route::post('/forgetPassword/action', [AuthController::class, 'forgetPasswordAction'])->name('forgetPasswordAction');
Route::get('/resetPassword/{token}', [AuthController::class, 'resetPassword'])->name('resetPassword');
Route::post('/resetPassword/action', [AuthController::class, 'resetPasswordAction'])->name('resetPasswordAction');



Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/open/{id}', [DashboardController::class, 'open'])->name('dashboard.open');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    //Update Sipinter
    Route::get('/updatesipinter', [UpdateSipinterController::class, 'view'])->name('updatesipinter');
    Route::get('/updatesipinter/add', [UpdateSipinterController::class, 'add'])->name('updatesipinter.add');
    Route::post('/updatesipinter/proses', [UpdateSipinterController::class, 'addupdatesipinter'])->name('updatesipinter.addProses');
    Route::get('/updatesipinter/edit/{id}', [UpdateSipinterController::class, 'edit'])->name('updatesipinter.edit');
    Route::post('/updatesipinter/editProses', [UpdateSipinterController::class, 'editProses'])->name('updatesipinter.editProses');
    Route::get('/updatesipinter/delete/{id}', [UpdateSipinterController::class, 'delete'])->name('updatesipinter.delete');
    //Invoice
    Route::get('/invoice', [InvoiceController::class, 'view'])->name('invoice');
    Route::get('/invoice/add/{id}', [InvoiceController::class, 'add'])->name('invoice.add');
    Route::post('/invoice/proses', [InvoiceController::class, 'addinvoices'])->name('invoice.addProses');
    Route::get('/invoice/edit/{id}', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::post('/invoice/editProses', [InvoiceController::class, 'editProses'])->name('invoice.editProses');
    Route::get('/invoice/detail/{id}', [InvoiceController::class, 'detailKelas'])
    ->name('invoice.detail');
    Route::get('/invoice/add/{tagihanId}', [InvoiceController::class, 'add'])->name('invoice.add');

    //Aktivasi
    Route::get('/aktivasi', [AktivasiController::class, 'view'])->name('aktivasi');
    Route::get('/aktivasi/add', [AktivasiController::class, 'add'])->name('aktivasi.add');
    Route::post('/aktivasi/proses', [AktivasiController::class, 'addsarpras'])->name('aktivasi.addProses');
    Route::get('/aktivasi/edit/{id}', [AktivasiController::class, 'edit'])->name('aktivasi.edit');
    Route::post('/aktivasi/editProses', [AktivasiController::class, 'editProses'])->name('aktivasi.editProses');
    Route::get('/aktivasi/delete/{id}', [AktivasiController::class, 'delete'])->name('aktivasi.delete');
    //admin
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::get('/adminAdd', [AdminController::class, 'add'])->name('admin.add');
    Route::post('/admin/add', [AdminController::class, 'addProses'])->name('admin.addproses');
    Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
    Route::post('/admin/editProses', [AdminController::class, 'editProses'])->name('admin.editProses');
    Route::get('/admin/delete/{id}', [AdminController::class, 'delete'])->name('admin.delete');
    //siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa');
    Route::get('/siswa/open/{id}', [SiswaController::class, 'open'])->name('siswa.open');
    Route::get('/siswaAdd', [SiswaController::class, 'add'])->name('siswa.add');
    Route::post('/siswa/add', [SiswaController::class, 'addSiswa'])->name('siswa.addproses');
    Route::get('/siswa/edit/{id}', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::post('/siswa1/editProses', [SiswaController::class, 'editProses'])->name('siswa1.editProses');
    Route::get('/siswa1/edit/{id}', [SiswaController::class, 'edit'])->name('siswa1.edit');
    Route::post('/siswa/editProses', [SiswaController::class, 'editProses'])->name('siswa.editProses');
    Route::get('/siswa/delete/{id}', [SiswaController::class, 'delete'])->name('siswa.delete');
    Route::get('/alumni', [SiswaController::class, 'alumni'])->name('alumni');
    Route::get('/siswa/tunggakan/{id}', [SiswaController::class, 'tunggakan'])->name('siswa.tunggakan');
    //tenaga
    Route::get('/tenaga', [TenagaController::class, 'view'])->name('tenaga');
    Route::get('/tenaga/open', [TenagaController::class, 'open'])->name('tenaga.open');
    Route::get('/tenaga/add', [TenagaController::class, 'add'])->name('tenaga.add');
    Route::post('/tenaga/proses', [TenagaController::class, 'addTenaga'])->name('tenaga.addProses');
    Route::get('/tenaga/edit/{id}', [TenagaController::class, 'edit'])->name('tenaga.edit');
    Route::post('/tenaga/editProses', [TenagaController::class, 'editProses'])->name('tenaga.editProses');
    Route::get('/tenaga/delete/{id}', [TenagaController::class, 'delete'])->name('tenaga.delete');
    Route::get('/tenaga/move', [TenagaController::class, 'movetenaga'])->name('tenaga.movekelas');
    Route::get('/load_data_moveTenagaFrom', [TenagaController::class, 'load_data_moveTenagaFrom'])->name('tenaga.load_data_moveTenagaFrom');
    //kesiswaan
    Route::get('/kesiswaan', [KesiswaanController::class, 'view'])->name('kesiswaan');
    Route::get('/kesiswaan/add', [KesiswaanController::class, 'add'])->name('kesiswaan.add');
    Route::post('/kesiswaan/proses', [KesiswaanController::class, 'addkesiswaan'])->name('kesiswaan.addProses');
    Route::get('/kesiswaan/edit/{id}', [KesiswaanController::class, 'edit'])->name('kesiswaan.edit');
    Route::post('/kesiswaan/editProses', [KesiswaanController::class, 'editProses'])->name('kesiswaan.editProses');
    Route::get('/kesiswaan/delete/{id}', [KesiswaanController::class, 'delete'])->name('kesiswaan.delete');
    //SK Yayasan
    Route::get('/sk', [SkController::class, 'view'])->name('sk');
    Route::get('/sk/add', [SkController::class, 'add'])->name('sk.add');
    Route::post('/sk/proses', [SkController::class, 'addsk'])->name('sk.addProses');
    Route::get('/sk/edit/{id}', [SkController::class, 'edit'])->name('sk.edit');
    Route::post('/sk/editProses', [SkController::class, 'editProses'])->name('sk.editProses');
    Route::get('/sk/delete/{id}', [SkController::class, 'delete'])->name('sk.delete');
    Route::get('download/{filename}', [SkController::class, 'download'])->name('file.download');
    //sarpras
    Route::get('/sarpras', [SarprasController::class, 'view'])->name('sarpras');
    Route::get('/sarpras/add', [SarprasController::class, 'add'])->name('sarpras.add');
    Route::post('/sarpras/proses', [SarprasController::class, 'addsarpras'])->name('sarpras.addProses');
    Route::get('/sarpras/edit/{id}', [SarprasController::class, 'edit'])->name('sarpras.edit');
    Route::post('/sarpras/editProses', [SarprasController::class, 'editProses'])->name('sarpras.editProses');
    Route::get('/sarpras/delete/{id}', [SarprasController::class, 'delete'])->name('sarpras.delete');
    //Tahun AJaran
    Route::get('/tahun', [TahunController::class, 'view'])->name('tahun');
    Route::get('/tahunAdd', [TahunController::class, 'add'])->name('tahun.add');
    Route::post('/tahun/add', [TahunController::class, 'addProses'])->name('tahun.addproses');
    Route::get('/tahun/edit/{id}', [TahunController::class, 'edit'])->name('tahun.edit');
    Route::post('/tahun/editProses', [TahunController::class, 'editProses'])->name('tahun.editProses');
    Route::get('/tahun/delete/{id}', [TahunController::class, 'delete'])->name('tahun.delete');
    //tagihan
    Route::get('/tagihan', [TagihanController::class, 'view'])->name('tagihan');
    Route::get('/tagihanAdd', [TagihanController::class, 'add'])->name('tagihan.add');
    Route::post('/tagihan/add', [TagihanController::class, 'addProses'])->name('tagihan.addproses');
    Route::get('/tagihan/delete/{id}', [TagihanController::class, 'delete'])->name('tagihan.delete');
    //informasi
    Route::get('/informasi', [InformasiController::class, 'view'])->name('informasi');
    Route::get('/informasi/search', [InformasiController::class, 'search'])->name('informasi.search');
    //Identitas Lembaga
    Route::get('/identitas', [IdentitasController::class, 'view'])->name('identitas');
    //Struktur Organisasi
    Route::get('/struktur', [StrukturController::class, 'view'])->name('struktur');
    //getdropdown
    Route::get('/jenisPembayaran', [TagihanController::class, 'jenisPembayaran'])->name('jenisPembayaran');
    Route::get('/getSiswa', [TagihanController::class, 'getSiswa'])->name('getSiswa');
    Route::get('/tagihan/search', [TagihanController::class, 'search'])->name('search');
    //Usulan Guru & Pegawai Baru
    Route::get('/usulan', [UsulanController::class, 'view'])->name('usulan');
    Route::get('/usulan/open/{id}', [UsulanController::class, 'open'])->name('usulan.open');
    Route::get('/usulan/add', [UsulanController::class, 'add'])->name('usulan.add');
    Route::post('/usulan/proses', [UsulanController::class, 'addsarpras'])->name('usulan.addProses');
    Route::get('/usulan/edit/{id}', [UsulanController::class, 'edit'])->name('usulan.edit');
    Route::post('/usulan/editProses', [UsulanController::class, 'editProses'])->name('usulan.editProses');
    Route::get('/usulan/delete/{id}', [UsulanController::class, 'delete'])->name('usulan.delete');
    //Mutasi Guru & Pegawai
    Route::get('/mutasi', [MutasiController::class, 'view'])->name('mutasi');
    Route::get('/mutasi/add', [MutasiController::class, 'add'])->name('mutasi.add');
    Route::post('/mutasi/proses', [MutasiController::class, 'addmutasi'])->name('mutasi.addProses');
    Route::get('/mutasi/edit/{id}', [MutasiController::class, 'edit'])->name('mutasi.edit');
    Route::post('/mutasi/editProses', [MutasiController::class, 'editProses'])->name('mutasi.editProses');
    Route::get('/mutasi/delete/{id}', [MutasiController::class, 'delete'])->name('mutasi.delete');
    //Persuratan
    Route::get('/persuratan', [PersuratanController::class, 'view'])->name('persuratan');
    Route::get('/persuratan/add', [PersuratanController::class, 'add'])->name('persuratan.add');
    Route::post('/persuratan/proses', [PersuratanController::class, 'addsarpras'])->name('persuratan.addProses');
    Route::get('/persuratan/edit/{id}', [PersuratanController::class, 'edit'])->name('persuratan.edit');
    Route::post('/persuratan/editProses', [PersuratanController::class, 'editProses'])->name('persuratan.editProses');
    Route::get('/persuratan/delete/{id}', [PersuratanController::class, 'delete'])->name('persuratan.delete');
    //Proposal
    Route::get('/proposal', [ProposalController::class, 'view'])->name('proposal');
    Route::get('/proposal/open/{id}', [ProposalController::class, 'open'])->name('proposal.open');
    Route::post('/proposal/openProses', [ProposalController::class, 'openProses'])->name('proposal.openProses');
    Route::get('/proposal/add', [ProposalController::class, 'add'])->name('proposal.add');
    Route::post('/proposal/proses', [ProposalController::class, 'addsarpras'])->name('proposal.addProses');
    Route::get('/proposal/approve/{id}', [ProposalController::class, 'approve'])->name('proposal.approve');
    Route::post('/proposal/approveProses', [ProposalController::class, 'approveProses'])->name('proposal.approveProses');
    Route::get('/proposal/edit/{id}', [ProposalController::class, 'edit'])->name('proposal.edit');
    Route::post('/proposal/editProses', [ProposalController::class, 'editProses'])->name('proposal.editProses');
    Route::get('/proposal/delete/{id}', [ProposalController::class, 'delete'])->name('proposal.delete');
    Route::post('/proposal/ubah-status', [ProposalController::class, 'ubahStatus'])->name('proposal.ubahStatus');

    //Profile Sekolah/Madrasah
    Route::get('/profile_sekolah', [ProfileSekolahController::class, 'view'])->name('profile_sekolah');
    Route::get('/profile_sekolah/search', [ProfileSekolahController::class, 'search'])->name('profile_sekolah.search');
    //sk januari
    Route::get('/sk_januari', [SkJanuariController::class, 'view'])->name('sk_januari');
    //pembayaran
    Route::get('/pembayaran', [PembayaranController::class, 'view'])->name('pembayaran');
    Route::get('/pembayaran/search', [PembayaranController::class, 'search'])->name('pembayaran.search');
    Route::get('/pembayaran/spp/{id}', [PembayaranController::class, 'spp'])->name('pembayaran.spp');
    Route::get('/pembayaran/payment/{id}', [PembayaranController::class, 'payment'])->name('pembayaran.payment');
    Route::get('/siswaByKelas/{id}', [PembayaranController::class, 'siswaByKelas'])->name('pembayaran.siswaByKelas');
    Route::get('/pembayaran/deleteSpp/{id}', [PembayaranController::class, 'deleteSpp'])->name('pembayaran.deleteSpp');
    //spp
    Route::post('/sppAddProses', [PembayaranController::class, 'sppAddProses'])->name('pembayaran.add.spp');
    Route::post('/paymentAddProses', [PembayaranController::class, 'paymentAddProses'])->name('pembayaran.add.payment');
    Route::post('/iuranAddProses', [PembayaranController::class, 'iuranAddProses'])->name('pembayaran.add.iuran');
    //midtrans
    Route::post('/getToken', [SnapController::class, 'token'])->name('token');
    Route::post('/getTokenPayment', [SnapController::class, 'payment'])->name('payment');
    Route::post('/midtrans/callback', [SnapController::class, 'callback']);
    //kelas
    Route::get('/kelas', [KelasController::class, 'view'])->name('kelas');
    Route::get('/kelas/add', [KelasController::class, 'add'])->name('kelas.add');
    Route::post('/kelas/proses', [KelasController::class, 'addkelas'])->name('kelas.addKelas');
    Route::get('/kelas/edit/{id}', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::post('/kelas/editProses', [KelasController::class, 'editProses'])->name('kelas.editProses');
    Route::get('/kelas/delete/{id}', [KelasController::class, 'delete'])->name('kelas.delete');
    Route::get('/kelas/move', [KelasController::class, 'movekelas'])->name('kelas.movekelas');
    Route::get('/load_data_moveKelasFrom', [KelasController::class, 'load_data_moveKelasFrom'])->name('kelas.load_data_moveKelasFrom');
    Route::get('/load_data_moveKelasTo', [KelasController::class, 'load_data_moveKelasTo'])->name('kelas.load_data_moveKelasTo');
    Route::post('/kelas/moveproses', [KelasController::class, 'moveproses'])->name('kelas.moveproses');
    Route::post('/kelas/backproses', [KelasController::class, 'backproses'])->name('kelas.backproses');
    Route::get('/kelas/lulus', [KelasController::class, 'lulus'])->name('kelas.lulus');
    Route::post('/kelas/lulusproses', [KelasController::class, 'lulusproses'])->name('kelas.lulusproses');
    Route::get('/load_data_lulus', [KelasController::class, 'load_data_lulus'])->name('kelas.load_data_lulus');
    //Aplikasi
    Route::get('/aplikasi', [AplikasiController::class, 'view'])->name('aplikasi');
    // Route::get('/aplikasi', [AplikasiController::class, 'edit'])->name('aplikasi.edit');
    Route::post('/aplikasi/editProses', [AplikasiController::class, 'editProses'])->name('aplikasi.editProses');
    //Laporan
    Route::get('/laporan', [LaporanController::class, 'view'])->name('laporan');
    Route::get('/laporan/load_data', [LaporanController::class, 'load_data'])->name('laporan.load_data');
    //excel
    Route::get('/cetakExcel', [LaporanController::class, 'cetakExcel'])->name('laporan.cetakExcel');
    Route::get('/cetakExcelById', [LaporanController::class, 'cetakExcelById'])->name('laporan.cetakExcelById');
    //Tunggakan
    Route::get('/tunggakan', [TunggakanController::class, 'view'])->name('tunggakan');
    Route::get('/tunggakan/load_data', [TunggakanController::class, 'load_data'])->name('tunggakan.load_data');
    Route::get('/cetakTunggakan', [TunggakanController::class, 'cetakTunggakan'])->name('tunggakan.cetakTunggakan');
    //Jenis Pembayaran
    Route::get('/jenisPembayaran', [JenisPembayaranController::class, 'view'])->name('jenisPembayaran');
    Route::get('/jenisPembayaranAdd', [JenisPembayaranController::class, 'add'])->name('jenisPembayaran.add');
    Route::post('/jenisPembayaran/add', [JenisPembayaranController::class, 'addProses'])->name('jenisPembayaran.addproses');
    Route::get('/jenisPembayaran/edit/{id}', [JenisPembayaranController::class, 'edit'])->name('jenisPembayaran.edit');
    Route::post('/jenisPembayaran/editProses', [JenisPembayaranController::class, 'editProses'])->name('jenisPembayaran.editProses');
    Route::get('/jenisPembayaran/delete/{id}', [JenisPembayaranController::class, 'delete'])->name('jenisPembayaran.delete');
    //Profile
    Route::get('/profile', [ProfileController::class, 'view'])->name('profile');
    //program kerja
    Route::get('/program_kerja', [ProgramKerjaController::class, 'index'])->name('program_kerja.index');
    Route::get('/program_kerja/add', [ProgramKerjaController::class, 'create'])->name('program_kerja.create');
    Route::post('/program_kerja', [ProgramKerjaController::class, 'store'])->name('program_kerja.store');
    Route::post('/program_kerja/update-status/{id}', [ProgramKerjaController::class, 'updateStatus'])->name('program_kerja.updateStatus');
    Route::get('/program_kerja/delete/{id}', [ProgramKerjaController::class, 'destroy'])->name('program_kerja.destroy');
    //laporan tahunan
    Route::prefix('laporan_tahunan')->group(function () {
        Route::get('/', [LaporanTahunanController::class, 'index'])->name('laporan_tahunan.index');
        Route::get('/add', [LaporanTahunanController::class, 'create'])->name('laporan_tahunan.create');
        Route::post('/add', [LaporanTahunanController::class, 'store'])->name('laporan_tahunan.store');
        Route::delete('/delete/{id}', [LaporanTahunanController::class, 'destroy'])->name('laporan_tahunan.destroy');
    });
    //agenda kesekretariatan
    Route::prefix('agenda_kesekretariatan')->name('agenda_kesekretariatan.')->group(function() {
        Route::get('/', [AgendaKesekretariatanController::class, 'index'])->name('index');
        Route::get('/add', [AgendaKesekretariatanController::class, 'create'])->name('add');
        Route::post('/store', [AgendaKesekretariatanController::class, 'store'])->name('store');

        Route::post('/update-status/{id}', [AgendaKesekretariatanController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/delete/{id}', [AgendaKesekretariatanController::class, 'destroy'])->name('destroy');
    });
    //Bendahara
    Route::get('/bendahara/laporan', [BendaharaController::class, 'index'])->name('bendahara.index');
    Route::get('/bendahara/edit/{id}', [BendaharaController::class, 'edit'])->name('bendahara.edit');
    Route::delete('/bendahara/delete/{id}', [BendaharaController::class, 'destroy'])->name('bendahara.destroy');
    Route::get('/bendahara/create', [BendaharaController::class, 'create'])->name('bendahara.create');
    Route::post('/bendahara/store', [BendaharaController::class, 'store'])->name('bendahara.store');
    Route::get('/bendahara/edit/{id}', [BendaharaController::class, 'edit'])->name('bendahara.edit');
    Route::put('/bendahara/update/{id}', [BendaharaController::class, 'update'])->name('bendahara.update');

    //Profile Lembaga
    Route::resource('broadcast', BroadcastController::class);

    //Pdf
    Route::get('/bulananPdf/{id}', [LaporanController::class, 'bulananPdf'])->name('laporan.bulananPdf');
    Route::get('/bulananPdfById/{id}', [LaporanController::class, 'bulananPdfById'])->name('laporan.bulananPdfById');
    Route::get('/lainyaPdf/{id}', [LaporanController::class, 'lainyaPdf'])->name('laporan.lainyaPdf');
    Route::get('/iuranPdf/{id}', [LaporanController::class, 'iuranPdf'])->name('laporan.iuranPdf');
    Route::get('/pdfSkJuli2025/{id}', [LaporanController::class, 'pdfSkJuli2025'])->name('laporan.pdfSkJuli2025');
    //coba
    Route::get('/coba', 'App\Http\Controllers\CobaController@index');
    //View & Download File
    //Route::get('view', [FileController::class, 'view'])->name('file.view');
    //Route::get('download', [FileController::class, 'download'])->name('file.download');

    Route::get('/upload-sk', [UploadSKController::class, 'index'])->name('upload-sk.index');
    Route::post('/upload-sk', [UploadSKController::class, 'store'])->name('upload-sk.store');
    // Route::get('/get-users/{kelas_id}', [UploadSKController::class, 'getUsersByKelas']);
    Route::get('/get-users/{kelas_id}', [UploadSKController::class, 'getUsers']);
    Route::post('/upload-sk-user', [UploadSKController::class, 'uploadSKUser'])->name('upload.sk.user');


    // routes/web.php
    Route::get('/tools/pdf-upload', [PDFController::class, 'showUploadForm'])->name('tools.pdf.upload.form');
    Route::post('/tools/pdf-upload', [PDFController::class, 'upload'])->name('tools.pdf.upload');

    // Halaman utama dan input pesanan
    Route::get('/batik_maarif', [BatikMaarifController::class, 'index'])->name('batik.maarif');
    Route::post('/batik-maarif', [BatikMaarifController::class, 'store'])->name('batik.maarif.store');

    Route::post('/batik-maarif/snap', [BatikMaarifController::class, 'createSnapToken'])->name('batik.maarif.snap');
    // Integrasi Midtrans
    Route::post('/batik-maarif/pay', [BatikMaarifController::class, 'pay'])->name('batik.maarif.pay');
    Route::post('/batik-maarif/notification', [BatikMaarifController::class, 'notificationHandler'])->name('batik.maarif.notification');

    // Opsional (redirect setelah bayar)
    Route::get('/batik-maarif/success', [BatikMaarifController::class, 'success']);
    Route::get('/batik-maarif/failed', [BatikMaarifController::class, 'failed']);
    Route::get('/batik-maarif/unfinish', [BatikMaarifController::class, 'unfinish']);
    Route::post('/batik-maarif/diterima/{id}', [BatikMaarifController::class, 'diterima'])
    ->name('batik.maarif.diterima');
    Route::delete('/batik-maarif/delete/{id}', [BatikMaarifController::class, 'destroy'])
    ->name('batik.maarif.delete');

    // Modul Guru
    Route::get('/admin/modul', [ModulController::class, 'index'])->name('modul.index');
    Route::post('/admin/modul', [ModulController::class, 'store'])->name('modul.store');
    Route::delete('/admin/modul/{id}', [ModulController::class, 'destroy'])->name('modul.destroy');

    Route::get('/data-siswa', [DataSiswaController::class, 'index'])->name('data-siswa.index');
    Route::get('/data-siswa/create', [DataSiswaController::class, 'create'])->name('data-siswa.create');
    Route::post('/data-siswa/store', [DataSiswaController::class, 'store'])->name('data-siswa.store');
    Route::delete('/data-siswa/{id}', [DataSiswaController::class, 'destroy'])->name('data-siswa.destroy');
    Route::get('/data-siswa/{id}/edit', [App\Http\Controllers\DataSiswaController::class, 'edit'])->name('data-siswa.edit');
    Route::put('/data-siswa/{id}', [App\Http\Controllers\DataSiswaController::class, 'update'])->name('data-siswa.update');


});

Route::get('/route-cache', function () {
    Artisan::call('route:cache');
    return 'Routes cache cleared';
});
Route::get('/config-cache', function () {
    Artisan::call('config:cache');
    return 'Config cache cleared';
});
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return 'Application cache cleared';
});
Route::get('/view-clear', function () {
    Artisan::call('view:clear');
    return 'View cache cleared';
});
Route::get('/optimize', function () {
    Artisan::call('optimize');
    return 'Routes cache cleared';
});
