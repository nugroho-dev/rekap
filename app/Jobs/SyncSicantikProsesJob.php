<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SyncSicantikProsesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $dateStart;
    public string $dateEnd;
    public ?int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $dateStart, string $dateEnd, ?int $userId = null)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->userId = $userId;
        $this->queue = config('queue.connections.database.queue', 'default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $base = config('services.sicantik.url', 'https://sicantik.go.id');
        $endpoint = '/api/TemplateData/keluaran/42611.json';

        try {
            $response = Http::timeout(20)->retry(3, 500)->get($base . $endpoint, [
                'date1' => $this->dateStart,
                'date2' => $this->dateEnd,
            ]);
        } catch (\Throwable $e) {
            Log::error('SyncSicantikProsesJob: request failed', ['error' => $e->getMessage(), 'date1' => $this->dateStart, 'date2' => $this->dateEnd]);
            return;
        }

        if (!$response->ok()) {
            Log::error('SyncSicantikProsesJob: non-ok response', ['status' => $response->status(), 'body' => $response->body()]);
            return;
        }

        $payload = $response->json();
        $items = data_get($payload, 'data.data', []);
        if (!is_array($items) || empty($items)) {
            Log::info('SyncSicantikProsesJob: no items found', ['date1' => $this->dateStart, 'date2' => $this->dateEnd]);
            return;
        }

        $totalItems = count($items);
        Log::info('SyncSicantikProsesJob: received items', ['date1' => $this->dateStart, 'date2' => $this->dateEnd, 'items' => $totalItems]);

        $warnThreshold = config('sicantik.warn_items_threshold', 50000);
        if ($totalItems > $warnThreshold) {
            Log::warning('SyncSicantikProsesJob: large payload received — auto-splitting into smaller jobs', ['items' => $totalItems, 'threshold' => $warnThreshold, 'date1' => $this->dateStart, 'date2' => $this->dateEnd]);

            // Auto-split into smaller sub-jobs using 'auto_split_days'
            $autoSplitDays = (int) config('sicantik.auto_split_days', 1);
            try {
                $start = \Carbon\Carbon::createFromFormat('Y-m-d', $this->dateStart);
                $end = \Carbon\Carbon::createFromFormat('Y-m-d', $this->dateEnd);
            } catch (\Throwable $e) {
                Log::error('SyncSicantikProsesJob: invalid date format for auto-split', ['error' => $e->getMessage(), 'date1' => $this->dateStart, 'date2' => $this->dateEnd]);
                return;
            }

            $dispatched = 0;
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $chunkStart = $cursor->copy();
                $chunkEnd = $cursor->copy()->addDays($autoSplitDays - 1);
                if ($chunkEnd->gt($end)) $chunkEnd = $end->copy();
                try {
                    \App\Jobs\SyncSicantikProsesJob::dispatch($chunkStart->toDateString(), $chunkEnd->toDateString(), $this->userId);
                    $dispatched++;
                } catch (\Throwable $e) {
                    Log::error('SyncSicantikProsesJob: failed to dispatch auto-split subjob', ['error' => $e->getMessage(), 'start' => $chunkStart->toDateString(), 'end' => $chunkEnd->toDateString()]);
                }
                $cursor->addDays($autoSplitDays);
            }

            Log::info('SyncSicantikProsesJob: auto-split completed', ['subjobs' => $dispatched, 'original_items' => $totalItems, 'date1' => $this->dateStart, 'date2' => $this->dateEnd]);

            // Exit current job; sub-jobs will handle processing
            return;
        }

        // Allowed columns to insert/upsert - precomputed for speed
        $allowed = [
            'id_proses_permohonan','alamat','data_status','default_active','del','dibuat_oleh','diproses_oleh','diubah_oleh',
            'email','end_date','file_signed_report','instansi_id','jenis_izin','jenis_izin_id',
            'jenis_kelamin','jenis_permohonan','jenis_proses_id','lokasi_izin','nama','nama_proses',
            'no_hp','no_izin','no_permohonan','no_rekomendasi','no_tlp','permohonan_izin_id',
            'start_date','status','tgl_dibuat','tgl_diubah','tgl_lahir','tgl_penetapan','tgl_pengajuan',
            'tgl_pengajuan_time','tgl_rekomendasi','tgl_selesai','tgl_selesai_time','tgl_signed_report'
        ];

        $batchSize = config('sicantik.sync_batch_size', 500);
        $processed = 0;

        foreach (array_chunk($items, $batchSize) as $chunkIndex => $chunk) {
            $toUpsert = [];
            foreach ($chunk as $row) {
                if (!is_array($row)) continue;
                $clean = array_intersect_key($row, array_flip($allowed));
                // Normalize/trim simple string fields to reduce payload size
                if (isset($clean['nama'])) $clean['nama'] = is_string($clean['nama']) ? trim($clean['nama']) : $clean['nama'];
                if (isset($clean['no_permohonan'])) $clean['no_permohonan'] = is_string($clean['no_permohonan']) ? trim($clean['no_permohonan']) : $clean['no_permohonan'];
                $toUpsert[] = $clean;
            }

            if (empty($toUpsert)) {
                unset($toUpsert);
                continue;
            }

            try {
                DB::beginTransaction();
                // upsert on id_proses_permohonan
                $uniqueBy = ['id_proses_permohonan'];
                $updateCols = array_values(array_diff(array_keys($toUpsert[0]), $uniqueBy));
                DB::table('proses')->upsert($toUpsert, $uniqueBy, $updateCols);
                DB::commit();
                $processed += count($toUpsert);
                Log::info('SyncSicantikProsesJob: upsert chunk completed', ['chunk' => $chunkIndex, 'rows' => count($toUpsert)]);
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('SyncSicantikProsesJob: upsert failed', ['error' => $e->getMessage(), 'chunk' => $chunkIndex]);
            }

            // Free memory from the processed chunk and run GC
            unset($toUpsert, $chunk);
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }

        Log::info('SyncSicantikProsesJob: completed', ['processed' => $processed, 'date1' => $this->dateStart, 'date2' => $this->dateEnd]);
    }
}
