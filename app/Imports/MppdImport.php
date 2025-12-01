<?php

namespace App\Imports;

use App\Models\Mppd;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MppdImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use SkipsFailures; // provides $this->failures()
    protected int $inserted = 0;
    protected int $updated = 0;
    protected array $aliases = [];
    protected array $usedAliases = [];

    public function __construct()
    {
        $this->aliases = config('import_aliases.mppd', []);
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $row = $this->normalizeRow($row);
        $nomorRegister = $row['nomor_register'] ?? null;
        if (!$nomorRegister) {
            return null;
        }
        $existing = Mppd::where('nomor_register', $nomorRegister)->first();
        $tanggalSip = $this->convertExcelDate($row['tanggal_sip'] ?? null);
        $tanggalAkhirSip = $this->convertExcelDate($row['tanggal_akhir_sip'] ?? null);
        if ($existing) {
            $existing->update([
                'nik' => $row['nik'] ?? null,
                'nama' => $row['nama'] ?? null,
                'alamat' => $row['alamat'] ?? null,
                'email' => $row['email'] ?? null,
                'nomor_telp' => $row['nomor_telp'] ?? null,
                'nomor_str' => $row['nomor_str'] ?? null,
                'masa_berlaku_str' => $row['masa_berlaku_str'] ?? null,
                'profesi' => $row['profesi'] ?? null,
                'tempat_praktik' => $row['tempat_praktik'] ?? null,
                'alamat_tempat_praktik' => $row['alamat_tempat_praktik'] ?? null,
                'nomor_sip' => $row['nomor_sip'] ?? null,
                'tanggal_sip' => $tanggalSip,
                'tanggal_akhir_sip' => $tanggalAkhirSip,
                'keterangan' => $row['keterangan'] ?? null,
            ]);
            $this->updated++;
            return null;
        }
        $this->inserted++;
        return new Mppd([
            'nik' => $row['nik'] ?? null,
            'nama' => $row['nama'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'email' => $row['email'] ?? null,
            'nomor_telp' => $row['nomor_telp'] ?? null,
            'nomor_str' => $row['nomor_str'] ?? null,
            'masa_berlaku_str' => $row['masa_berlaku_str'] ?? null,
            'nomor_register' => $nomorRegister,
            'profesi' => $row['profesi'] ?? null,
            'tempat_praktik' => $row['tempat_praktik'] ?? null,
            'alamat_tempat_praktik' => $row['alamat_tempat_praktik'] ?? null,
            'nomor_sip' => $row['nomor_sip'] ?? null,
            'tanggal_sip' => $tanggalSip,
            'tanggal_akhir_sip' => $tanggalAkhirSip,
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }
    public function rules(): array
    {
        return [
            'nomor_register' => ['required'],
            'nik' => ['nullable','string'],
            'nama' => ['nullable','string'],
        ];
    }
    public function prepareForValidation($row, $index)
    {
        return $this->normalizeRow($row);
    }
    public function batchSize(): int { return 500; }
    public function chunkSize(): int { return 500; }
    protected function convertExcelDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            // Already a numeric Excel serial
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value))->toDateString();
            }

            // Already a DateTimeInterface
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->toDateString();
            }

            // Normalize string
            $str = trim((string) $value);
            if ($str === '') {
                return null;
            }

            // First try Carbon's parser for common formats (ISO, English months, etc.)
            try {
                return Carbon::parse($str)->toDateString();
            } catch (\Throwable $e) {
                // fall through to Indonesian month handling
            }

            // Handle Indonesian month names like "02 Oktober 2025" (with optional leading zeros)
            $bulan = [
                'januari' => 1, 'jan' => 1,
                'pebruari' => 2, 'peb' => 2,
                'februari' => 2, 'feb' => 2,
                'maret' => 3, 'mar' => 3,
                'april' => 4, 'apr' => 4,
                'mei' => 5,
                'juni' => 6, 'jun' => 6,
                'juli' => 7, 'jul' => 7,
                'agustus' => 8, 'agu' => 8, 'ags' => 8,
                'september' => 9, 'sep' => 9, 'sept' => 9,
                'oktober' => 10, 'okt' => 10,
                'november' => 11, 'nov' => 11,
                'desember' => 12, 'des' => 12,
            ];

            // Pattern: d{1,2} <bulan> yyyy (case-insensitive)
            if (preg_match('/^(\d{1,2})\s+([\p{L}\.]+)\s+(\d{2,4})$/ui', $str, $m)) {
                $d = (int) $m[1];
                $monthKey = mb_strtolower(rtrim($m[2], '.'));
                $y = (int) $m[3];
                if ($y < 100) { // normalize 2-digit years if encountered
                    $y += ($y >= 70 ? 1900 : 2000);
                }
                if (isset($bulan[$monthKey])) {
                    $mm = (int) $bulan[$monthKey];
                    return sprintf('%04d-%02d-%02d', $y, $mm, $d);
                }
            }

            // Pattern: dd-mm-yyyy or dd/mm/yyyy
            if (preg_match('/^(\d{1,2})[\-\/.](\d{1,2})[\-\/.](\d{2,4})$/', $str, $m)) {
                $d = (int) $m[1];
                $mm = (int) $m[2];
                $y = (int) $m[3];
                if ($y < 100) { $y += ($y >= 70 ? 1900 : 2000); }
                if ($mm >= 1 && $mm <= 12 && $d >= 1 && $d <= 31) {
                    return sprintf('%04d-%02d-%02d', $y, $mm, $d);
                }
            }

            // If all parsing strategies fail, return null
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }
    public function getInsertedCount(): int { return $this->inserted; }
    public function getUpdatedCount(): int { return $this->updated; }
    public function getRowCount(): int { return $this->inserted + $this->updated; }
    public function getUsedAliases(): array { return $this->usedAliases; }
    private function normalizeHeaderKey(string $key): string
    {
        $k = mb_strtolower(trim($key));
        $k = str_replace(['.', '-', '_'], ' ', $k);
        $k = preg_replace('/\s+/', ' ', $k);
        return $k;
    }
    private function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $original => $value) {
            $keyNorm = $this->normalizeHeaderKey($original);
            $canonical = $this->aliases[$keyNorm] ?? null;
            if (!$canonical) {
                $underscore = str_replace(' ', '_', $keyNorm);
                if (isset($this->aliases[$underscore])) {
                    $canonical = $this->aliases[$underscore];
                }
            }
            if (!$canonical) {
                // If heading row already slugged and matches canonical directly
                if (isset($this->aliases[$original])) {
                    $canonical = $this->aliases[$original];
                }
            }
            if (!$canonical) {
                // Fallback: use slugged variant
                $canonical = str_replace(' ', '_', $keyNorm);
            }
            if ($canonical !== $original && !isset($this->usedAliases[$original])) {
                $this->usedAliases[$original] = $canonical;
            }
            // Strip leading apostrophes for specific text fields
            if (in_array($canonical, ['nik','nomor_telp','nomor_str','nomor_register'])) {
                if (is_string($value)) {
                    $value = ltrim($value, "' ");
                }
            }
            $normalized[$canonical] = $value;
        }
        return $normalized;
    }
    
}
