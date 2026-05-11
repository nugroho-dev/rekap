<?php

namespace App\Imports;

use App\Models\Pengawasan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PengawasanImport implements ToModel, WithHeadingRow
{
    private ?array $canonicalMap = null;

    private int $createdCount = 0;
    private int $updatedCount = 0;
    private int $skippedEmptyKodeCount = 0;
    private int $skippedUnknownKodeCount = 0;

    /** @var array<int, string> */
    private array $unknownKodeExamples = [];

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $kodeRaw = $this->readColumn($row, ['nomor_kode_proyek', 'nomor kode proyek', 'id_proyek', 'id proyek']);
        $kodeProyek = $this->resolveKodeProyek($kodeRaw);

        if ($kodeProyek === null) {
            return null;
        }

        $data = [
            'nomor_kode_proyek' => $kodeProyek,
            'kesesuaian' => $this->readColumn($row, ['kesesuaian']),
            'pembinaan' => $this->readColumn($row, ['pembinaan']),
            'perbaikan' => $this->readColumn($row, ['perbaikan']),
            'sanksi' => $this->readColumn($row, ['sanksi']),
            'hasil_pengawasan' => $this->readColumn($row, ['hasil_pengawasan', 'hasil pengawasan']),
            'persyaratan_dasar' => $this->readColumn($row, ['persyaratan_dasar', 'persyaratan dasar']),
            'pemenuhan_pb' => $this->readColumn($row, ['pemenuhan_pb', 'pemenuhan pb']),
            'csr' => $this->readColumn($row, ['csr']),
            'lkpm' => $this->readColumn($row, ['lkpm']),
            'permasalahan' => $this->readColumn($row, ['permasalahan']),
            'rekomendasi' => $this->readColumn($row, ['rekomendasi']),
        ];

        $this->createdCount++;
        return new Pengawasan($data);
    }

    public function getCreatedCount(): int
    {
        return $this->createdCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getSkippedEmptyKodeCount(): int
    {
        return $this->skippedEmptyKodeCount;
    }

    public function getSkippedUnknownKodeCount(): int
    {
        return $this->skippedUnknownKodeCount;
    }

    /** @return array<int, string> */
    public function getUnknownKodeExamples(): array
    {
        return $this->unknownKodeExamples;
    }

    private function resolveKodeProyek(?string $value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            $this->skippedEmptyKodeCount++;
            return null;
        }

        $normalized = $this->normalizeKey($raw);
        if ($normalized === '') {
            $this->skippedEmptyKodeCount++;
            return null;
        }

        $map = $this->getCanonicalMap();
        $canonical = $map[$normalized] ?? null;
        if ($canonical === null) {
            $this->skippedUnknownKodeCount++;
            if (count($this->unknownKodeExamples) < 5) {
                $this->unknownKodeExamples[] = $raw;
            }
            return null;
        }

        return $canonical;
    }

    /** @return array<string, string> */
    private function getCanonicalMap(): array
    {
        if ($this->canonicalMap !== null) {
            return $this->canonicalMap;
        }

        $map = [];
        $idProyekList = DB::table('proyek')
            ->whereNotNull('id_proyek')
            ->pluck('id_proyek');

        foreach ($idProyekList as $idProyek) {
            $idProyek = trim((string) $idProyek);
            if ($idProyek === '') {
                continue;
            }

            $normalized = $this->normalizeKey($idProyek);
            if ($normalized === '') {
                continue;
            }

            if (!isset($map[$normalized])) {
                $map[$normalized] = $idProyek;
            }

            // Dukung sumber lama yang menghilangkan prefix R-.
            if (str_starts_with($normalized, 'R')) {
                $withoutR = substr($normalized, 1);
                if ($withoutR !== '' && !isset($map[$withoutR])) {
                    $map[$withoutR] = $idProyek;
                }
            }
        }

        $this->canonicalMap = $map;

        return $this->canonicalMap;
    }

    private function normalizeKey(string $value): string
    {
        $upper = strtoupper(trim($value));
        return preg_replace('/[^A-Z0-9]/', '', $upper) ?? '';
    }

    /** @param array<int, string> $keys */
    private function readColumn(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $row)) {
                continue;
            }

            $value = trim((string) $row[$key]);
            return $value === '' ? null : $value;
        }

        return null;
    }

}
