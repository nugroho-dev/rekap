<?php

namespace App\Console\Commands;

use App\Models\ApiAuditLog;
use Illuminate\Console\Command;

class PruneApiAuditLogs extends Command
{
    protected $signature = 'api-audit:prune {--days=90 : Hapus log yang lebih lama dari jumlah hari ini}';

    protected $description = 'Menghapus log audit API yang sudah lama';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days);

        $deleted = ApiAuditLog::query()
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Berhasil menghapus {$deleted} log audit API yang lebih lama dari {$days} hari.");

        return self::SUCCESS;
    }
}