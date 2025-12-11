<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Komitmen;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

class KomitmenImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    public $importedCount = 0;
    public $updatedCount = 0;
    public $errorCount = 0;
    public $failedRows = [];
    private function generateIdRule($tanggal, $namaPelakuUsaha, $nib)
    {
        $date = Carbon::parse($tanggal)->format('Ymd');
        $hash = substr(md5($tanggal . $namaPelakuUsaha . $nib), 0, 6);
        return "KM-{$date}-" . strtoupper($hash);
    }

    private function parseTanggal($value)
    {
        if (empty($value)) {
            return null;
        }

        // Jika nilai adalah angka (Excel serial date)
        if (is_numeric($value)) {
            try {
                return Carbon::instance(Date::excelToDateTimeObject($value))->toDateString();
            } catch (\Exception $e) {
                return null;
            }
        }

        // Jika nilai adalah string dengan format Indonesia
        if (is_string($value)) {
            // Map bulan Indonesia ke Inggris
            $bulanIndonesia = [
                'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
                'April' => 'April', 'Mei' => 'May', 'Juni' => 'June',
                'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September',
                'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December'
            ];
            
            $tanggalStr = str_replace(array_keys($bulanIndonesia), array_values($bulanIndonesia), $value);
            
            try {
                return Carbon::parse($tanggalStr)->toDateString();
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Parse tanggal
        $tanggal = $this->parseTanggal($row['tanggal_terbit_izin'] ?? null);
        
        if (!$tanggal) {
            $this->errorCount++;
            return null;
        }

        $namaPelakuUsaha = trim($row['nama_pelaku_usaha'] ?? '');
        $nib = trim($row['nib'] ?? '');
        $namaProyek = trim($row['nama_proyek'] ?? '');

        // Cek duplikasi berdasarkan tanggal, nama pelaku usaha, dan NIB
        $existingData = Komitmen::where('tanggal_izin_terbit', $tanggal)
            ->where('nama_pelaku_usaha', $namaPelakuUsaha)
            ->where('nib', $nib)
            ->first();

        if ($existingData) {
            // Update data yang sudah ada
            $existingData->update([
                'alamat_pelaku_usaha' => $row['alamat_pelaku_usaha'] ?? '',
                'nama_proyek' => $namaProyek,
                'jenis_izin' => $row['jenis_izin'] ?? '',
                'status' => $row['status'] ?? '',
                'tanggal_izin_terbit' => $tanggal,
                'keterangan' => $row['keterangan'] ?? '',
            ]);
            $this->updatedCount++;
            return null;
        }

        // Generate ID Rule otomatis
        $idRule = $this->generateIdRule($tanggal, $namaPelakuUsaha, $nib);

        $this->importedCount++;
        return new Komitmen([
            'id_rule' => $idRule,
            'nama_pelaku_usaha' => $namaPelakuUsaha,
            'alamat_pelaku_usaha' => $row['alamat_pelaku_usaha'] ?? '',
            'nib' => $nib,
            'nama_proyek' => $namaProyek,
            'jenis_izin' => $row['jenis_izin'] ?? '',
            'status' => $row['status'] ?? '',
            'tanggal_izin_terbit' => $tanggal,
            'keterangan' => $row['keterangan'] ?? '',
        ]);
    }
    public function rules(): array
    {
        return [
            'nama_pelaku_usaha' => 'required',
            'nama_proyek' => 'required',
            'nib' => 'required',
        ];
    }

    public function onError(Throwable $e)
    {
        $this->errorCount++;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failedRows[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
            ];
        }
        $this->errorCount++;
    }
}
