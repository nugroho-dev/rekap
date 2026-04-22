<?php

namespace Tests\Unit;

use App\Imports\LkpmUmkImport;
use PHPUnit\Framework\TestCase;

class LkpmUmkImportTest extends TestCase
{
    public function test_returns_model_for_valid_row()
    {
        $import = new LkpmUmkImport();

        $model = $import->model([
            'id_laporan' => 'ID-001',
        ]);

        $this->assertNotNull($model);
        $this->assertSame('ID-001', $model->getAttribute('id_laporan'));
    }

    public function test_returns_null_when_id_laporan_empty()
    {
        $import = new LkpmUmkImport();

        $model = $import->model([
            'id_laporan' => '',
        ]);

        $summary = $import->summary();

        $this->assertNull($model);
        $this->assertCount(1, $summary['failed']);
    }

    public function test_skips_duplicate_id_laporan_in_same_file()
    {
        $import = new LkpmUmkImport();

        $first = $import->model([
            'id_laporan' => 'ID-DUPL',
        ]);

        $second = $import->model([
            'id_laporan' => 'ID-DUPL',
        ]);

        $summary = $import->summary();

        $this->assertNotNull($first);
        $this->assertNull($second);
        $this->assertCount(1, $summary['duplicates']);
    }

    public function test_allows_different_id_laporan_in_same_file()
    {
        $import = new LkpmUmkImport();

        $first = $import->model([
            'id_laporan' => 'ID-A',
        ]);

        $second = $import->model([
            'id_laporan' => 'ID-B',
        ]);

        $summary = $import->summary();

        $this->assertNotNull($first);
        $this->assertNotNull($second);
        $this->assertEmpty($summary['duplicates']);
    }

    public function test_maps_kabupaten_kota_header_with_slash()
    {
        $import = new LkpmUmkImport();

        $model = $import->model([
            'id_laporan' => 'ID-KAB',
            'KABUPATEN/KOTA' => 'Kab. Bantul',
        ]);

        $this->assertNotNull($model);
        $this->assertSame('Kab. Bantul', $model->getAttribute('kab_kota'));
    }

    public function test_summary_counts_success()
    {
        $import = new LkpmUmkImport();

        $import->model(['id_laporan' => 'ID-S1']);
        $import->model(['id_laporan' => 'ID-S2']);

        $summary = $import->summary();

        $this->assertSame(2, $summary['success']);
        $this->assertEmpty($summary['duplicates']);
    }
}
