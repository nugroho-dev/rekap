<?php

use App\Http\Controllers\Api\ApiCekSicantikController;
use App\Http\Controllers\Api\TteController;
use App\Http\Controllers\DashboardBerusahaController;
use App\Http\Controllers\DashboardBimtekController;
use App\Http\Controllers\DashboardBusinessController;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardExpoController;
use App\Http\Controllers\DashboardFasilitasiController;
use App\Http\Controllers\DashboardKomitmenController;
use App\Http\Controllers\DashboardLoiController;
use App\Http\Controllers\DashboardPbgController;
use App\Http\Controllers\DashboardPengawasanController;
use App\Http\Controllers\DashboardVprosesSicantikController;
use App\Http\Controllers\DashboradSimpelController;
use App\Http\Controllers\DayOffDashboardController;
use App\Http\Controllers\InsentifController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\KonsultasiDashboardController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MppdController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PengawasanDashboardController;
use App\Http\Controllers\PetaPotensiController;
use App\Http\Controllers\ProdukHukumDashboardController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\PublicViewHomeController;
use App\Http\Controllers\SicantikApiController;
use App\Http\Controllers\SicantikDashboardController;
use App\Http\Controllers\SicantikProsesController;
use App\Http\Controllers\SigumilangDashboardController;
use App\Http\Controllers\UsersDashboardController;
use App\Http\Controllers\VerifikasiRealisasiInvestasiController;
use App\Models\Instansi;
use Illuminate\Http\Request;
use App\Http\Controllers\ProyekVerificationController;
use App\Http\Controllers\NibController;
use App\Http\Controllers\IzinController;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/apicek/{no_permohonan}/{email}', [ApiCekSicantikController::class, 'index']);
Route::get('/unduh/{no_permohonan}/{email}', [TteController::class, 'index']);

Route::get('/', [PublicViewHomeController::class, 'index']);
Route::post('/', [SicantikApiController::class, 'index']);
Route::match(['get','post'], '/kirim/dokumen/{id}', [SicantikApiController::class, 'dokumen']);
Route::get('/kirim/{id}', [SicantikApiController::class, 'kirim']);
Route::get('/send-mail', [MailController::class, 'index']);
Route::post('/send-mail/{id}', [MailController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/maintenance', [MaintenanceController::class, 'index']);

    // konfigurasi group (pegawai, instansi, user)
    Route::prefix('konfigurasi')->name('konfigurasi.')->group(function () {

        // Pegawai
        Route::get('pegawai/checkSlug', [PegawaiController::class, 'checkSlug'])->name('pegawai.checkSlug');
        Route::get('pegawai/ttd/{pegawai}', [PegawaiController::class, 'checkTtd'])->name('pegawai.ttd');
        Route::resource('pegawai', PegawaiController::class)->names('pegawai');
        Route::put('pegawai/{pegawai}/restore', [PegawaiController::class, 'restore'])->name('pegawai.restore');
        Route::delete('pegawai/{pegawai}/force-delete', [PegawaiController::class, 'forceDelete'])->name('pegawai.forceDelete');

        // Instansi
        Route::get('instansi/checkSlug', [InstansiController::class, 'checkSlug'])->name('instansi.checkSlug');
        Route::resource('instansi', InstansiController::class)->names('instansi');
        Route::put('instansi/{instansi}/restore', [InstansiController::class, 'restore'])->name('instansi.restore');
        Route::delete('instansi/{instansi}/force-delete', [InstansiController::class, 'forceDelete'])->name('instansi.forceDelete');

        // User
        Route::get('user/checkSlug', [UsersDashboardController::class, 'checkSlug'])->name('user.checkSlug');
        Route::resource('user', UsersDashboardController::class)->names('user');
    });

    // Konsultasi
    Route::match(['get','post'], '/konsultasi', [KonsultasiDashboardController::class, 'index']);
    Route::post('/konsultasi/import_excel', [KonsultasiDashboardController::class, 'import_excel']);
    Route::post('/konsultasicari', [KonsultasiDashboardController::class, 'index']);

    // Commitment / Komitmen
    Route::resource('/commitment', DashboardKomitmenController::class);
    Route::post('/commitment/import_excel', [DashboardKomitmenController::class, 'import_excel']);
    Route::post('/komitmensort', [DashboardKomitmenController::class, 'index']);

    // Pengaduan
    Route::resource('/pengaduan', PengaduanController::class);
    Route::post('/pengaduancari', [PengaduanController::class, 'index']);
    Route::get('/pengaduan/pengaduan/checkSlug', [PengaduanController::class, 'checkSlug']);

    // Pengawasan & related
    Route::resource('/pengawasan/sigumilang', SigumilangDashboardController::class);
    Route::get('/pengawasan/sigumilang/{id_proyek}/histori/{nib}', [SigumilangDashboardController::class,'histori']);
    Route::get('/pengawasan/laporan/sigumilang', [SigumilangDashboardController::class,'laporan']);

    // Produk hukum / deregulasi
    Route::resource('/deregulasi', ProdukHukumDashboardController::class);
    Route::post('/deregulasicari', [ProdukHukumDashboardController::class, 'index']);
    Route::get('/deregulasi/deregulasi/checkSlug', [ProdukHukumDashboardController::class, 'checkSlug']);

    // Insentif
    Route::resource('/insentif', InsentifController::class);
    Route::post('/insentifcari', [InsentifController::class, 'index']);
    Route::get('/insentif/insentif/checkSlug', [InsentifController::class, 'checkSlug']);

    // Peta Potensi
    Route::resource('/potensi', PetaPotensiController::class);
    Route::post('/potensicari', [PetaPotensiController::class, 'index']);
    Route::get('/peta/checkSlug', [PetaPotensiController::class, 'checkSlug']);

    // Verifikasi realisasi investasi
    Route::resource('/realiasi/investasi/verifikasi', VerifikasiRealisasiInvestasiController::class);

    // Sicantik / proses / statistik
    Route::get('/np', [SicantikDashboardController::class, 'index']);
    Route::match(['get','post'], '/sicantik', [DashboardVprosesSicantikController::class, 'index']);
    // Create new Sicantik entry (form + store)
    Route::get('/sicantik/create', [DashboardVprosesSicantikController::class, 'create']);
    Route::post('/sicantik', [DashboardVprosesSicantikController::class, 'store']);
    // Detail and supporting endpoints for Sicantik
    Route::get('/sicantik/print', [DashboardVprosesSicantikController::class, 'print']);
    Route::post('/sicantik/print', [DashboardVprosesSicantikController::class, 'print']);
    // Statistik routes must be registered before the parameterized detail route
    Route::get('/sicantik/statistik', [DashboardVprosesSicantikController::class, 'statistik']);
    Route::post('/sicantik/statistik', [DashboardVprosesSicantikController::class, 'statistik']);
    // Manual clear cache statistik (summary + detail)
    Route::post('/sicantik/statistik/clear-cache', [DashboardVprosesSicantikController::class, 'clearStatistikCache'])->name('sicantik.statistik.clearCache');
    // AJAX month detail for statistik (year & month query params)
    Route::get('/sicantik/statistik/detail', [DashboardVprosesSicantikController::class, 'statistikDetail']);
    // Proses detail by no_permohonan (for SLA breakdown per langkah)
    Route::get('/sicantik/proses/{no_permohonan}', [DashboardVprosesSicantikController::class, 'showPermohonanProses'])->name('sicantik.proses.detail');
    // Detail endpoint for AJAX: return proses steps for a given id/no_permohonan
    Route::get('/sicantik/{id}', [DashboardVprosesSicantikController::class, 'show']);
    Route::post('/sicantik/sych', [DashboardVprosesSicantikController::class, 'sync']);
    // New route: accept correct spelling '/sync' in addition to legacy '/sych'
    Route::post('/sicantik/sync', [DashboardVprosesSicantikController::class, 'sync']);
    Route::post('/sicantik/rincian', [DashboardVprosesSicantikController::class, 'rincian']);
    Route::get('/sicantik/rincian/print', [DashboardVprosesSicantikController::class, 'printRincian'])->name('sicantik.rincian.print');

    // Dayoff
    Route::match(['get','post'], '/dayoff/sync', [DayOffDashboardController::class, 'handle']);
    Route::match(['get','post'], '/dayoff', [DayOffDashboardController::class, 'index']);

    // MPPD
    Route::match(['get','post'], '/mppd', [MppdController::class, 'index']);
    Route::post('/mppd/import_excel', [MppdController::class, 'import_excel']);
    // Alias legacy /mppdigital/import_excel ke import handler yang benar
    Route::post('/mppdigital/import_excel', [MppdController::class, 'import_excel']);
    Route::get('/mppdigital/import_excel', function(){ return redirect('/mppd'); });
    Route::get('/mppd/export_excel', [MppdController::class, 'export_excel']);
    Route::get('/mppd/audits', [MppdController::class, 'audits']);
    Route::get('/mppd/statistik', [MppdController::class, 'statistik']);
    Route::post('/mppd/statistik', [MppdController::class, 'statistik']);
    Route::post('/mppd/rincian', [MppdController::class, 'rincian']);
    Route::post('/mppd/upload_file', [MppdController::class, 'upload_file']);
    Route::post('/mppd/delete_file', [MppdController::class, 'delete_file']);
    Route::resource('/mppd', MppdController::class)->except(['index','store']);

    // Simpel
    Route::match(['get','post'], '/simpel', [DashboradSimpelController::class, 'index']);
    Route::get('/simpel/print', [DashboradSimpelController::class, 'print']);
    Route::post('/simpel/print', [DashboradSimpelController::class, 'print']);
    Route::get('/simpel/statistik', [DashboradSimpelController::class, 'statistik']);
    Route::post('/simpel/statistik', [DashboradSimpelController::class, 'statistik']);
    Route::post('/simpel/rincian', [DashboradSimpelController::class, 'rincian']);

    // Proyek
    Route::match(['get','post'], '/berusaha/proyek', [ProyekController::class, 'index']);
    Route::post('/berusaha/proyek/import_excel', [ProyekController::class, 'import_excel']);
    Route::get('/berusaha/proyek/statistik', [ProyekController::class, 'statistik']);
    Route::post('/berusaha/proyek/statistik', [ProyekController::class, 'statistik']);
    // Proyek export
    Route::get('/berusaha/proyek/export/excel', [ProyekController::class, 'exportExcel'])->name('proyek.export.excel');
    Route::get('/berusaha/proyek/export/pdf', [ProyekController::class, 'exportPdf'])->name('proyek.export.pdf');

    Route::get('/proyek/detail', [ProyekController::class, 'detail']);
    Route::post('/proyek/detail', [ProyekController::class, 'detail']);

    // PBG
    Route::get('/pbg', [DashboardPbgController::class, 'index']);
    Route::post('/pbgsort', [DashboardPbgController::class, 'index']);
    Route::post('/pbg/import_excel', [DashboardPbgController::class, 'import_excel']);
    Route::post('/pbg', [DashboardPbgController::class, 'store']);
    // Statistik PBG (register before parameterized /pbg/{pbg})
    Route::get('/pbg/statistik', [DashboardPbgController::class, 'statistik']);
    Route::post('/pbg/statistik', [DashboardPbgController::class, 'statistik']);
    Route::post('/pbg/{pbg}/file/delete', [DashboardPbgController::class, 'deleteFile']);
    // Detail harus sebelum /edit agar tidak ketimpa
    Route::get('/pbg/{pbg}', [DashboardPbgController::class, 'show']);
    Route::get('/pbg/{pbg}/edit', [DashboardPbgController::class, 'edit']);
    Route::put('/pbg/{pbg}', [DashboardPbgController::class, 'update']);
    Route::delete('/pbg/{pbg}', [DashboardPbgController::class, 'destroy']);

    // NIB listing and import
    Route::get('/nib', [NibController::class, 'index'])->name('nib.index');
    Route::post('/nib/import', [NibController::class, 'import'])->name('nib.import');
    // Alternate path under /berusaha
    Route::get('/berusaha/nib', [NibController::class, 'index'])->name('nib.index.berusaha');
    Route::post('/berusaha/nib/import', [NibController::class, 'import'])->name('nib.import.berusaha');
    // NIB statistik
    Route::get('/nib/statistik', [NibController::class, 'statistik'])->name('nib.statistik');
    Route::get('/berusaha/nib/statistik', [NibController::class, 'statistik'])->name('nib.statistik.berusaha');

    // Izin listing and import
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::post('/izin/import', [IzinController::class, 'import'])->name('izin.import');
    // Alternate path under /berusaha
    Route::get('/berusaha/izin', [IzinController::class, 'index'])->name('izin.index.berusaha');
    Route::post('/berusaha/izin/import', [IzinController::class, 'import'])->name('izin.import.berusaha');
    // Izin export
    Route::get('/izin/export/excel', [IzinController::class, 'exportExcel'])->name('izin.export.excel');
    Route::get('/izin/export/pdf', [IzinController::class, 'exportPdf'])->name('izin.export.pdf');
    // Izin statistik
    Route::get('/izin/statistik', [IzinController::class, 'statistik'])->name('izin.statistik');

    // Role / Permission helpers (protected)
    Route::get('/createrolepermission', function(){
        try{
            Role::create(['name' => 'administrator']);
            Permission::create(['name' => 'view-konsultasi']);
            return 'sukses';
        } catch(\Exception $th){
            return 'gagal';
        }
    });
    Route::get('/give-user-role', function(){
        try {
            $user = User::findOrFail(1);
            $user->assignRole('administrator');
            return 'sukses';
        } catch(\Exception $th) {
            return 'gagal';
        }
    });
    Route::get('/give-user-permission', function () {
        try {
            $role = Role::findOrFail(1);
            $role->givePermissionTo('view-konsultasi');
            return 'sukses';
        } catch(\Exception $th) {
            return 'gagal';
        }
    });

    Route::prefix('proyek')->middleware('auth')->group(function () {
        Route::get('/realisasi/verifikasi', [ProyekVerificationController::class, 'index'])->name('proyek.verification.index');
        // standalone verification form page
        Route::get('/realisasi/verifikasi/form', [ProyekVerificationController::class, 'form'])->name('proyek.verification.form');
        Route::post('/realisasi/verifikasi', [ProyekVerificationController::class, 'store'])->name('proyek.verification.store');
    Route::post('/realisasi/verifikasi/apply-recommendations', [ProyekVerificationController::class, 'applyRecommendations'])->name('proyek.verification.applyRecommendations');
        // import verification from excel
        Route::post('/realisasi/verifikasi/import', [ProyekVerificationController::class, 'importVerifiedExcel'])->name('proyek.verification.import');
        // download template for excel import
        Route::get('/realisasi/verifikasi/template', [ProyekVerificationController::class, 'downloadImportTemplate'])->name('proyek.verification.template');
        Route::post('/realisasi/verifikasi/{proyekVerification}/status', [ProyekVerificationController::class, 'updateStatus'])->name('proyek.verification.updateStatus');
        Route::delete('/realisasi/verifikasi/{proyekVerification}', [ProyekVerificationController::class, 'destroy'])->name('proyek.verification.destroy');
        // export verified list (xlsx | pdf)
        Route::get('/realisasi/verifikasi/export', [ProyekVerificationController::class, 'exportVerified'])->name('proyek.verification.export');
    });

    // Page to list verified projects (clicked from summary table)
    Route::get('proyek/verifikasi/terverifikasi', [ProyekVerificationController::class, 'listVerified'])
        ->name('proyek.verification.list')
        ->middleware('auth');
});