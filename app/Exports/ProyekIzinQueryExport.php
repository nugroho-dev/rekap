<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProyekIzinQueryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithChunkReading, WithStrictNullComparison
{
    protected ?string $dateStart;
    protected ?string $dateEnd;
    protected ?string $year;
    protected string $search;
    protected int $no = 0;

    public function __construct(?string $dateStart, ?string $dateEnd, ?string $year, string $search = '')
    {
        $this->dateStart = $dateStart ?: null;
        $this->dateEnd = $dateEnd ?: null;
        $this->year = $year ?: null;
        $this->search = trim((string) $search);
    }

    public function query()
    {
        $q = DB::table('proyek as p')
            ->leftJoin('izin as i', 'i.id_proyek', '=', 'p.id_proyek')
            ->select([
                'p.id_proyek', 'p.nib', 'p.nama_perusahaan', 'p.nama_proyek',
                'p.kbli', 'p.judul_kbli', 'p.uraian_jenis_proyek', 'p.uraian_risiko_proyek',
                'p.day_of_tanggal_pengajuan_proyek', 'p.jumlah_investasi', 'p.tki',
                'p.alamat_usaha', 'p.kelurahan_usaha', 'p.kecamatan_usaha', 'p.kab_kota_usaha',
                'p.longitude', 'p.latitude', 'p.uraian_skala_usaha',
                'p.nama_user', 'p.email', 'p.nomor_telp',
                'i.id_permohonan_izin', 'i.uraian_jenis_perizinan', 'i.nama_dokumen',
                'i.kd_resiko', 'i.status_perizinan', 'i.day_of_tgl_izin', 'i.day_of_tanggal_terbit_oss',
            ]);

        if ($this->dateStart && $this->dateEnd) {
            $q->whereBetween('p.day_of_tanggal_pengajuan_proyek', [$this->dateStart, $this->dateEnd]);
        }
        if ($this->year) {
            $q->whereYear('p.day_of_tanggal_pengajuan_proyek', $this->year);
        }
        if ($this->search !== '') {
            $s = $this->search;
            $q->where(function ($w) use ($s) {
                $w->where('p.nib', 'like', "%$s%")
                  ->orWhere('p.nama_perusahaan', 'like', "%$s%")
                  ->orWhere('p.nama_proyek', 'like', "%$s%")
                  ->orWhere('p.kbli', 'like', "%$s%")
                  ->orWhere('p.judul_kbli', 'like', "%$s%")
                  ->orWhere('i.id_permohonan_izin', 'like', "%$s%")
                  ->orWhere('i.uraian_jenis_perizinan', 'like', "%$s%")
                  ->orWhere('i.status_perizinan', 'like', "%$s%")
                  ->orWhere('i.kd_resiko', 'like', "%$s%");
            });
        }

        return $q->orderBy('p.day_of_tanggal_pengajuan_proyek', 'asc')
                 ->orderBy('i.day_of_tgl_izin', 'asc');
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Proyek',
            'NIB',
            'Nama Perusahaan',
            'Nama Proyek',
            'KBLI',
            'Judul KBLI',
            'Jenis Proyek',
            'Risiko Proyek',
            'Tgl Pengajuan Proyek',
            'Jumlah Investasi',
            'TKI',
            'Alamat Usaha',
            'Kelurahan Usaha',
            'Kecamatan Usaha',
            'Kab/Kota Usaha',
            'Longitude',
            'Latitude',
            'Skala Usaha',
            'Kontak Nama',
            'Kontak Email',
            'Kontak Telp',
            'ID Permohonan Izin',
            'Jenis Perizinan',
            'Nama Dokumen',
            'Resiko Izin',
            'Status Perizinan',
            'Tgl Izin',
            'Tgl Terbit OSS',
        ];
    }

    public function map($row): array
    {
        $this->no++;
        return [
            $this->no,
            $row->id_proyek,
            $row->nib,
            $row->nama_perusahaan,
            $row->nama_proyek,
            $row->kbli,
            $row->judul_kbli,
            $row->uraian_jenis_proyek,
            $row->uraian_risiko_proyek,
            $row->day_of_tanggal_pengajuan_proyek,
            $row->jumlah_investasi,
            $row->tki,
            $row->alamat_usaha,
            $row->kelurahan_usaha,
            $row->kecamatan_usaha,
            $row->kab_kota_usaha,
            $row->longitude,
            $row->latitude,
            $row->uraian_skala_usaha,
            $row->nama_user,
            $row->email,
            $row->nomor_telp,
            $row->id_permohonan_izin,
            $row->uraian_jenis_perizinan,
            $row->nama_dokumen,
            $row->kd_resiko,
            $row->status_perizinan,
            $row->day_of_tgl_izin,
            $row->day_of_tanggal_terbit_oss,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => '@', // NIB as text
            'K' => '"Rp"#,##0', // investasi
            'L' => NumberFormat::FORMAT_NUMBER, // TKI
            'Q' => NumberFormat::FORMAT_NUMBER_00, // longitude
            'R' => NumberFormat::FORMAT_NUMBER_00, // latitude
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
