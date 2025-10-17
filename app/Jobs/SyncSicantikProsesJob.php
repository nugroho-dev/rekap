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
use App\Models\Proses;
use Illuminate\Database\Eloquent\Model as EloquentModel;

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

                // If incoming row lacks id_proses_permohonan, try to map to an existing record via no_permohonan
                $hasId = !empty($clean['id_proses_permohonan']);
                $hasNo = !empty($clean['no_permohonan']);

                if (!$hasId && $hasNo) {
                    try {
                        $existing = DB::table('proses')->where('no_permohonan', trim($clean['no_permohonan']))->select('id_proses_permohonan')->first();
                        if ($existing && !empty($existing->id_proses_permohonan)) {
                            $clean['id_proses_permohonan'] = $existing->id_proses_permohonan;
                            Log::info('SyncSicantikProsesJob: mapped incoming row to existing id via no_permohonan', ['no_permohonan' => $clean['no_permohonan'], 'mapped_id' => $existing->id_proses_permohonan]);
                        }
                    } catch (\Throwable $e) {
                        // on error, skip mapping and proceed — we'll either insert or skip below
                    }
                }

                // Normalize/trim simple string fields to reduce payload size
                if (isset($clean['nama'])) $clean['nama'] = is_string($clean['nama']) ? trim($clean['nama']) : $clean['nama'];
                if (isset($clean['no_permohonan'])) $clean['no_permohonan'] = is_string($clean['no_permohonan']) ? trim($clean['no_permohonan']) : $clean['no_permohonan'];

                // If the row has neither id_proses_permohonan nor no_permohonan, skip it to avoid creating duplicate/anonymous rows
                if (empty($clean['id_proses_permohonan']) && empty($clean['no_permohonan'])) {
                    Log::warning('SyncSicantikProsesJob: skipping row without identifier', ['sample' => array_slice($row, 0, 5)]);
                    continue;
                }

                $toUpsert[] = $clean;
            }

            if (empty($toUpsert)) {
                unset($toUpsert);
                continue;
            }

            try {
                DB::beginTransaction();
                // Use Eloquent updateOrCreate per row so model events, mutators and casts run.
                // Temporarily disable mass-assignment protection for bulk writes.
                EloquentModel::unguard();

                foreach ($toUpsert as $uRow) {
                    // Determine lookup keys: prefer id_proses_permohonan, fallback to no_permohonan
                    $lookup = [];
                    if (!empty($uRow['id_proses_permohonan'])) {
                        $lookup = ['id_proses_permohonan' => $uRow['id_proses_permohonan']];
                    } elseif (!empty($uRow['no_permohonan'])) {
                        $lookup = ['no_permohonan' => $uRow['no_permohonan']];
                    } else {
                        // shouldn't happen due to earlier guard, but skip defensively
                        continue;
                    }

                    try {
                        Proses::updateOrCreate($lookup, $uRow);
                    } catch (\Throwable $e) {
                        // Log individual row failures but keep processing the chunk
                        Log::error('SyncSicantikProsesJob: updateOrCreate failed for row', ['error' => $e->getMessage(), 'lookup' => $lookup]);
                    }
                }

                EloquentModel::reguard();
                DB::commit();
                $processed += count($toUpsert);
                Log::info('SyncSicantikProsesJob: upsert(chunk->updateOrCreate) completed', ['chunk' => $chunkIndex, 'rows' => count($toUpsert)]);
            } catch (\Throwable $e) {
                DB::rollBack();
                // Ensure reguard even on failure
                try {
                    EloquentModel::reguard();
                } catch (\Throwable $_) {
                }
                Log::error('SyncSicantikProsesJob: upsert/updateOrCreate failed', ['error' => $e->getMessage(), 'chunk' => $chunkIndex]);
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
