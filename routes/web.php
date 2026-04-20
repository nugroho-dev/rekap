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
use App\Http\Controllers\Admin\PublikasiDataController;
use App\Http\Controllers\Admin\ApiDocumentationController;
use App\Http\Controllers\Admin\ApiAuditLogController;
use App\Models\Proses;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\KbliController;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/apicek/{no_permohonan}/{email}', [ApiCekSicantikController::class, 'index']);
Route::get('/unduh/{no_permohonan}/{email}', [TteController::class, 'index']);

Route::get('/', [PublicViewHomeController::class, 'index']);
Route::post('/', [SicantikApiController::class, 'index']);
// Gabungan Proyek & Izin
Route::get('/berusaha/proyekizin', [App\Http\Controllers\ProyekIzinController::class, 'index'])->name('proyekizin.index');
Route::get('/berusaha/proyekizin/export-excel', [App\Http\Controllers\ProyekIzinController::class, 'exportExcel'])->name('proyekizin.export.excel');
Route::match(['get','post'], '/kirim/dokumen/{id}', [SicantikApiController::class, 'dokumen']);
Route::get('/kirim/{id}', [SicantikApiController::class, 'kirim']);
Route::get('/send-mail', [MailController::class, 'index']);
Route::post('/send-mail/{id}', [MailController::class, 'index']);

// Public Statistik SiCantik (controller)
Route::get('/statistik-sicantik', [\App\Http\Controllers\PublicStatistikSicantikController::class, 'index'])->name('public.statistik.sicantik');

// Public Statistik MPPD
Route::get('/statistik-mppd', [\App\Http\Controllers\MppdController::class, 'statistik_public'])->name('public.statistik.mppd');
// Public Statistik Simpel (Izin Pemakaman)
Route::get('/statistik-simpel', [\App\Http\Controllers\DashboradSimpelController::class, 'statistik_public'])->name('public.statistik.simpel');
// Public Statistik PBG
Route::get('/statistik-pbg', [\App\Http\Controllers\DashboardPbgController::class, 'statistik_public'])->name('public.statistik.pbg');
// Public Statistik Perizinan Berusaha
Route::get('/statistik-proyek', [\App\Http\Controllers\ProyekController::class, 'statistik_public'])->name('public.statistik.proyek');
Route::get('/statistik-nib', [\App\Http\Controllers\NibController::class, 'statistik_public'])->name('public.statistik.nib');
Route::get('/statistik-izin', [\App\Http\Controllers\IzinController::class, 'statistik_public'])->name('public.statistik.izin');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view');
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->middleware('permission:dashboard.view');
    Route::get('/api/docs', [ApiDocumentationController::class, 'index'])->name('api.docs.index')->middleware('permission:api.docs.view');
    Route::get('/api/audits', [ApiAuditLogController::class, 'index'])->name('api.audits.index')->middleware('permission:api.audit.view');
    Route::get('/api/audits/export', [ApiAuditLogController::class, 'exportCsv'])->name('api.audits.export')->middleware('permission:api.audit.export');
    Route::get('/api/audits/{apiAuditLog}', [ApiAuditLogController::class, 'show'])->name('api.audits.show')->middleware('permission:api.audit.view');

    // konfigurasi group (pegawai, instansi, user)
    Route::prefix('konfigurasi')->name('konfigurasi.')->middleware('permission:konfigurasi.view')->group(function () {
        // Kategori Informasi
        Route::resource('kategori-informasi', \App\Http\Controllers\Admin\KategoriInformasiController::class)
            ->names([
                'index' => 'admin.kategori-informasi.index',
                'create' => 'admin.kategori-informasi.create',
                'store' => 'admin.kategori-informasi.store',
                'edit' => 'admin.kategori-informasi.edit',
                'update' => 'admin.kategori-informasi.update',
                'destroy' => 'admin.kategori-informasi.destroy',
                'show' => 'admin.kategori-informasi.show',
            ]);

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
        Route::get('user/api-accounts', [UsersDashboardController::class, 'apiAccounts'])->name('user.api-accounts');
        Route::get('user/checkSlug', [UsersDashboardController::class, 'checkSlug'])->name('user.checkSlug');
        Route::get('user/{user}/access', [UsersDashboardController::class, 'access'])->name('user.access');
        Route::put('user/{user}/access', [UsersDashboardController::class, 'updateAccess'])->name('user.access.update');
        Route::post('user/{user}/api-tokens', [UsersDashboardController::class, 'storeApiToken'])->name('user.api-tokens.store');
        Route::post('user/{user}/api-tokens/quick', [UsersDashboardController::class, 'storeQuickApiToken'])->name('user.api-tokens.quick-store');
        Route::delete('user/{user}/api-tokens/{tokenId}', [UsersDashboardController::class, 'destroyApiToken'])->name('user.api-tokens.destroy');
        Route::resource('user', UsersDashboardController::class)->names('user');

        // Publikasi Data
        Route::match(['get','post'], 'publikasi', [PublikasiDataController::class, 'index'])->name('admin.publikasi.index');
        Route::get('publikasi/create', [PublikasiDataController::class, 'create'])->name('admin.publikasi.create');
        Route::post('publikasi', [PublikasiDataController::class, 'store'])->name('admin.publikasi.store');
        Route::get('publikasi/{publikasi_data}/edit', [PublikasiDataController::class, 'edit'])->name('admin.publikasi.edit');
        Route::put('publikasi/{publikasi_data}', [PublikasiDataController::class, 'update'])->name('admin.publikasi.update');
        Route::delete('publikasi/{publikasi_data}', [PublikasiDataController::class, 'destroy'])->name('admin.publikasi.destroy');
        Route::get('publikasi/{publikasi_data}', [PublikasiDataController::class, 'show'])->name('admin.publikasi.show');
    });

    // Konsultasi
    Route::match(['get','post'], '/konsultasi', [KonsultasiDashboardController::class, 'index'])->middleware('permission:konsultasi.view');
    Route::get('/konsultasi/create', [KonsultasiDashboardController::class, 'create'])->name('konsultasi.create')->middleware('permission:konsultasi.view');
    Route::post('/konsultasi', [KonsultasiDashboardController::class, 'store'])->name('konsultasi.store')->middleware('permission:konsultasi.view');
    Route::get('/konsultasi/statistik', [KonsultasiDashboardController::class, 'statistik'])->middleware('permission:konsultasi.view');
    Route::post('/konsultasi/import_excel', [KonsultasiDashboardController::class, 'import_excel'])->middleware('permission:konsultasi.view');
    Route::post('/konsultasicari', [KonsultasiDashboardController::class, 'index'])->middleware('permission:konsultasi.view');
    // Edit konsultasi
    Route::get('/konsultasi/{konsultasi}/edit', [KonsultasiDashboardController::class, 'edit'])->name('konsultasi.edit')->middleware('permission:konsultasi.view');
    Route::put('/konsultasi/{konsultasi}', [KonsultasiDashboardController::class, 'update'])->name('konsultasi.update')->middleware('permission:konsultasi.view');
    // Soft delete konsultasi
    Route::delete('/konsultasi/{konsultasi}', [KonsultasiDashboardController::class, 'destroy'])->name('konsultasi.destroy')->middleware('permission:konsultasi.view');

    // Commitment / Komitmen
    Route::match(['get','post'],'/commitment', [DashboardKomitmenController::class, 'index'])->middleware('permission:commitment.view');
    Route::get('/commitment/statistik', [DashboardKomitmenController::class, 'statistik'])->middleware('permission:commitment.view');
    Route::post('/commitment/import_excel', [DashboardKomitmenController::class, 'import_excel'])->middleware('permission:commitment.view');
    Route::post('/komitmensort', [DashboardKomitmenController::class, 'index'])->middleware('permission:commitment.view');

    // Pengaduan
    Route::match(['get','post'],'/pengaduan', [PengaduanController::class, 'index'])->middleware('permission:pengaduan.view');
    Route::get('/pengaduan/create', [PengaduanController::class, 'create'])->middleware('permission:pengaduan.view');
    Route::post('/pengaduan', [PengaduanController::class, 'store'])->middleware('permission:pengaduan.view');
    Route::get('/pengaduan/pengaduan/checkSlug', [PengaduanController::class, 'checkSlug'])->middleware('permission:pengaduan.view');
     Route::get('/pengaduan/statistik', [PengaduanController::class, 'statistik'])->middleware('permission:pengaduan.view');
    Route::get('/pengaduan/{pengaduan}', [PengaduanController::class, 'show'])->middleware('permission:pengaduan.view');
    Route::get('/pengaduan/{pengaduan}/edit', [PengaduanController::class, 'edit'])->middleware('permission:pengaduan.view');
    Route::put('/pengaduan/{pengaduan}', [PengaduanController::class, 'update'])->middleware('permission:pengaduan.view');
    Route::delete('/pengaduan/{pengaduan}', [PengaduanController::class, 'destroy'])->middleware('permission:pengaduan.view');
   

    // Pengawasan & related
    //Route::resource('/pengawasan/sigumilang', SigumilangDashboardController::class);
    Route::get('/pengawasan/sigumilang/', [SigumilangDashboardController::class,'index'])->name('sigumilang.index')->middleware('permission:sigumilang.view');
    Route::get('/pengawasan/sigumilang/{id_proyek}/histori/{nib}', [SigumilangDashboardController::class,'histori'])->middleware('permission:sigumilang.view');
    Route::get('/pengawasan/laporan/sigumilang', [SigumilangDashboardController::class,'laporan'])->middleware('permission:sigumilang.view');
    Route::get('/pengawasan/statistik/sigumilang', [SigumilangDashboardController::class,'statistik'])->middleware('permission:sigumilang.view');

    // Produk hukum / deregulasi
    Route::match(['get','post'], '/deregulasi', [ProdukHukumDashboardController::class, 'index'])->middleware('permission:deregulasi.view');
    Route::post('/deregulasi/import_excel', [ProdukHukumDashboardController::class, 'import_excel'])->middleware('permission:deregulasi.view');
    Route::get('/deregulasi/statistik', [ProdukHukumDashboardController::class, 'statistik'])->middleware('permission:deregulasi.view');
    Route::get('/deregulasi/create', [ProdukHukumDashboardController::class, 'create'])->middleware('permission:deregulasi.view');
    Route::post('/deregulasi', [ProdukHukumDashboardController::class, 'store'])->middleware('permission:deregulasi.view');   
    Route::get('/deregulasi/{deregulasi}', [ProdukHukumDashboardController::class, 'show'])->middleware('permission:deregulasi.view');
    Route::get('/deregulasi/{deregulasi}/edit', [ProdukHukumDashboardController::class, 'edit'])->middleware('permission:deregulasi.view');
    Route::put('/deregulasi/{deregulasi}', [ProdukHukumDashboardController::class, 'update'])->middleware('permission:deregulasi.view');
    Route::delete('/deregulasi/{deregulasi}', [ProdukHukumDashboardController::class, 'destroy'])->middleware('permission:deregulasi.view');
    Route::get('/deregulasi/deregulasi/checkSlug', [ProdukHukumDashboardController::class, 'checkSlug'])->middleware('permission:deregulasi.view');

    // Insentif
    Route::match(['get','post'], '/insentif', [InsentifController::class, 'index'])->middleware('permission:insentif.view');
    Route::post('/insentif/import_excel', [InsentifController::class, 'import_excel'])->middleware('permission:insentif.view');
    Route::get('/insentif/statistik', [InsentifController::class, 'statistik'])->middleware('permission:insentif.view');
    Route::get('/insentif/create', [InsentifController::class, 'create'])->middleware('permission:insentif.view');
    Route::post('/insentif', [InsentifController::class, 'store'])->middleware('permission:insentif.view');
    Route::get('/insentif/{insentif}', [InsentifController::class, 'show'])->middleware('permission:insentif.view');
    Route::get('/insentif/{insentif}/edit', [InsentifController::class, 'edit'])->middleware('permission:insentif.view');
    Route::put('/insentif/{insentif}', [InsentifController::class, 'update'])->middleware('permission:insentif.view');
    Route::delete('/insentif/{insentif}', [InsentifController::class, 'destroy'])->middleware('permission:insentif.view');
    Route::get('/insentif/insentif/checkSlug', [InsentifController::class, 'checkSlug'])->middleware('permission:insentif.view');

    // Peta Potensi
    Route::match(['get','post'], '/potensi', [PetaPotensiController::class, 'index'])->middleware('permission:potensi.view');
    Route::get('/potensi/statistik', [PetaPotensiController::class, 'statistik'])->middleware('permission:potensi.view');
    Route::get('/potensi/create', [PetaPotensiController::class, 'create'])->middleware('permission:potensi.view');
    Route::post('/potensi', [PetaPotensiController::class, 'store'])->middleware('permission:potensi.view');
    Route::get('/potensi/{potensi}', [PetaPotensiController::class, 'show'])->middleware('permission:potensi.view');
    Route::get('/potensi/{potensi}/edit', [PetaPotensiController::class, 'edit'])->middleware('permission:potensi.view');
    Route::put('/potensi/{potensi}', [PetaPotensiController::class, 'update'])->middleware('permission:potensi.view');
    Route::delete('/potensi/{potensi}', [PetaPotensiController::class, 'destroy'])->middleware('permission:potensi.view');
    Route::get('/peta/checkSlug', [PetaPotensiController::class, 'checkSlug'])->middleware('permission:potensi.view');

    //loi
    Route::match(['get','post'],'/loi', [DashboardLoiController::class, 'index'])->middleware('permission:promosi.view');
    Route::get('/loi/statistik', [DashboardLoiController::class, 'statistik'])->name('loi.statistik')->middleware('permission:promosi.view');
    Route::get('/loi/create', [DashboardLoiController::class, 'create'])->middleware('permission:promosi.view');
    Route::post('/loi', [DashboardLoiController::class, 'store'])->middleware('permission:promosi.view');
    Route::get('/loi/check/checkSlug', [DashboardLoiController::class, 'checkSlug'])->middleware('permission:promosi.view');
    Route::get('/loi/{loi}', [DashboardLoiController::class, 'show'])->middleware('permission:promosi.view');
    Route::get('/loi/{loi}/edit', [DashboardLoiController::class, 'edit'])->middleware('permission:promosi.view');
    Route::put('/loi/{loi}', [DashboardLoiController::class, 'update'])->middleware('permission:promosi.view');
    Route::delete('/loi/{loi}', [DashboardLoiController::class, 'destroy'])->middleware('permission:promosi.view');

    //expo
    Route::match(['get','post'], '/expo', [DashboardExpoController::class, 'index'])->middleware('permission:promosi.view');
    Route::get('/expo/statistik', [DashboardExpoController::class, 'statistik'])->middleware('permission:promosi.view');
    Route::get('/expo/create', [DashboardExpoController::class, 'create'])->middleware('permission:promosi.view');
    Route::post('/expo', [DashboardExpoController::class, 'store'])->middleware('permission:promosi.view');
    Route::get('/expo/{expo}', [DashboardExpoController::class, 'show'])->middleware('permission:promosi.view');
    Route::get('/expo/{expo}/edit', [DashboardExpoController::class, 'edit'])->middleware('permission:promosi.view');
    Route::put('/expo/{expo}', [DashboardExpoController::class, 'update'])->middleware('permission:promosi.view');
    Route::delete('/expo/{expo}', [DashboardExpoController::class, 'destroy'])->middleware('permission:promosi.view');
    Route::get('/expo/check/checkSlug', [DashboardExpoController::class, 'checkSlug'])->middleware('permission:promosi.view');

    //bussiness
    Route::match(['get','post'], '/business', [DashboardBusinessController::class, 'index'])->middleware('permission:promosi.view');
    Route::get('/business/statistik', [DashboardBusinessController::class, 'statistik'])->middleware('permission:promosi.view');
    Route::get('/business/create', [DashboardBusinessController::class, 'create'])->middleware('permission:promosi.view');
    Route::post('/business', [DashboardBusinessController::class, 'store'])->middleware('permission:promosi.view');
    Route::get('/business/{business}', [DashboardBusinessController::class, 'show'])->middleware('permission:promosi.view');
    Route::get('/business/{business}/edit', [DashboardBusinessController::class, 'edit'])->middleware('permission:promosi.view');
    Route::put('/business/{business}', [DashboardBusinessController::class, 'update'])->middleware('permission:promosi.view');
    Route::delete('/business/{business}', [DashboardBusinessController::class, 'destroy'])->middleware('permission:promosi.view');
    Route::get('/business/check/checkSlug', [DashboardBusinessController::class, 'checkSlug'])->middleware('permission:promosi.view');

    //bimtek
    Route::match(['get','post'], '/bimtek', [DashboardBimtekController::class, 'index'])->middleware('permission:bimtek.view');
    Route::get('/bimtek/statistik', [DashboardBimtekController::class, 'statistik'])->middleware('permission:bimtek.view');
    Route::post('/bimtek/import_excel', [DashboardBimtekController::class, 'import_excel'])->middleware('permission:bimtek.view');
    Route::get('/bimtek/{bimtek}', [DashboardBimtekController::class, 'show'])->middleware('permission:bimtek.view');
    Route::get('/bimtek/{bimtek}/edit', [DashboardBimtekController::class, 'edit'])->middleware('permission:bimtek.view');
    Route::put('/bimtek/{bimtek}', [DashboardBimtekController::class, 'update'])->middleware('permission:bimtek.view');
    Route::delete('/bimtek/{bimtek}', [DashboardBimtekController::class, 'destroy'])->middleware('permission:bimtek.view');

    //pengawasan
    Route::match(['get','post'], '/pengawasan', [DashboardPengawasanController::class, 'index'])->middleware('permission:pengawasan.view');
    Route::get('/pengawasan/statistik', [DashboardPengawasanController::class, 'statistik'])->middleware('permission:pengawasan.view');
    Route::post('/pengawasan/import_excel', [DashboardPengawasanController::class, 'import_excel'])->middleware('permission:pengawasan.view');
    Route::get('/pengawasan/{pengawasan}',[DashboardPengawasanController::class, 'show'])->middleware('permission:pengawasan.view');
    Route::get('/pengawasan/{pengawasan}/edit',[DashboardPengawasanController::class, 'edit'])->middleware('permission:pengawasan.view');
    Route::put('/pengawasan/{pengawasan}',[DashboardPengawasanController::class, 'update'])->middleware('permission:pengawasan.view');
    Route::delete('/pengawasan/{pengawasan}',[DashboardPengawasanController::class, 'destroy'])->middleware('permission:pengawasan.view');

    //fasilitasi
    Route::resource('/fasilitasi', DashboardFasilitasiController::class)->middleware('permission:fasilitasi.view');

    // Verifikasi realisasi investasi
    Route::resource('/realiasi/investasi/verifikasi', VerifikasiRealisasiInvestasiController::class);

    // Sicantik / proses / statistik
    Route::get('/np', [SicantikDashboardController::class, 'index']);
    Route::match(['get','post'], '/sicantik', [DashboardVprosesSicantikController::class, 'index'])->middleware('permission:sicantik.view');
    // Create new Sicantik entry (form + store)
    Route::get('/sicantik/create', [DashboardVprosesSicantikController::class, 'create'])->middleware('permission:sicantik.view');
    Route::post('/sicantik', [DashboardVprosesSicantikController::class, 'store'])->middleware('permission:sicantik.view');
    // Detail and supporting endpoints for Sicantik
    Route::get('/sicantik/print', [DashboardVprosesSicantikController::class, 'print'])->middleware('permission:sicantik.view');
    Route::post('/sicantik/print', [DashboardVprosesSicantikController::class, 'print'])->middleware('permission:sicantik.view');
    // Statistik routes must be registered before the parameterized detail route
    Route::get('/sicantik/statistik', [DashboardVprosesSicantikController::class, 'statistik'])->middleware('permission:sicantik.view');
    Route::post('/sicantik/statistik', [DashboardVprosesSicantikController::class, 'statistik'])->middleware('permission:sicantik.view');
       
        // Manual clear cache statistik (summary + detail)
        Route::post('/sicantik/statistik/clear-cache', [DashboardVprosesSicantikController::class, 'clearStatistikCache'])->name('sicantik.statistik.clearCache')->middleware('permission:sicantik.view');
        // AJAX month detail for statistik (year & month query params)
        Route::get('/sicantik/statistik/detail', [DashboardVprosesSicantikController::class, 'statistikDetail'])->middleware('permission:sicantik.view');
    // Proses detail by no_permohonan (for SLA breakdown per langkah)
    Route::get('/sicantik/proses/{no_permohonan}', [DashboardVprosesSicantikController::class, 'showPermohonanProses'])->name('sicantik.proses.detail')->middleware('permission:sicantik.view');
    // Detail endpoint for AJAX: return proses steps for a given id/no_permohonan
    Route::get('/sicantik/{id}', [DashboardVprosesSicantikController::class, 'show'])->middleware('permission:sicantik.view');
    Route::post('/sicantik/sych', [DashboardVprosesSicantikController::class, 'sync'])->middleware('permission:sicantik.view');
    // New route: accept correct spelling '/sync' in addition to legacy '/sych'
    Route::post('/sicantik/sync', [DashboardVprosesSicantikController::class, 'sync'])->middleware('permission:sicantik.view');
    // Download signed PDF to server (similar to Simpel)
    Route::post('/sicantik/download-pdf', [DashboardVprosesSicantikController::class, 'downloadPdfToServer'])->name('sicantik.downloadPdf')->middleware('permission:sicantik.view');
    // Batch download signed PDFs to server
    Route::post('/sicantik/download-pdf/batch', [DashboardVprosesSicantikController::class, 'downloadPdfBatchToServer'])->name('sicantik.downloadPdfBatch')->middleware('permission:sicantik.view');
    Route::post('/sicantik/rincian', [DashboardVprosesSicantikController::class, 'rincian'])->middleware('permission:sicantik.view');
    Route::get('/sicantik/rincian/print', [DashboardVprosesSicantikController::class, 'printRincian'])->name('sicantik.rincian.print')->middleware('permission:sicantik.view');

    // Dayoff
    Route::match(['get','post'], '/dayoff/sync', [DayOffDashboardController::class, 'handle'])->middleware('permission:dayoff.view');
    Route::match(['get','post'], '/dayoff', [DayOffDashboardController::class, 'index'])->middleware('permission:dayoff.view');

    // MPPD
    Route::match(['get','post'], '/mppd', [MppdController::class, 'index'])->middleware('permission:mppd.view');
    Route::post('/mppd/import_excel', [MppdController::class, 'import_excel'])->middleware('permission:mppd.view');
    // Alias legacy /mppdigital/import_excel ke import handler yang benar
    Route::post('/mppdigital/import_excel', [MppdController::class, 'import_excel'])->middleware('permission:mppd.view');
    Route::get('/mppdigital/import_excel', function(){ return redirect('/mppd'); })->middleware('permission:mppd.view');
    Route::get('/mppd/export_excel', [MppdController::class, 'export_excel'])->middleware('permission:mppd.view');
    Route::get('/mppd/audits', [MppdController::class, 'audits'])->middleware('permission:mppd.view');
    Route::get('/mppd/statistik', [MppdController::class, 'statistik'])->middleware('permission:mppd.view');
    Route::post('/mppd/statistik', [MppdController::class, 'statistik'])->middleware('permission:mppd.view');
    Route::post('/mppd/rincian', [MppdController::class, 'rincian'])->middleware('permission:mppd.view');
    Route::get('/mppd/rincian', [MppdController::class, 'rincian'])->middleware('permission:mppd.view');
    Route::get('/mppd/rincian/print', [MppdController::class, 'printRincian'])->name('mppd.rincian.print')->middleware('permission:mppd.view');
    Route::post('/mppd/upload_file', [MppdController::class, 'upload_file'])->middleware('permission:mppd.view');
    Route::post('/mppd/delete_file', [MppdController::class, 'delete_file'])->middleware('permission:mppd.view');
    Route::resource('/mppd', MppdController::class)->except(['index','store'])->middleware('permission:mppd.view');

    // Simpel
    Route::match(['get','post'], '/simpel', [DashboradSimpelController::class, 'index'])->middleware('permission:simpel.view');
    Route::get('/simpel/print', [DashboradSimpelController::class, 'print'])->middleware('permission:simpel.view');
    Route::post('/simpel/print', [DashboradSimpelController::class, 'print'])->middleware('permission:simpel.view');
    Route::get('/simpel/statistik', [DashboradSimpelController::class, 'statistik'])->middleware('permission:simpel.view');
    Route::post('/simpel/statistik', [DashboradSimpelController::class, 'statistik'])->middleware('permission:simpel.view');
    Route::get('/simpel/rincian', [DashboradSimpelController::class, 'rincian'])->middleware('permission:simpel.view');
    Route::post('/simpel/rincian', [DashboradSimpelController::class, 'rincian'])->middleware('permission:simpel.view');
    Route::get('/simpel/rincian/print', [DashboradSimpelController::class, 'printRincian'])->name('simpel.rincian.print')->middleware('permission:simpel.view');
    Route::post('/simpel/rincian', [DashboradSimpelController::class, 'rincian'])->middleware('permission:simpel.view');
    Route::post('/simpel/download-pdf', [DashboradSimpelController::class, 'downloadPdfToServer'])->name('simpel.downloadPdf')->middleware('permission:simpel.view');
    // Batch download PDFs to server (Simpel)
    Route::post('/simpel/download-pdf/batch', [DashboradSimpelController::class, 'downloadPdfBatchToServer'])->name('simpel.downloadPdfBatch')->middleware('permission:simpel.view');

    // Proyek
    Route::match(['get','post'], '/berusaha/proyek', [ProyekController::class, 'index'])->middleware('permission:proyek.view');
    Route::post('/berusaha/proyek/import_excel', [ProyekController::class, 'import_excel'])->middleware('permission:proyek.view');
    Route::get('/berusaha/proyek/statistik', [ProyekController::class, 'statistik'])->middleware('permission:proyek.view');
    Route::post('/berusaha/proyek/statistik', [ProyekController::class, 'statistik'])->middleware('permission:proyek.view');
    // Proyek statistik berdasarkan kategori
    Route::match(['get','post'], '/berusaha/proyek/statistik/risiko', [ProyekController::class, 'statistikRisiko'])->name('proyek.statistik.risiko')->middleware('permission:proyek.view');
    Route::match(['get','post'], '/berusaha/proyek/statistik/kbli', [ProyekController::class, 'statistikKbli'])->name('proyek.statistik.kbli')->middleware('permission:proyek.view');
    Route::match(['get','post'], '/berusaha/proyek/statistik/skala-usaha', [ProyekController::class, 'statistikSkalaUsaha'])->name('proyek.statistik.skala-usaha')->middleware('permission:proyek.view');
    Route::match(['get','post'], '/berusaha/proyek/statistik/kecamatan', [ProyekController::class, 'statistikKecamatan'])->name('proyek.statistik.kecamatan')->middleware('permission:proyek.view');
    Route::match(['get','post'], '/berusaha/proyek/statistik/kelurahan', [ProyekController::class, 'statistikKelurahan'])->name('proyek.statistik.kelurahan')->middleware('permission:proyek.view');
    // Proyek export
    Route::get('/berusaha/proyek/export/excel', [ProyekController::class, 'exportExcel'])->name('proyek.export.excel')->middleware('permission:proyek.view');
    Route::get('/berusaha/proyek/export/pdf', [ProyekController::class, 'exportPdf'])->name('proyek.export.pdf')->middleware('permission:proyek.view');

    Route::get('/proyek/detail', [ProyekController::class, 'detail'])->middleware('permission:proyek.view');
    Route::post('/proyek/detail', [ProyekController::class, 'detail'])->middleware('permission:proyek.view');

    // PBG
    Route::get('/pbg', [DashboardPbgController::class, 'index'])->middleware('permission:pbg.view');
    Route::post('/pbgsort', [DashboardPbgController::class, 'index'])->middleware('permission:pbg.view');
    Route::post('/pbg/import_excel', [DashboardPbgController::class, 'import_excel'])->middleware('permission:pbg.view');
    Route::get('/pbg/export/excel', [DashboardPbgController::class, 'exportExcel'])->name('pbg.export.excel')->middleware('permission:pbg.view');
    Route::post('/pbg', [DashboardPbgController::class, 'store'])->middleware('permission:pbg.view');
    // Statistik PBG (register before parameterized /pbg/{pbg})
    Route::get('/pbg/statistik', [DashboardPbgController::class, 'statistik'])->middleware('permission:pbg.view');
    Route::post('/pbg/statistik', [DashboardPbgController::class, 'statistik'])->middleware('permission:pbg.view');
    Route::post('/pbg/{pbg}/file/delete', [DashboardPbgController::class, 'deleteFile'])->middleware('permission:pbg.view');
    // Detail harus sebelum /edit agar tidak ketimpa
    Route::get('/pbg/{pbg}', [DashboardPbgController::class, 'show'])->middleware('permission:pbg.view');
    Route::get('/pbg/{pbg}/edit', [DashboardPbgController::class, 'edit'])->middleware('permission:pbg.view');
    Route::put('/pbg/{pbg}', [DashboardPbgController::class, 'update'])->middleware('permission:pbg.view');
    Route::delete('/pbg/{pbg}', [DashboardPbgController::class, 'destroy'])->middleware('permission:pbg.view');

    // NIB listing, import, and export
    Route::get('/nib', [NibController::class, 'index'])->name('nib.index')->middleware('permission:nib.view');
    Route::post('/nib/import', [NibController::class, 'import'])->name('nib.import')->middleware('permission:nib.view');
    Route::get('/nib/export', [NibController::class, 'export'])->name('nib.export')->middleware('permission:nib.view');
    // Alternate path under /berusaha
    Route::get('/berusaha/nib', [NibController::class, 'index'])->name('nib.index.berusaha')->middleware('permission:nib.view');
    Route::post('/berusaha/nib/import', [NibController::class, 'import'])->name('nib.import.berusaha')->middleware('permission:nib.view');
    Route::get('/berusaha/nib/export', [NibController::class, 'export'])->name('nib.export.berusaha')->middleware('permission:nib.view');
    // NIB statistik
    Route::get('/nib/statistik', [NibController::class, 'statistik'])->name('nib.statistik')->middleware('permission:nib.view');
    Route::get('/berusaha/nib/statistik', [NibController::class, 'statistik'])->name('nib.statistik.berusaha')->middleware('permission:nib.view');

    // Izin listing and import
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index')->middleware('permission:izin.view');
    Route::post('/izin/import', [IzinController::class, 'import'])->name('izin.import')->middleware('permission:izin.view');
    // Alternate path under /berusaha
    Route::get('/berusaha/izin', [IzinController::class, 'index'])->name('izin.index.berusaha')->middleware('permission:izin.view');
    Route::post('/berusaha/izin/import', [IzinController::class, 'import'])->name('izin.import.berusaha')->middleware('permission:izin.view');
    // Izin export
    Route::get('/izin/export/excel', [IzinController::class, 'exportExcel'])->name('izin.export.excel')->middleware('permission:izin.view');
    Route::get('/izin/export/pdf', [IzinController::class, 'exportPdf'])->name('izin.export.pdf')->middleware('permission:izin.view');
    // Izin statistik
    Route::get('/izin/statistik', [IzinController::class, 'statistik'])->name('izin.statistik')->middleware('permission:izin.view');

    // LKPM (Laporan Kegiatan Penanaman Modal)
    Route::get('/lkpm', [App\Http\Controllers\LkpmController::class, 'index'])->name('lkpm.index')->middleware('permission:lkpm.view');
    Route::get('/lkpm/statistik', [App\Http\Controllers\LkpmController::class, 'statistik'])->name('lkpm.statistik')->middleware('permission:lkpm.view');
    Route::post('/lkpm/import-umk', [App\Http\Controllers\LkpmController::class, 'importUmk'])->name('lkpm.import.umk')->middleware('permission:lkpm.view');
     // Dedicated Non-UMK statistik route
        Route::get('/lkpm/statistik/non-umk', [App\Http\Controllers\LkpmController::class, 'statistikNonUmk'])->name('lkpm.statistikNonUmk')->middleware('permission:lkpm.view');
    Route::post('/lkpm/import-non-umk', [App\Http\Controllers\LkpmController::class, 'importNonUmk'])->name('lkpm.import.non-umk')->middleware('permission:lkpm.view');
    Route::delete('/lkpm/umk/{id}', [App\Http\Controllers\LkpmController::class, 'destroyUmk'])->name('lkpm.destroy.umk')->middleware('permission:lkpm.view');
    Route::delete('/lkpm/non-umk/{id}', [App\Http\Controllers\LkpmController::class, 'destroyNonUmk'])->name('lkpm.destroy.non-umk')->middleware('permission:lkpm.view');

    // Role / Permission helpers (protected)
    Route::get('/createrolepermission', function(){
        try{
            Role::query()->firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            Permission::create(['name' => 'view-konsultasi']);
            return 'sukses';
        } catch(\Exception $th){
            return 'gagal';
        }
    });
    Route::get('/give-user-role', function(){
        try {
            $user = User::findOrFail(1);
            $user->assignRole('admin');
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

    Route::prefix('proyek')->middleware(['auth', 'permission:verification.view'])->group(function () {
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
        ->middleware(['auth', 'permission:verification.view']);
});

// KBLI Master
    Route::get('/admin/kbli', [KbliController::class, 'index'])->name('kbli.index')->middleware(['auth', 'permission:kbli.view']);
    Route::get('/admin/kbli/import', [KbliController::class, 'importForm'])->name('kbli.import')->middleware(['auth', 'permission:kbli.view']);
    Route::post('/admin/kbli/import', [KbliController::class, 'import'])->name('kbli.import.post')->middleware(['auth', 'permission:kbli.view']);
    Route::get('/admin/kbli/import/template', [KbliController::class, 'downloadTemplate'])->name('kbli.import.template')->middleware(['auth', 'permission:kbli.view']);