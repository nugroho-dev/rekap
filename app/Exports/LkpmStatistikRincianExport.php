<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LkpmStatistikRincianExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    protected Collection $rows;
    protected array $headings;
    protected array $columnFormats;

    public function __construct(Collection $rows, array $headings, array $columnFormats = [])
    {
        $this->rows = $rows;
        $this->headings = $headings;
        $this->columnFormats = $columnFormats;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function columnFormats(): array
    {
        return $this->columnFormats;
    }
}
