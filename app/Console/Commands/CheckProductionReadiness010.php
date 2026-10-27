<?php
namespace App\Console\Commands;

use App\Services\ProductionReadiness;
use Illuminate\Console\Command;

final class CheckProductionReadiness010 extends Command
{
    protected $signature = 'perpus:production-check';
    protected $description = 'Pemeriksaan read-only sebelum produksi: Laravel, PHP, konfigurasi, dan database.';

    public function handle(ProductionReadiness $checks): int
    {
        $report = $checks->report();
        $this->info('PERPUSTAKAAN FULL SECURE — PRODUCTION READINESS (READ ONLY)');
        foreach ($report['checks'] as $row) {
            $this->line('[' . $row['status'] . '] ' . $row['title'] . ' — ' . $row['detail']);
        }
        $this->warn($report['message']);
        $this->line('PASS: '.$report['counts']['PASS'].' | BLOCK: '.$report['counts']['BLOCK'].' | MANUAL: '.$report['counts']['MANUAL']);
        // Item MANUAL selalu membutuhkan bukti UAT: tidak boleh menghasilkan status GO-LIVE hijau.
        return (!$report['production_ready'] || $report['counts']['BLOCK']) ? self::FAILURE : self::SUCCESS;
    }
}
