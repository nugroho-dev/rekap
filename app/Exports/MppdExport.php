<?php

namespace App\Exports;

use App\Models\Mppd;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Carbon;

class MppdExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $q = Mppd::query();
        if (!empty($this->filters['search'])) {
            $s = $this->filters['search'];
            $q->where(function($sub) use ($s){
                $sub->where('nama','LIKE',"%$s%")
                    ->orWhere('nik','LIKE',"%$s%")
                    ->orWhere('nomor_register','LIKE',"%$s%")
                    ->orWhere('profesi','LIKE',"%$s%")
                    ->orWhere('tempat_praktik','LIKE',"%$s%")
                    ->orWhere('nomor_sip','LIKE',"%$s%")
                    ->orWhere('keterangan','LIKE',"%$s%");
            });
        }
        if (!empty($this->filters['date_start']) && !empty($this->filters['date_end'])) {
            $start = $this->filters['date_start'];
            $end = $this->filters['date_end'];
            if ($start <= $end) {
                $q->whereBetween('tanggal_sip', [$start,$end]);
            }
        }
        if (!empty($this->filters['month']) && !empty($this->filters['year'])) {
            $q->whereMonth('tanggal_sip', $this->filters['month'])
              ->whereYear('tanggal_sip', $this->filters['year']);
        } elseif(!empty($this->filters['year'])) {
            $q->whereYear('tanggal_sip', $this->filters['year']);
        }
        return $q->orderBy('tanggal_sip','desc');
    }

    public function headings(): array
    {
        return [
            'Nomor Register','NIK','Nama','Alamat','Email','Telp','Nomor STR','Masa STR','Profesi','Tempat Praktik','Alamat Praktik','Nomor SIP','Tanggal SIP','Tanggal Akhir SIP','Keterangan'
        ];
    }

    public function map($mppd): array
    {
        return [
            $mppd->nomor_register,
            $mppd->nik,
            $mppd->nama,
            $mppd->alamat,
            $mppd->email,
            $mppd->nomor_telp,
            $mppd->nomor_str,
            $mppd->masa_berlaku_str,
            $mppd->profesi,
            $mppd->tempat_praktik,
            $mppd->alamat_tempat_praktik,
            $mppd->nomor_sip,
            $mppd->tanggal_sip,
            $mppd->tanggal_akhir_sip,
            $mppd->keterangan,
        ];
    }
}
