<?php

namespace Tests\Unit;

use App\Imports\LkpmNonUmkImport;
use PHPUnit\Framework\TestCase;

class LkpmNonUmkImportTest extends TestCase
{
    public function test_parses_indonesian_formatted_numbers()
    {
        $import = new LkpmNonUmkImport();

        $row = [
            'no_laporan' => 'NL-1',
            'nilai_total_investasi_rencana' => '9.367.341.840',
            'nilai_modal_tetap_rencana' => '109.850.000.000',
            'total_tambahan_investasi' => '1.234.567,89',
            'jumlah_realisasi_tki' => '1.234',
        ];

        $model = $import->model($row);

        $this->assertNotNull($model);
        $this->assertSame('NL-1', $model->getAttribute('no_laporan'));
        $this->assertEquals(9367341840.0, (float)$model->getAttribute('nilai_total_investasi_rencana'));
        $this->assertEquals(109850000000.0, (float)$model->getAttribute('nilai_modal_tetap_rencana'));
        $this->assertEquals(1234567.89, (float)$model->getAttribute('total_tambahan_investasi'));
        $this->assertEquals(1234, (int)$model->getAttribute('jumlah_realisasi_tki'));
    }

    public function test_maps_kabupaten_kota_header_with_slash()
    {
        $import = new LkpmNonUmkImport();

        $row = [
            'no_laporan' => 'NL-2',
            'KABUPATEN/KOTA' => 'Kab. Sleman',
        ];

        $model = $import->model($row);

        $this->assertNotNull($model);
        $this->assertSame('Kab. Sleman', $model->getAttribute('kabupaten_kota'));
    }

    public function test_parses_compact_numeric_dates_without_treating_them_as_excel_serials()
    {
        $import = new LkpmNonUmkImport();
        $reflection = new \ReflectionClass($import);
        $method = $reflection->getMethod('parseTanggal');
        $method->setAccessible(true);

        $parsed = $method->invoke($import, '20240929');

        $this->assertSame('2024-09-29', $parsed);
    }

    public function test_parses_common_excel_tanggal_laporan_formats()
    {
        $import = new LkpmNonUmkImport();
        $reflection = new \ReflectionClass($import);
        $method = $reflection->getMethod('parseTanggal');
        $method->setAccessible(true);

        $this->assertSame('2024-09-29', $method->invoke($import, '29/09/2024'));
        $this->assertSame('2024-09-29', $method->invoke($import, '2024-09-29'));
        $this->assertSame('2024-09-29', $method->invoke($import, 45564));
    }

    public function test_skips_duplicate_no_laporan_and_same_no_kode_proyek_in_file()
    {
        $import = new LkpmNonUmkImport();

        $first = $import->model([
            'no_laporan' => 'NL-10',
            'no_kode_proyek' => 'PRJ-1',
        ]);

        $second = $import->model([
            'no_laporan' => 'NL-10',
            'no_kode_proyek' => 'PRJ-1',
        ]);

        $summary = $import->summary();

        $this->assertNotNull($first);
        $this->assertNull($second);
        $this->assertCount(1, $summary['duplicates']);
    }

    public function test_skips_duplicate_when_no_kode_proyek_empty_with_same_no_laporan_in_file()
    {
        $import = new LkpmNonUmkImport();

        $first = $import->model([
            'no_laporan' => 'NL-20',
            'no_kode_proyek' => 'PRJ-2',
        ]);

        $second = $import->model([
            'no_laporan' => 'NL-20',
            'no_kode_proyek' => '',
        ]);

        $summary = $import->summary();

        $this->assertNotNull($first);
        $this->assertNull($second);
        $this->assertCount(1, $summary['duplicates']);
    }
}
