<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Konsultasi;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KonsultasiImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    public $importedCount = 0;
    public $updatedCount = 0;
    public $errorCount = 0;
    public $failedRows = [];

    /**
     * Generate unique id_rule based on date and name
     */
    private function generateIdRule($tanggal, $nama_pemohon, $no_hp)
    {
        $date = Carbon::parse($tanggal)->format('Ymd');
        $hash = substr(md5($tanggal . $nama_pemohon . $no_hp), 0, 6);
        
        return "KS-{$date}-" . strtoupper($hash);
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Parse tanggal dari Excel
        $tanggal = now()->toDateString(); // Default
        
        if (isset($row['tanggal']) && !empty($row['tanggal'])) {
            try {
                // Cek apakah tanggal adalah numeric (Excel date serial)
                if (is_numeric($row['tanggal'])) {
                    $tanggal = Carbon::instance(Date::excelToDateTimeObject($row['tanggal']))->toDateString();
                } else {
                    // Parsing tanggal string (support format Indonesia)
                    // Contoh: "7 Januari 2025", "07 Januari 2025"
                    $dateString = $row['tanggal'];
                    
                    // Mapping bulan Indonesia ke Inggris
                    $bulanIndonesia = [
                        'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
                        'April' => 'April', 'Mei' => 'May', 'Juni' => 'June',
                        'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September',
                        'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December'
                    ];
                    
                    // Replace nama bulan Indonesia dengan Inggris
                    foreach ($bulanIndonesia as $indo => $eng) {
                        $dateString = str_replace($indo, $eng, $dateString);
                    }
                    
                    $tanggal = Carbon::parse($dateString)->toDateString();
                }
            } catch (\Exception $e) {
                $tanggal = now()->toDateString();
            }
        }

        // Ambil nama pemohon, bisa berisi nama dan nomor telepon
        $namaPemohon = $row['nama_pemohon'] ?? '';
        
        // Ekstrak nomor telepon jika ada (format: nama\n08xxx atau nama 08xxx)
        $noHp = '';
        
        // Coba berbagai variasi line break
        $lineBreaks = ["\r\n", "\n", "\r", chr(10), chr(13)];
        $foundBreak = false;
        
        foreach ($lineBreaks as $break) {
            if (strpos($namaPemohon, $break) !== false) {
                $parts = explode($break, $namaPemohon);
                $namaPemohon = trim($parts[0]); // Nama di baris pertama
                
                // Cari nomor HP di baris berikutnya
                for ($i = 1; $i < count($parts); $i++) {
                    $line = trim($parts[$i]);
                    if (!empty($line)) {
                        // Hilangkan semua karakter non-digit kecuali digit
                        $digitsOnly = preg_replace('/[^0-9]/', '', $line);
                        
                        // Cek apakah dimulai dengan 0 dan panjang 10-14 digit
                        if (preg_match('/^(0\d{9,13})$/', $digitsOnly)) {
                            $noHp = $digitsOnly;
                            $foundBreak = true;
                            break;
                        }
                    }
                }
                if ($foundBreak) break;
            }
        }
        
        // Jika tidak ada line break, cek di baris yang sama
        if (!$foundBreak) {
            // Cari nomor HP dengan spasi
            if (preg_match('/\b(0[\s\d]{10,})\b/', $namaPemohon, $matches)) {
                $noHp = preg_replace('/\s+/', '', $matches[1]); // Hilangkan spasi
                // Bersihkan nama dari nomor telepon
                $namaPemohon = trim(preg_replace('/\b0[\s\d]{10,}\b/', '', $namaPemohon));
            }
        }
        
        // Bersihkan spasi berlebih di nama
        $namaPemohon = trim($namaPemohon);

        // Generate id_rule jika tidak ada di Excel
        $id_rule = isset($row['id_rule']) && !empty($row['id_rule']) 
            ? $row['id_rule'] 
            : $this->generateIdRule($tanggal, $namaPemohon, $noHp);

        // Cek duplikasi berdasarkan kombinasi unik: tanggal + nama_pemohon + perihal
        $existing = Konsultasi::withTrashed()
            ->where('tanggal', $tanggal)
            ->where('nama_pemohon', $namaPemohon)
            ->where('perihal', $row['perihal'] ?? '')
            ->first();

        // Tentukan jenis berdasarkan data di Excel atau default
        $jenis = 'Konsultasi'; // Default
        if (isset($row['jenis']) && !empty($row['jenis'])) {
            $jenisInput = trim(strtolower($row['jenis']));
            if (in_array($jenisInput, ['konsultasi', 'informasi'])) {
                $jenis = ucfirst($jenisInput);
            }
        }

        // Data yang akan disimpan/diupdate
        $data = [
            'id_rule' => $id_rule,
            'tanggal' => $tanggal,
            'nama_pemohon' => $namaPemohon,
            'no_hp' => $noHp ?: '',
            'nama_perusahaan' => null,
            'email' => null,
            'alamat' => null,
            'perihal' => $row['perihal'] ?? null,
            'keterangan' => $row['keterangan'] ?? null,
            'jenis' => $jenis,
            'jenis_konsultasi' => null,
        ];

        if ($existing) {
            // Update data yang sudah ada
            $existing->update($data);
            
            // Restore jika sudah dihapus (soft delete)
            if ($existing->trashed()) {
                $existing->restore();
            }
            
            $this->updatedCount++;
            return null; // Tidak perlu create baru
        }

        // Create data baru
        $this->importedCount++;
        return new Konsultasi($data);
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errorCount++;
            $this->failedRows[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }

    public function onError(\Throwable $e)
    {
        $this->errorCount++;
        $this->failedRows[] = [
            'message' => $e->getMessage(),
        ];
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }

    public function getErrorCount()
    {
        return $this->errorCount;
    }

    public function getErrors()
    {
        return $this->failedRows;
    }

    public function rules(): array
    {
        return [
            'nama_pemohon' => 'required|string',
            'perihal' => 'required|string',
            'tanggal' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_pemohon.required' => 'Nama pemohon wajib diisi',
            'perihal.required' => 'Perihal wajib diisi',
        ];
    }
}
