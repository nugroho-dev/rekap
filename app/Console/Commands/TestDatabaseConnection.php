<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
  use Illuminate\Support\Facades\DB;

class TestDatabaseConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the database connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         try {
                DB::connection()->getPdo();
                $databaseName = DB::connection()->getDatabaseName();
                $this->info("Berhasil terhubung ke database: {$databaseName}");
            } catch (\Exception $e) {
                $this->error("Gagal terhubung ke database. Error: " . $e->getMessage());
            }
    }
}
