<?php

namespace App\Exports\Queued;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use App\Models\ProyekVerification;
use App\Models\Proyek;

class ProyekVerifiedQueuedExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue, WithChunkReading, WithEvents
{
    protected int $year;
    protected int $month;
    protected string $q;
    protected string $penanaman;
    protected string $kbli_status;
    protected array $meta;

    public function __construct(int $year, int $month, string $q = '', string $penanaman = 'all', string $kbli_status = 'all', array $meta = [])
    {
        $this->year = $year;
        $this->month = $month;
        $this->q = $q;
        $this->penanaman = $penanaman;
        $this->kbli_status = $kbli_status;
        $this->meta = $meta;
    }

    /**
     * Build the Eloquent query for export. Use eager-load to fetch proyek.
     */
    public function query()
    {
        $query = ProyekVerification::with('proyek')
            ->where('status', 'verified')
            ->whereNotNull('verified_at')
            ->whereYear('verified_at', $this->year)
            ->whereMonth('verified_at', $this->month)
            ->orderBy('verified_at', 'desc');

        if (!empty($this->q)) {
            $q = $this->q;
            $query->whereHas('proyek', function ($p) use ($q) {
                $p->where('nama_perusahaan', 'like', "%{$q}%")
                  ->orWhere('nama_proyek', 'like', "%{$q}%")
                  ->orWhere('nib', 'like', "%{$q}%");
            });
        }

        if ($this->penanaman === 'pma') {
            $query->whereHas('proyek', function ($p) {
                $p->whereRaw("LOWER(uraian_status_penanaman_modal) LIKE '%pma%'");
            });
        } elseif ($this->penanaman === 'pmdn') {
            $query->whereHas('proyek', function ($p) {
                $p->whereRaw("LOWER(uraian_status_penanaman_modal) LIKE '%pmdn%'");
            });
        }

        if ($this->kbli_status === 'baru') {
            $query->whereRaw("LOWER(status_kbli) LIKE '%baru%'");
        } elseif ($this->kbli_status === 'lama') {
            $query->whereRaw("LOWER(status_kbli) = 'lama'");
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No', 'Id Proyek', 'Nama Perusahaan', 'NIB', 'Nama Proyek', 'KBLI', 'Judul KBLI', 'Jumlah Investasi', 'Tenaga Kerja', 'Penanaman', 'Status Perusahaan', 'Status KBLI'
        ];
    }

    public function map($row): array
    {
        // $row is an instance of ProyekVerification with relation 'proyek'
        return [
            $row->id, // temporary row number placeholder; will be replaced client-side if needed
            $row->id_proyek,
            optional($row->proyek)->nama_perusahaan ?? '-',
            (string) (optional($row->proyek)->nib ?? '-'),
            optional($row->proyek)->nama_proyek ?? '-',
            optional($row->proyek)->kbli ?? '-',
            optional($row->proyek)->judul_kbli ?? '-',
            optional($row->proyek)->jumlah_investasi ? (float) optional($row->proyek)->jumlah_investasi : 0,
            optional($row->proyek)->tki ? (int) optional($row->proyek)->tki : 0,
            optional($row->proyek)->uraian_status_penanaman_modal ?? '-',
            $row->status_perusahaan ?? '-',
            $row->status_kbli ?? '-',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                if (empty($this->meta)) return;
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $metaCount = count($this->meta);
                $sheet->insertNewRowBefore(1, $metaCount);
                for ($i = 0; $i < $metaCount; $i++) {
                    $r = $i + 1;
                    $sheet->setCellValue('A' . $r, $this->meta[$i]);
                    $sheet->mergeCells("A{$r}:{$highestColumn}{$r}");
                    $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFont()->setBold(true);
                    $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                }
            }
        ];
    }
}
