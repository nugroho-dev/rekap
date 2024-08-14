<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipeDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipe_dokumen')->insert([
            'nama_tipe_dokumen'  => 'Undang-undang (UU)',
            'slug'  => 'undang-undang-(uu)',
            'del'  => '0',
        ]);
        DB::table('tipe_dokumen')->insert([
            'nama_tipe_dokumen'  => 'Undang-undang Darurat',
            'slug'  => 'undang-undang-(uu)',
            'del'  => '0',
        ]);
        DB::table('tipe_dokumen')->insert([
            'nama_tipe_dokumen'=>'Peraturan Pemerintah Pengganti Undang-Undang (Perpu)',
            'slug'=>'peraturan-pemerintah-pengganti-undang-undang-(perpu)',
            'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Pemerintah (PP)',
    'slug'=>'peraturan-pemerintah-(pp)',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Presiden (Perpres)',
    'slug'=>'peraturan-presiden-(perpres)',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Presiden (Keppres)',
    'slug'=>'keputusan-presiden-(keppres)',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Instruksi Presiden (Inpres)',
    'slug'=>'instruksi-presiden-(inpres)',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Daerah (PERDA)',
    'slug'=>'peraturan-daerah-(perda)',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Qanun',
    'slug'=>'qanun',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Daerah Khusus Provinsi Papua (PERDASUS PAPUA)',
    'slug'=>'peraturan-daerah-khusus-provinsi-papua-(perdasus-papua)',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Daerah Istimewa',
    'slug'=>'peraturan-daerah-istimewa',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Gubernur (PERGUB)',
    'slug'=>'peraturan-gubernur-(pergub)',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Bupati (PERBUP)',
    'slug'=>'peraturan-bupati-(perbup)',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Walikota (PERWALI)',
    'slug'=>'peraturan-walikota-(perwali)',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Pemeriksa Keuangan',
    'slug'=>'peraturan-badan-pemeriksa-keuangan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Koordinator Bidang Kesejahteraan Rakyat',
    'slug'=>'peraturan-menteri-koordinator-bidang-kesejahteraan-rakyat',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Badan Pemeriksa Keuangan',
    'slug'=>'keputusan-badan-pemeriksa-keuangan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Dalam Negeri',
    'slug'=>'peraturan-menteri-dalam-negeri',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Keuangan',
    'slug'=>'peraturan-menteri-keuangan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Koordinator Bidang Politik, Hukum, dan Keamanan',
    'slug'=>'peraturan-menteri-koordinator-bidang-politik,-hukum,-dan-keamanan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Luar Negeri',
    'slug'=>'peraturan-menteri-luar-negeri',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Hukum dan HAM',
    'slug'=>'peraturan-menteri-hukum-dan-ham',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Pertahanan',
    'slug'=>'peraturan-menteri-pertahanan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Perhubungan',
    'slug'=>'peraturan-menteri-perhubungan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kejaksaan Republik Indonesia',
    'slug'=>'peraturan-kejaksaan-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Kepolisian Negara Republik Indonesia',
    'slug'=>'peraturan-kepala-kepolisian-negara-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Narkotika Nasional',
    'slug'=>'peraturan-kepala-badan-narkotika-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Meteorologi, Klimatologi, dan Geofisika',
    'slug'=>'peraturan-kepala-badan-meteorologi,-klimatologi,-dan-geofisika',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Siber dan Sandi Negara',
    'slug'=>'peraturan-badan-siber-dan-sandi-negara',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Komisi Nasional Hak Asasi Manusia',
    'slug'=>'peraturan-komisi-nasional-hak-asasi-manusia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Komisi Pemberantasan Korupsi',
    'slug'=>'peraturan-komisi-pemberantasan-korupsi',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Komisi Pemilihan Umum',
    'slug'=>'peraturan-komisi-pemilihan-umum',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Nasional Penanggulangan Terorisme',
    'slug'=>'peraturan-badan-nasional-penanggulangan-terorisme',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Pengawas Pemilihan Umum',
    'slug'=>'peraturan-badan-pengawas-pemilihan-umum',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Koordinasi Penanaman Modal',
    'slug'=>'peraturan-badan-koordinasi-penanaman-modal',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Menteri Keuangan',
    'slug'=>'keputusan-menteri-keuangan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Perdagangan',
    'slug'=>'peraturan-menteri-perdagangan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Perindustrian',
    'slug'=>'peraturan-menteri-perindustrian',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Negara Perencanaan Pembangunan Nasional/Kepala Badan Perencanaan Pembangunan Nasional',
    'slug'=>'peraturan-menteri-negara-perencanaan-pembangunan-nasional/kepala-badan-perencanaan-pembangunan-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Koperasi dan Usaha Kecil dan Menengah',
    'slug'=>'peraturan-menteri-koperasi-dan-usaha-kecil-dan-menengah',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Pusat Statistik',
    'slug'=>'peraturan-badan-pusat-statistik',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Pusat Statistik',
    'slug'=>'peraturan-kepala-badan-pusat-statistik',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Bank Indonesia',
    'slug'=>'peraturan-bank-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Otoritas Jasa Keuangan',
    'slug'=>'peraturan-otoritas-jasa-keuangan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Pusat Pelaporan dan Analisis Transaksi Keuangan',
    'slug'=>'peraturan-kepala-pusat-pelaporan-dan-analisis-transaksi-keuangan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Lembaga Penjamin Simpanan',
    'slug'=>'peraturan-lembaga-penjamin-simpanan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Standardisasi Nasional',
    'slug'=>'peraturan-badan-standardisasi-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Kepala Badan Standardisasi Nasional',
    'slug'=>'keputusan-kepala-badan-standardisasi-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah',
    'slug'=>'peraturan-kepala-lembaga-kebijakan-pengadaan-barang/jasa-pemerintah',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Komisi Pengawas Persaingan Usaha',
    'slug'=>'peraturan-komisi-pengawas-persaingan-usaha',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Komisi Pengawas Persaingan Usaha',
    'slug'=>'keputusan-komisi-pengawas-persaingan-usaha',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Tenaga Nuklir Nasional',
    'slug'=>'peraturan-badan-tenaga-nuklir-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Dewan Perwakilan Rakyat',
    'slug'=>'peraturan-dewan-perwakilan-rakyat',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Dewan Perwakilan Daerah',
    'slug'=>'peraturan-dewan-perwakilan-daerah',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Mahkamah Agung',
    'slug'=>'peraturan-mahkamah-agung',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Perpustakaan Nasional',
    'slug'=>'peraturan-perpustakaan-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Mahkamah Konstitusi',
    'slug'=>'peraturan-mahkamah-konstitusi',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Komisi Yudisial',
    'slug'=>'peraturan-komisi-yudisial',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Koordinator Bidang Pembangunan Manusia Dan Kebudayaan',
    'slug'=>'peraturan-menteri-koordinator-bidang-pembangunan-manusia-dan-kebudayaan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Sekretaris Negara Republik Indonesia',
    'slug'=>'peraturan-menteri-sekretaris-negara-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Sosial',
    'slug'=>'peraturan-menteri-sosial',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Pariwisata Dan Ekonomi Kreatif',
    'slug'=>'peraturan-menteri-pariwisata-dan-ekonomi-kreatif',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Ketenagakerjaan',
    'slug'=>'peraturan-menteri-ketenagakerjaan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Komunikasi dan Informatika',
    'slug'=>'peraturan-menteri-komunikasi-dan-informatika',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Pendayagunaan Aparatur Negara dan Reformasi Birokrasi',
    'slug'=>'peraturan-menteri-pendayagunaan-aparatur-negara-dan-reformasi-birokrasi',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Pemberdayaan Perempuan Dan Perlindungan Anak',
    'slug'=>'peraturan-menteri-pemberdayaan-perempuan-dan-perlindungan-anak',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Pemuda dan Olahraga',
    'slug'=>'peraturan-menteri-pemuda-dan-olahraga',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Riset, Teknologi, Dan Pendidikan Tinggi Republik Indonesia',
    'slug'=>'peraturan-menteri-riset,-teknologi,-dan-pendidikan-tinggi-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Agraria dan Tata Ruang/ Kepala Badan Pertanahan Nasional Republik Indonesia',
    'slug'=>'peraturan-menteri-agraria-dan-tata-ruang/-kepala-badan-pertanahan-nasional-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Desa, Pembangunan Daerah Tertinggal, dan Transmigrasi',
    'slug'=>'peraturan-menteri-desa,-pembangunan-daerah-tertinggal,-dan-transmigrasi',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Pengawas Tenaga Nuklir',
    'slug'=>'peraturan-badan-pengawas-tenaga-nuklir',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Tenaga Nuklir Nasional',
    'slug'=>'peraturan-kepala-badan-tenaga-nuklir-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Lembaga Ilmu Pengetahuan Indonesia',
    'slug'=>'peraturan-lembaga-ilmu-pengetahuan-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Perpustakaan Nasional',
    'slug'=>'peraturan-kepala-perpustakaan-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Nasional Penanggulangan Bencana',
    'slug'=>'peraturan-kepala-badan-nasional-penanggulangan-bencana',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Riset dan Inovasi Nasional',
    'slug'=>'peraturan-badan-riset-dan-inovasi-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Kependudukan dan Keluarga Berencana Nasional',
    'slug'=>'peraturan-kepala-badan-kependudukan-dan-keluarga-berencana-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Kepegawaian Negara',
    'slug'=>'peraturan-kepala-badan-kepegawaian-negara',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Pengawasan Keuangan dan Pembangunan',
    'slug'=>'peraturan-kepala-badan-pengawasan-keuangan-dan-pembangunan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Lembaga Administrasi Negara',
    'slug'=>'peraturan-kepala-lembaga-administrasi-negara',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Arsip Nasional Republik Indonesia',
    'slug'=>'peraturan-kepala-arsip-nasional-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Nasional Penempatan dan Perlindungan Tenaga Kerja Indonesia',
    'slug'=>'peraturan-badan-nasional-penempatan-dan-perlindungan-tenaga-kerja-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Direktur Utama Lembaga Penyiaran Publik Radio Republik Indonesia',
    'slug'=>'peraturan-direktur-utama-lembaga-penyiaran-publik-radio-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Direksi Lembaga Penyiaran Publik Radio Republik Indonesia',
    'slug'=>'peraturan-direksi-lembaga-penyiaran-publik-radio-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Informasi Geospasial',
    'slug'=>'peraturan-badan-informasi-geospasial',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Ombudsman Republik Indonesia',
    'slug'=>'peraturan-ombudsman-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Ketua Ombudsman Republik Indonesia',
    'slug'=>'peraturan-ketua-ombudsman-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Ekonomi Kreatif',
    'slug'=>'peraturan-kepala-badan-ekonomi-kreatif',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Koordinator Bidang Kemaritiman dan Investasi',
    'slug'=>'peraturan-menteri-koordinator-bidang-kemaritiman-dan-investasi',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Pertanian',
    'slug'=>'peraturan-menteri-pertanian',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Menteri Pertanian',
    'slug'=>'keputusan-menteri-pertanian',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Kelautan dan Perikanan',
    'slug'=>'keputusan-menteri-kelautan-dan-perikanan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Menteri Kelautan dan Perikanan',
    'slug'=>'keputusan-menteri-kelautan-dan-perikanan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Energi dan Sumber Daya Mineral',
    'slug'=>'peraturan-menteri-energi-dan-sumber-daya-mineral',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Menteri Energi dan Sumber Daya Mineral',
    'slug'=>'keputusan-menteri-energi-dan-sumber-daya-mineral',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Pekerjaan Umum dan Perumahan Rakyat',
    'slug'=>'peraturan-menteri-pekerjaan-umum-dan-perumahan-rakyat',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Lingkungan Hidup',
    'slug'=>'peraturan-menteri-lingkungan-hidup',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Lingkungan Hidup Dan Kehutanan',
    'slug'=>'peraturan-menteri-lingkungan-hidup-dan-kehutanan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Kepala Satuan Kerja Khusus Pelaksana Kegiatan Usaha Hulu Minyak dan Gas Bumi',
    'slug'=>'keputusan-kepala-satuan-kerja-khusus-pelaksana-kegiatan-usaha-hulu-minyak-dan-gas-bumi',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Kependudukan dan Keluarga Berencana Nasional',
    'slug'=>'peraturan-badan-kependudukan-dan-keluarga-berencana-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Instruksi Menteri Dalam Negeri',
    'slug'=>'instruksi-menteri-dalam-negeri',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Menteri Dalam Negeri',
    'slug'=>'keputusan-menteri-dalam-negeri',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Agama',
    'slug'=>'peraturan-menteri-agama',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Pengusahaan Kawasan Perdagangan dan Pelabuhan Bebas Sabang',
    'slug'=>'peraturan-kepala-badan-pengusahaan-kawasan-perdagangan-dan-pelabuhan-bebas-sabang',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Pengusahaan Kawasan Perdagangan Bebas dan Pelabuhan Bebas Batam',
    'slug'=>'peraturan-kepala-badan-pengusahaan-kawasan-perdagangan-bebas-dan-pelabuhan-bebas-batam',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Nasional Pengelola Perbatasan',
    'slug'=>'peraturan-badan-nasional-pengelola-perbatasan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Kesehatan',
    'slug'=>'peraturan-menteri-kesehatan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Menteri Kesehatan',
    'slug'=>'keputusan-menteri-kesehatan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Surat Edaran Menteri Kesehatan',
    'slug'=>'surat-edaran-menteri-kesehatan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Pendidikan dan Kebudayaan',
    'slug'=>'peraturan-menteri-pendidikan-dan-kebudayaan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Penyelenggara Jaminan Sosial Kesehatan',
    'slug'=>'peraturan-badan-penyelenggara-jaminan-sosial-kesehatan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan BPJS Ketenagakerjaan',
    'slug'=>'peraturan-bpjs-ketenagakerjaan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Pengawas Obat dan Makanan',
    'slug'=>'peraturan-kepala-badan-pengawas-obat-dan-makanan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Badan Usaha Milik Negara',
    'slug'=>'peraturan-menteri-badan-usaha-milik-negara',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Surat Edaran Menteri Badan Usaha Milik Negara',
    'slug'=>'surat-edaran-menteri-badan-usaha-milik-negara',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Koordinator Bidang Perekonomian',
    'slug'=>'peraturan-menteri-koordinator-bidang-perekonomian',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Kepegawaian Negara',
    'slug'=>'peraturan-badan-kepegawaian-negara',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah',
    'slug'=>'peraturan-lembaga-kebijakan-pengadaan-barang/jasa-pemerintah',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Surat Edaran Otoritas Jasa Keuangan',
    'slug'=>'
surat-edaran-otoritas-jasa-keuangan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Surat Edaran (SE) Kepala Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah',
    'slug'=>'surat-edaran-(se)-kepala-lembaga-kebijakan-pengadaan-barang/jasa-pemerintah',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Lembaga Perlindungan Saksi dan Korban',
    'slug'=>'peraturan-lembaga-perlindungan-saksi-dan-korban',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Jaksa Agung',
    'slug'=>'peraturan-jaksa-agung',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Arsip Nasional Republik Indonesia',
    'slug'=>'peraturan-arsip-nasional-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Ketua Komisi Nasional Hak Asasi Manusia',
    'slug'=>'peraturan-ketua-komisi-nasional-hak-asasi-manusia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Pengawas Obat dan Makanan',
    'slug'=>'peraturan-badan-pengawas-obat-dan-makanan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Meteorologi, Klimatologi, dan Geofisika',
    'slug'=>'peraturan-badan-meteorologi,-klimatologi,-dan-geofisika',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Lembaga Administrasi Negara',
    'slug'=>'peraturan-lembaga-administrasi-negara',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Gubernur Lembaga Ketahanan Nasional',
    'slug'=>'peraturan-gubernur-lembaga-ketahanan-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Keamanan Laut',
    'slug'=>'peraturan-kepala-badan-keamanan-laut',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Keamanan Laut',
    'slug'=>'peraturan-badan-keamanan-laut',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Surat Edaran Menteri Pendayagunaan Aparatur Negara dan Reformasi Birokrasi',
    'slug'=>'surat-edaran-menteri-pendayagunaan-aparatur-negara-dan-reformasi-birokrasi',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Sekretaris Kabinet',
    'slug'=>'peraturan-sekretaris-kabinet',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Nasional Penanggulangan Bencana',
    'slug'=>'peraturan-badan-nasional-penanggulangan-bencana',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Kepala Badan Nasional Penanggulangan Bencana',
    'slug'=>'keputusan-kepala-badan-nasional-penanggulangan-bencana',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepolisian Negara Republik Indonesia',
    'slug'=>'peraturan-kepolisian-negara-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi',
    'slug'=>'peraturan-menteri-pendidikan,-kebudayaan,-riset,-dan-teknologi',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Pengawasan Keuangan dan Pembangunan',
    'slug'=>'peraturan-badan-pengawasan-keuangan-dan-pembangunan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Intelijen Negara',
    'slug'=>'peraturan-badan-intelijen-negara',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Pusat Pelaporan dan Analisis Transaksi Keuangan',
    'slug'=>'peraturan-pusat-pelaporan-dan-analisis-transaksi-keuangan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Majelis Permusyawaratan Rakyat',
    'slug'=>'peraturan-majelis-permusyawaratan-rakyat',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Surat Edaran (SE) Mendagri',
    'slug'=>'surat-edaran-(se)-mendagri',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Menteri Luar Negeri',
    'slug'=>'keputusan-menteri-luar-negeri',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Narkotika Nasional',
    'slug'=>'peraturan-badan-narkotika-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Koordinasi Penanaman Modal',
    'slug'=>'peraturan-kepala-badan-koordinasi-penanaman-modal',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Standardisasi Nasional',
    'slug'=>'peraturan-kepala-badan-standardisasi-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Pengawas Tenaga Nuklir',
    'slug'=>'peraturan-kepala-badan-pengawas-tenaga-nuklir',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Informasi Geospasial',
    'slug'=>'peraturan-kepala-badan-informasi-geospasial',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Pembinaan Ideologi Pancasila',
    'slug'=>'peraturan-badan-pembinaan-ideologi-pancasila',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Dewan Direksi Lembaga Penyiaran Publik Televisi Republik Indonesia',
    'slug'=>'peraturan-dewan-direksi-lembaga-penyiaran-publik-televisi-republik-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Menteri Pendayagunaan Aparatur Negara dan Reformasi Birokrasi',
    'slug'=>'keputusan-menteri-pendayagunaan-aparatur-negara-dan-reformasi-birokrasi',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Keputusan Menteri Sosial',
    'slug'=>'keputusan-menteri-sosial',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Nasional Pencarian dan Pertolongan',
    'slug'=>'peraturan-badan-nasional-pencarian-dan-pertolongan',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Pelindungan Pekerja Migran Indonesia',
    'slug'=>'peraturan-badan-pelindungan-pekerja-migran-indonesia',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Sekretariat Jenderal Dewan Ketahanan Nasional',
    'slug'=>'peraturan-sekretariat-jenderal-dewan-ketahanan-nasional',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Kepala Badan Nasional Penanggulangan Terorisme',
    'slug'=>'peraturan-kepala-badan-nasional-penanggulangan-terorisme',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Pengelola Tabungan Perumahan Rakyat',
    'slug'=>'peraturan-badan-pengelola-tabungan-perumahan-rakyat',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Pengelola Keuangan Haji',
    'slug'=>'peraturan-badan-pengelola-keuangan-haji',
    'del'=>'0',
        ]);
    DB::table('tipe_dokumen')->insert([
        'nama_tipe_dokumen'=>'Peraturan Badan Ekonomi Kreatif',
    'slug'=>'peraturan-badan-ekonomi-kreatif',
    'del'=>'0',
        ]);
    
       
    }
}
