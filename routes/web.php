<?php

use App\Http\Controllers\Api\ApiCekSicantikController;
use App\Http\Controllers\Api\TteController;
use App\Http\Controllers\DashboardBerusahaController;
use App\Http\Controllers\DashboardBimtekController;
use App\Http\Controllers\DashboardBusinessController;
use App\Models\User;
use PhpParser\Node\Stmt\TryCatch;
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
use App\Http\Controllers\DashboardPengawasanController;
use App\Http\Controllers\DashboardVprosesSicantikController;
use App\Http\Controllers\DayOffDashboardController;
use App\Http\Controllers\InsentifController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\KonsultasiDashboardController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PengawasanDashboardController;
use App\Http\Controllers\PetaPotensiController;
use App\Http\Controllers\ProdukHukumDashboardController;
use App\Http\Controllers\SicantikApiController;
use App\Http\Controllers\SicantikDashboardController;
use App\Http\Controllers\SicantikProsesController;
use App\Http\Controllers\SigumilangDashboardController;
use App\Http\Controllers\UsersDashboardController;
use App\Http\Controllers\VerifikasiRealisasiInvestasiController;
use App\Models\Instansi;

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

//Route::get('/', function () {
    //return view('welcome');
//});
Route::get('/apicek/{no_permohonan}/{email}', [ApiCekSicantikController::class, 'index']);
Route::get('/unduh/{no_permohonan}/{email}', [TteController::class, 'index']);
Route::get('/', [SicantikApiController::class, 'index']);
Route::post('/', [SicantikApiController::class, 'index']);
Route::get('/kirim/{id}', [SicantikApiController::class, 'kirim']);
Route::get('/kirim/dokumen/{id}', [SicantikApiController::class, 'dokumen']);
Route::post('/kirim/dokumen/{id}', [SicantikApiController::class, 'dokumen']);
Route::get('/send-mail', [MailController::class, 'index']);
Route::get('/proses', [SicantikProsesController::class, 'index']);
Route::post('/proses', [SicantikProsesController::class, 'index']);
Route::post('/send-mail/{id}', [MailController::class, 'index']);
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/logout', [LoginController::class, 'logout']);
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
Route::get('/maintenance', [MaintenanceController::class, 'index'])->middleware('auth');
Route::get('/konfigurasi/pegawai/checkSlug', [PegawaiController::class, 'checkSlug'])->middleware('auth');
Route::resource('/konfigurasi/pegawai', PegawaiController::class)->middleware('auth');
Route::get('/konfigurasi/instansi/checkSlug', [InstansiController::class, 'checkSlug'])->middleware('auth');
Route::resource('/konfigurasi/instansi', InstansiController::class)->middleware('auth');
Route::get('/konfigurasi/user/checkSlug', [UsersDashboardController::class, 'checkSlug'])->middleware('auth');
Route::resource('/konfigurasi/user', UsersDashboardController::class)->middleware('auth');
Route::get('/pelayanan/konsultasi/display', [KonsultasiDashboardController::class, 'display'])->middleware('auth');
Route::post('/pelayanan/konsultasi/display', [KonsultasiDashboardController::class, 'display'])->middleware('auth');
Route::post('/pelayanan/konsultasi/print', [KonsultasiDashboardController::class, 'print'])->middleware('auth');
Route::post('/pelayanan/konsultasi/cari', [KonsultasiDashboardController::class, 'cari'])->middleware('auth');
Route::get('/pelayanan/konsultasi/checkSlug', [KonsultasiDashboardController::class, 'checkSlug'])->middleware('auth');
Route::resource('/pelayanan/konsultasi', KonsultasiDashboardController::class)->middleware('auth');
Route::get('/pengaduan/pengaduan/display', [PengaduanController::class, 'display'])->middleware('auth');
Route::post('/pengaduan/pengaduan/display', [PengaduanController::class, 'display'])->middleware('auth');
Route::get('/pengaduan/pengaduan/tandaterima/{item:slug}', [PengaduanController::class, 'printtandaterima'])->middleware('auth');
Route::get('/pengaduan/pengaduan/klasifikasi/{item:slug}', [PengaduanController::class, 'klasifikasi'])->middleware('auth');
Route::post('/pengaduan/pengaduan/klasifikasi/{item:slug}', [PengaduanController::class, 'updateklasifikasi'])->middleware('auth');
Route::post('/pengaduan/pengaduan/print', [PengaduanController::class, 'print'])->middleware('auth');
Route::post('/pengaduan/pengaduan/cari', [PengaduanController::class, 'cari'])->middleware('auth');
Route::get('/pengaduan/pengaduan/checkSlug', [PengaduanController::class, 'checkSlug'])->middleware('auth');
Route::resource('/pengaduan/pengaduan', PengaduanController::class)->middleware('auth');
Route::resource('/pengawasan/sigumilang', SigumilangDashboardController::class)->middleware('auth');
Route::get('/pengawasan/sigumilang/{id_proyek}/histori/{nib}', [SigumilangDashboardController::class,'histori'])->middleware('auth');
Route::get('/pengawasan/laporan/sigumilang', [SigumilangDashboardController::class,'laporan'])->middleware('auth');
Route::resource('/deregulasi/hukum', ProdukHukumDashboardController::class)->middleware('auth');
Route::get('/deregulasi/checkSlug', [ProdukHukumDashboardController::class, 'checkSlug'])->middleware('auth');
Route::resource('/insentif/permohonan', InsentifController::class)->middleware('auth');
Route::get('/insentif/checkSlug', [InsentifController::class, 'checkSlug'])->middleware('auth');
Route::resource('/peta/potensi', PetaPotensiController::class)->middleware('auth');
Route::get('/peta/checkSlug', [PetaPotensiController::class, 'checkSlug'])->middleware('auth');
Route::resource('/realiasi/investasi/verifikasi', VerifikasiRealisasiInvestasiController::class)->middleware('auth');
Route::get('/np', [SicantikDashboardController::class, 'index'])->middleware('auth');
Route::get('/berusaha', [DashboardBerusahaController::class, 'index'])->middleware('auth');
Route::post('/berusaha', [DashboardBerusahaController::class, 'index'])->middleware('auth');
Route::post('/berusaha/import_excel', [DashboardBerusahaController::class, 'import_excel'])->middleware('auth');
Route::get('/berusaha/statistik', [DashboardBerusahaController::class, 'statistik'])->middleware('auth');
Route::get('/pengawasan', [DashboardPengawasanController::class, 'index'])->middleware('auth');
Route::post('/pengawasan', [DashboardPengawasanController::class, 'index'])->middleware('auth');
Route::get('/pengawasan/{item:nomor_kode_proyek}', [DashboardPengawasanController::class, 'edit'])->middleware('auth');
Route::post('/pengawasan/{item:nomor_kode_proyek}', [DashboardPengawasanController::class, 'update'])->middleware('auth');
Route::post('/imporpengawasan/import_excel', [DashboardPengawasanController::class, 'import_excel'])->middleware('auth');
Route::get('/pengawasan/statistik', [DashboardPengawasanController::class, 'statistik'])->middleware('auth');
Route::resource('/bimtek', DashboardBimtekController::class)->middleware('auth');
Route::post('/bimtek/import_excel', [DashboardBimtekController::class, 'import_excel'])->middleware('auth');
Route::post('/bimtek', [DashboardBimtekController::class, 'index'])->middleware('auth');
Route::resource('/fasilitasi', DashboardFasilitasiController::class)->middleware('auth');
Route::post('/fasilitasi/import_excel', [DashboardFasilitasiController::class, 'import_excel'])->middleware('auth');
Route::post('/fasilitasi', [DashboardFasilitasiController::class, 'index'])->middleware('auth');
Route::resource('/loi', DashboardLoiController::class)->middleware('auth');
Route::post('/loisort', [DashboardLoiController::class, 'index'])->middleware('auth');
Route::get('/loi/check/checkSlug', [DashboardLoiController::class, 'checkSlug'])->middleware('auth');
Route::resource('/expo', DashboardExpoController::class)->middleware('auth');
Route::post('/exposort', [DashboardExpoController::class, 'index'])->middleware('auth');
Route::get('/expo/check/checkSlug', [DashboardExpoController::class, 'checkSlug'])->middleware('auth');
Route::resource('/business', DashboardBusinessController::class)->middleware('auth');
Route::post('/bisnissort', [DashboardBusinessController::class, 'index'])->middleware('auth');
Route::get('/bisnis/check/checkSlug', [DashboardBusinessController::class, 'checkSlug'])->middleware('auth');
Route::resource('/commitment', DashboardKomitmenController::class)->middleware('auth');
Route::post('/commitment/import_excel', [DashboardKomitmenController::class, 'import_excel'])->middleware('auth');
Route::post('/komitmensort', [DashboardKomitmenController::class, 'index'])->middleware('auth');
Route::get('/sicantik', [DashboardVprosesSicantikController::class, 'index'])->middleware('auth');
Route::get('/dayoff/sync', [DayOffDashboardController::class, 'handle'])->middleware('auth');
Route::post('/dayoff/sync', [DayOffDashboardController::class, 'handle'])->middleware('auth');
Route::get('/dayoff', [DayOffDashboardController::class, 'index'])->middleware('auth');
Route::post('/dayoff', [DayOffDashboardController::class, 'index'])->middleware('auth');

Route::get('/createrolepermission', function(){
    try{
        Role::create(['name' => 'administrator']);
        Permission::create(['name' => 'view-konsultasi']);
        echo "sukses";
    }catch(Exception $th){
        echo "gagal";
    }
});
Route::get('/give-user-role', function(){
    try {
        $user = User::findorfail(1);
        $user->assignRole('administrator');
        echo "sukses";
    } catch (Exception $th) {
        echo "gagal";
    }
});

Route::get('/give-user-permission', function () {
    try {
        $role = Role::findorfail(1);
        $role->givePermissionTo('view-konsultasi');
        echo "sukses";
    } catch (Exception $th) {
        echo "gagal";
    }
});