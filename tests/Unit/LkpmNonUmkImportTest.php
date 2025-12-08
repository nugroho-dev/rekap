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
}
