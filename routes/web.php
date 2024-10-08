<?php

use App\Http\Controllers\Api\ApiCekSicantikController;
use App\Http\Controllers\Api\TteController;
use App\Models\User;
use PhpParser\Node\Stmt\TryCatch;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\KonsultasiDashboardController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PengawasanDashboardController;
use App\Http\Controllers\ProdukHukumDashboardController;
use App\Http\Controllers\SicantikApiController;
use App\Http\Controllers\SicantikProsesController;
use App\Http\Controllers\SigumilangDashboardController;
use App\Http\Controllers\UsersDashboardController;
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