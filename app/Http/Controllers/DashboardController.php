<?php

namespace App\Http\Controllers;

use App\Models\Insentif;
use App\Models\Pengaduan;
use App\Models\Hukum;
use App\Models\Potensi;
use App\Models\Pengawasan;
use App\Models\Expo;
use App\Models\Bisnis;
use App\Models\Loi;
use App\Models\Konsultasi;
use App\Models\Mppd;
use App\Models\Vsimpel;
use Illuminate\Http\Request;
use App\Models\Proses;
use Carbon\Carbon;
use App\Models\Berusaha;
use App\Models\Izin;
use App\Models\Proyek;
use App\Models\LkpmNonUmk;
use App\Models\LkpmUmk;
use App\Models\Nib;
use App\Models\Fasilitasi;
use App\Models\Bimtek;
use App\Models\Komitmen;
use App\Models\ProyekVerification;
use App\Models\Pbg;

class DashboardController extends Controller
{
    public function index()
    {
        $judul = 'Data Hub DASHBOARD';
        $year = date('Y');
        // Data bulanan untuk grafik
        $monthlyInsentif = Insentif::selectRaw('MONTH(tanggal_permohonan) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_permohonan', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyPengawasan = Pengawasan::selectRaw('MONTH(hari_penjadwalan) as bulan, COUNT(*) as total')
            ->whereYear('hari_penjadwalan', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyExpo = Expo::selectRaw('MONTH(tanggal_mulai) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_mulai', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyBusinessMeeting = Bisnis::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->where('del', 0)
            ->whereYear('tanggal', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyLoi = Loi::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->whereNull('deleted_at')
            ->whereYear('tanggal', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyBerusaha = Berusaha::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyIzin = Izin::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyProyek = Proyek::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyFasilitasi = Fasilitasi::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->whereYear('tanggal', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyBimtek = Bimtek::selectRaw('MONTH(tanggal_pelaksanaan) as bulan, COUNT(*) as total')
            ->where('del', 0)
            ->whereYear('tanggal_pelaksanaan', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyKomitmen = Komitmen::selectRaw('MONTH(tanggal_izin_terbit) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_izin_terbit', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyPengaduan = Pengaduan::selectRaw('MONTH(tanggal_terima) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_terima', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyProdukHukum = Hukum::selectRaw('MONTH(tanggal_pengundangan) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_pengundangan', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyPetaPotensi = Potensi::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyKonsultasi = Konsultasi::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->whereYear('tanggal', $year)
            ->where('jenis', 'Konsultasi')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyInformasi = Konsultasi::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->whereYear('tanggal', $year)
            ->where('jenis', 'Informasi')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyProyekVerification = ProyekVerification::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyPbg = Pbg::selectRaw('MONTH(tgl_terbit) as bulan, COUNT(*) as total')
            ->whereYear('tgl_terbit', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlySimpel = Vsimpel::selectRaw('MONTH(rekomendasi) as bulan, COUNT(*) as total')
            ->whereYear('rekomendasi', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyMppd = Mppd::selectRaw('MONTH(tanggal_sip) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_sip', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $monthlyIzinTerbitSicantik = Proses::selectRaw('MONTH(end_date) as bulan, COUNT(*) as total')
            ->where('status', 'Selesai')
            ->where('jenis_proses_id', 40)
            ->whereNotNull('end_date')
            ->whereYear('end_date', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();
        $monthlyNib = Nib::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        // Total LOI tahun berjalan
        $totalLoi = Loi::whereNull('deleted_at')->whereYear('tanggal', $year)->count();
        $lastUpdateLoiObj = Loi::whereNull('deleted_at')->whereYear('tanggal', $year)->orderByDesc('updated_at')->first();
        $lastUpdateLoi = optional($lastUpdateLoiObj)->updated_at ? Carbon::parse($lastUpdateLoiObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateLoiDate = optional($lastUpdateLoiObj)->updated_at ? Carbon::parse($lastUpdateLoiObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        $totalBerusaha = Berusaha::whereYear('created_at', $year)->count();
        $lastUpdateBerusahaObj = Berusaha::whereYear('created_at', $year)->orderByDesc('updated_at')->first();
        $lastUpdateBerusaha = optional($lastUpdateBerusahaObj)->updated_at ? Carbon::parse($lastUpdateBerusahaObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateBerusahaDate = optional($lastUpdateBerusahaObj)->updated_at ? Carbon::parse($lastUpdateBerusahaObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;
        $totalIzin = Izin::whereYear('created_at', $year)->count();
        $lastUpdateIzinObj = Izin::whereYear('created_at', $year)->orderByDesc('updated_at')->first();
        $lastUpdateIzin = optional($lastUpdateIzinObj)->updated_at ? Carbon::parse($lastUpdateIzinObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateIzinDate = optional($lastUpdateIzinObj)->updated_at ? Carbon::parse($lastUpdateIzinObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;
        $totalProyek = Proyek::whereYear('created_at', $year)->count();
        $lastUpdateProyekObj = Proyek::whereYear('created_at', $year)->orderByDesc('updated_at')->first();
        $lastUpdateProyek = optional($lastUpdateProyekObj)->updated_at ? Carbon::parse($lastUpdateProyekObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateProyekDate = optional($lastUpdateProyekObj)->updated_at ? Carbon::parse($lastUpdateProyekObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;
        $totalLkpmNonUmk = LkpmNonUmk::whereYear('created_at', $year)->count();
        $lastUpdateLkpmNonUmkObj = LkpmNonUmk::whereYear('created_at', $year)->orderByDesc('updated_at')->first();
        $lastUpdateLkpmNonUmk = optional($lastUpdateLkpmNonUmkObj)->updated_at ? Carbon::parse($lastUpdateLkpmNonUmkObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateLkpmNonUmkDate = optional($lastUpdateLkpmNonUmkObj)->updated_at ? Carbon::parse($lastUpdateLkpmNonUmkObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;
        $totalLkpmUmk = LkpmUmk::whereYear('created_at', $year)->count();
        $lastUpdateLkpmUmkObj = LkpmUmk::whereYear('created_at', $year)->orderByDesc('updated_at')->first();
        $lastUpdateLkpmUmk = optional($lastUpdateLkpmUmkObj)->updated_at ? Carbon::parse($lastUpdateLkpmUmkObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateLkpmUmkDate = optional($lastUpdateLkpmUmkObj)->updated_at ? Carbon::parse($lastUpdateLkpmUmkObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;
        $totalNib = Nib::whereYear('created_at', $year)->count();
        $lastUpdateNibObj = Nib::whereYear('created_at', $year)->orderByDesc('updated_at')->first();
        $lastUpdateNib = optional($lastUpdateNibObj)->updated_at ? Carbon::parse($lastUpdateNibObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateNibDate = optional($lastUpdateNibObj)->updated_at ? Carbon::parse($lastUpdateNibObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;
        // Fasilitasi: berdasarkan tanggal
        $totalFasilitasi = Fasilitasi::whereYear('tanggal', $year)->count();
        $lastUpdateFasilitasiObj = Fasilitasi::whereYear('tanggal', $year)->orderByDesc('updated_at')->first();
        $lastUpdateFasilitasi = optional($lastUpdateFasilitasiObj)->updated_at ? Carbon::parse($lastUpdateFasilitasiObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateFasilitasiDate = optional($lastUpdateFasilitasiObj)->updated_at ? Carbon::parse($lastUpdateFasilitasiObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        // Pengaduan: berdasarkan tanggal_terima
        $totalPengaduan = Pengaduan::whereYear('tanggal_terima', $year)->count();
        $lastUpdatePengaduanObj = Pengaduan::whereYear('tanggal_terima', $year)->orderByDesc('updated_at')->first();
        $lastUpdatePengaduan = optional($lastUpdatePengaduanObj)->updated_at ? Carbon::parse($lastUpdatePengaduanObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdatePengaduanDate = optional($lastUpdatePengaduanObj)->updated_at ? Carbon::parse($lastUpdatePengaduanObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        // Produk Hukum: berdasarkan tanggal_pengundangan
        $totalProdukHukum = Hukum::whereYear('tanggal_pengundangan', $year)->count();
        $lastUpdateProdukHukumObj = Hukum::whereYear('tanggal_pengundangan', $year)->orderByDesc('updated_at')->first();
        $lastUpdateProdukHukum = optional($lastUpdateProdukHukumObj)->updated_at ? Carbon::parse($lastUpdateProdukHukumObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateProdukHukumDate = optional($lastUpdateProdukHukumObj)->updated_at ? Carbon::parse($lastUpdateProdukHukumObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        // Peta Potensi: berdasarkan created_at
        $totalPetaPotensi = Potensi::whereYear('created_at', $year)->count();
        $lastUpdatePetaPotensiObj = Potensi::whereYear('created_at', $year)->orderByDesc('updated_at')->first();
        $lastUpdatePetaPotensi = optional($lastUpdatePetaPotensiObj)->updated_at ? Carbon::parse($lastUpdatePetaPotensiObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdatePetaPotensiDate = optional($lastUpdatePetaPotensiObj)->updated_at ? Carbon::parse($lastUpdatePetaPotensiObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;
        // Bimtek: berdasarkan tanggal_pelaksanaan dan del=0
        $totalBimtek = Bimtek::where('del', 0)->whereYear('tanggal_pelaksanaan', $year)->count();
        $lastUpdateBimtekObj = Bimtek::where('del', 0)->whereYear('tanggal_pelaksanaan', $year)->orderByDesc('updated_at')->first();
        $lastUpdateBimtek = optional($lastUpdateBimtekObj)->updated_at ? Carbon::parse($lastUpdateBimtekObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateBimtekDate = optional($lastUpdateBimtekObj)->updated_at ? Carbon::parse($lastUpdateBimtekObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        // Komitmen: berdasarkan tanggal_izin_terbit
        $totalKomitmen = Komitmen::whereYear('tanggal_izin_terbit', $year)->count();
        $lastUpdateKomitmenObj = Komitmen::whereYear('tanggal_izin_terbit', $year)->orderByDesc('updated_at')->first();
        $lastUpdateKomitmen = optional($lastUpdateKomitmenObj)->updated_at ? Carbon::parse($lastUpdateKomitmenObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateKomitmenDate = optional($lastUpdateKomitmenObj)->updated_at ? Carbon::parse($lastUpdateKomitmenObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        // Konsultasi: berdasarkan jenis dan tahun
        $totalKonsultasi = Konsultasi::whereYear('tanggal', $year)->where('jenis', 'Konsultasi')->count();
        $lastUpdateKonsultasiObj = Konsultasi::whereYear('tanggal', $year)->where('jenis', 'Konsultasi')->orderByDesc('updated_at')->first();
        $lastUpdateKonsultasi = optional($lastUpdateKonsultasiObj)->updated_at ? Carbon::parse($lastUpdateKonsultasiObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateKonsultasiDate = optional($lastUpdateKonsultasiObj)->updated_at ? Carbon::parse($lastUpdateKonsultasiObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;
        $totalInformasi = Konsultasi::whereYear('tanggal', $year)->where('jenis', 'Informasi')->count();
        $lastUpdateInformasiObj = Konsultasi::whereYear('tanggal', $year)->where('jenis', 'Informasi')->orderByDesc('updated_at')->first();
        $lastUpdateInformasi = optional($lastUpdateInformasiObj)->updated_at ? Carbon::parse($lastUpdateInformasiObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateInformasiDate = optional($lastUpdateInformasiObj)->updated_at ? Carbon::parse($lastUpdateInformasiObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;
        $totalProyekVerification = ProyekVerification::whereYear('created_at', $year)->count();
        $lastUpdateProyekVerificationObj = ProyekVerification::whereYear('created_at', $year)->orderByDesc('updated_at')->first();
        $lastUpdateProyekVerification = optional($lastUpdateProyekVerificationObj)->updated_at ? Carbon::parse($lastUpdateProyekVerificationObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateProyekVerificationDate = optional($lastUpdateProyekVerificationObj)->updated_at ? Carbon::parse($lastUpdateProyekVerificationObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        // PBG: berdasarkan tgl_terbit
        $totalPbg = Pbg::whereYear('tgl_terbit', $year)->count();
        $lastUpdatePbgObj = Pbg::whereYear('tgl_terbit', $year)->orderByDesc('updated_at')->first();
        $lastUpdatePbg = optional($lastUpdatePbgObj)->updated_at ? Carbon::parse($lastUpdatePbgObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdatePbgDate = optional($lastUpdatePbgObj)->updated_at ? Carbon::parse($lastUpdatePbgObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        // SIMPEL: berdasarkan rekomendasi
        $totalSimpel = Vsimpel::whereYear('rekomendasi', $year)->count();
        $lastUpdateSimpelObj = Vsimpel::whereYear('rekomendasi', $year)->orderByDesc('updated_at')->first();
        $lastUpdateSimpel = optional($lastUpdateSimpelObj)->updated_at ? Carbon::parse($lastUpdateSimpelObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateSimpelDate = optional($lastUpdateSimpelObj)->updated_at ? Carbon::parse($lastUpdateSimpelObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        // MPPD: berdasarkan tanggal_sip
        $totalMppd = Mppd::whereYear('tanggal_sip', $year)->count();
        $lastUpdateMppdObj = Mppd::whereYear('tanggal_sip', $year)->orderByDesc('updated_at')->first();
        $lastUpdateMppd = optional($lastUpdateMppdObj)->updated_at ? Carbon::parse($lastUpdateMppdObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateMppdDate = optional($lastUpdateMppdObj)->updated_at ? Carbon::parse($lastUpdateMppdObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;
        
        $totalBusinessMeeting = Bisnis::where('del', 0)->whereYear('tanggal', $year)->count();
        $lastUpdateBusinessMeetingObj = Bisnis::where('del', 0)->whereYear('tanggal', $year)->orderByDesc('updated_at')->first();
        $lastUpdateBusinessMeeting = optional($lastUpdateBusinessMeetingObj)->updated_at ? Carbon::parse($lastUpdateBusinessMeetingObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateBusinessMeetingDate = optional($lastUpdateBusinessMeetingObj)->updated_at ? Carbon::parse($lastUpdateBusinessMeetingObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;
        
        $totalExpo = Expo::whereYear('tanggal_mulai', $year)->count();
        $lastUpdateExpoObj = Expo::whereYear('tanggal_mulai', $year)->orderByDesc('updated_at')->first();
        $lastUpdateExpo = optional($lastUpdateExpoObj)->updated_at ? Carbon::parse($lastUpdateExpoObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateExpoDate = optional($lastUpdateExpoObj)->updated_at ? Carbon::parse($lastUpdateExpoObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;  
        
        $totalPengawasan = Pengawasan::whereYear('hari_penjadwalan', $year)->count();
        $lastUpdatePengawasanObj = Pengawasan::whereYear('hari_penjadwalan', $year)->orderByDesc('updated_at')->first();
        $lastUpdatePengawasan = optional($lastUpdatePengawasanObj)->updated_at ? Carbon::parse($lastUpdatePengawasanObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdatePengawasanDate = optional($lastUpdatePengawasanObj)->updated_at ? Carbon::parse($lastUpdatePengawasanObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        $totalInsentif = Insentif::whereYear('tanggal_permohonan', $year)->count();
        $lastUpdateInsentifObj = Insentif::whereYear('tanggal_permohonan', $year)->orderByDesc('updated_at')->first();
        $lastUpdateInsentif = optional($lastUpdateInsentifObj)->updated_at ? Carbon::parse($lastUpdateInsentifObj->updated_at)->locale('id')->diffForHumans() : null;   
        $lastUpdateInsentifDate = optional($lastUpdateInsentifObj)->updated_at ? Carbon::parse($lastUpdateInsentifObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        // Total izin terbit SiCantik: status = 'Selesai', jenis_proses_id = 40, end_date != null, end_date tahun berjalan
        $totalIzinTerbitSicantik = Proses::where('status', 'Selesai')
            ->where('jenis_proses_id', 40)
            ->whereNotNull('end_date')
            ->whereYear('end_date', $year)
            ->count();
        $lastUpdateIzinTerbitSicantikObj = Proses::where('status', 'Selesai')
            ->where('jenis_proses_id', 40)
            ->whereNotNull('end_date')
            ->whereYear('end_date', $year)
            ->orderByDesc('updated_at')->first();
        $lastUpdateIzinTerbitSicantik = optional($lastUpdateIzinTerbitSicantikObj)->updated_at ? Carbon::parse($lastUpdateIzinTerbitSicantikObj->updated_at)->locale('id')->diffForHumans() : null;
        $lastUpdateIzinTerbitSicantikDate = optional($lastUpdateIzinTerbitSicantikObj)->updated_at ? Carbon::parse($lastUpdateIzinTerbitSicantikObj->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : null;

        return view('admin.dashboard.index', compact(
            'judul',
            'totalBerusaha',
            'totalIzin',
            'totalProyek',
            'totalLkpmNonUmk',
            'totalLkpmUmk',
            'totalNib',
            'totalFasilitasi',
            'totalBimtek',
            'totalKomitmen',
            'totalProyekVerification',
            'totalPbg',
            'totalSimpel',
            'totalMppd',
            'totalIzinTerbitSicantik',
            'totalKonsultasi',
            'totalInformasi',
            'totalPengaduan',
            'totalProdukHukum',
            'totalPetaPotensi',
            'totalLoi',
            'totalBusinessMeeting',
            'totalExpo',
            'totalPengawasan',
            'totalInsentif',
            // last update
            'lastUpdateInsentif', 'lastUpdateInsentifDate',
            'lastUpdatePengawasan', 'lastUpdatePengawasanDate',
            'lastUpdateExpo', 'lastUpdateExpoDate',
            'lastUpdateBusinessMeeting', 'lastUpdateBusinessMeetingDate',
            'lastUpdateLoi', 'lastUpdateLoiDate',
            'lastUpdateBerusaha', 'lastUpdateBerusahaDate',
            'lastUpdateIzin', 'lastUpdateIzinDate',
            'lastUpdateProyek', 'lastUpdateProyekDate',
            'lastUpdateLkpmNonUmk', 'lastUpdateLkpmNonUmkDate',
            'lastUpdateLkpmUmk', 'lastUpdateLkpmUmkDate',
            'lastUpdateNib', 'lastUpdateNibDate',
            'lastUpdateFasilitasi', 'lastUpdateFasilitasiDate',
            'lastUpdatePengaduan', 'lastUpdatePengaduanDate',
            'lastUpdateProdukHukum', 'lastUpdateProdukHukumDate',
            'lastUpdatePetaPotensi', 'lastUpdatePetaPotensiDate',
            'lastUpdateBimtek', 'lastUpdateBimtekDate',
            'lastUpdateKomitmen', 'lastUpdateKomitmenDate',
            'lastUpdateKonsultasi', 'lastUpdateKonsultasiDate',
            'lastUpdateInformasi', 'lastUpdateInformasiDate',
            'lastUpdateProyekVerification', 'lastUpdateProyekVerificationDate',
            'lastUpdatePbg', 'lastUpdatePbgDate',
            'lastUpdateSimpel', 'lastUpdateSimpelDate',
            'lastUpdateMppd', 'lastUpdateMppdDate',
            'lastUpdateIzinTerbitSicantik', 'lastUpdateIzinTerbitSicantikDate',
            // monthly data for chart
            'monthlyInsentif',
            'monthlyPengawasan',
            'monthlyExpo',
            'monthlyBusinessMeeting',
            'monthlyLoi',
            'monthlyBerusaha',
            'monthlyIzin',
            'monthlyProyek',
            'monthlyFasilitasi',
            'monthlyBimtek',
            'monthlyKomitmen',
            'monthlyPengaduan',
            'monthlyProdukHukum',
            'monthlyPetaPotensi',
            'monthlyKonsultasi',
            'monthlyInformasi',
            'monthlyProyekVerification',
            'monthlyPbg',
            'monthlySimpel',
            'monthlyMppd',
            'monthlyIzinTerbitSicantik',
            'monthlyNib'
        ));
    }
}
