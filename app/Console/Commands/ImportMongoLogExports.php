<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Optional import of mongoexport JSON Lines dumps into the new MySQL log tables.
 * Does not connect to MongoDB, nor does it delete anything from the source.
 */
class ImportMongoLogExports extends Command
{
    protected $signature = 'perpus:import-mongo-logs {directory : Folder containing JSONL exports} {--dry-run : Validate without writing}';
    protected $description = 'Import historical mongoexport JSON Lines logs to MySQL (optional)';

    private const MAP = [
        'activity_logs' => ['user_id', 'nama_user', 'role', 'aktivitas', 'keterangan', 'data', 'ip_address', 'user_agent', 'waktu'],
        'analytics_logs' => ['user_id', 'nama_user', 'role', 'event', 'halaman', 'aksi', 'ip_address', 'user_agent', 'data', 'waktu'],
        'search_history' => ['user_id', 'nama_user', 'role', 'keyword', 'kategori', 'sort', 'halaman', 'keterangan', 'data', 'waktu'],
        'notifications' => ['user_id', 'nama_user', 'judul', 'pesan', 'tipe', 'status', 'dibaca', 'data', 'waktu'],
    ];

    public function handle(): int
    {
        $directory = (string) $this->argument('directory');
        if (!is_dir($directory)) {
            $this->error('Folder tidak ditemukan.');
            return self::FAILURE;
        }

        $total = 0;
        foreach (self::MAP as $collection => $fields) {
            $file = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $collection . '.json';
            if (!is_file($file)) {
                $this->warn("Lewati {$collection}: export tidak ditemukan.");
                continue;
            }
            $handle = fopen($file, 'rb');
            if ($handle === false) {
                $this->error("Gagal membuka {$file}");
                return self::FAILURE;
            }
            $count = 0;
            try {
                while (($line = fgets($handle)) !== false) {
                    if (trim($line) === '') continue;
                    $row = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                    if (!is_array($row) || array_is_list($row)) throw new RuntimeException('Format harus JSON Lines dari mongoexport (tanpa --jsonArray).');

                    $data = [];
                    foreach ($fields as $field) {
                        if (!array_key_exists($field, $row)) continue;
                        $value = $row[$field];
                        if ($field === 'data') {
                            $value = json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                        } elseif ($field === 'waktu') {
                            $value = $this->normalizeDate($value);
                        } elseif ($field === 'user_id') {
                            $value = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
                        } elseif ($field === 'dibaca') {
                            $value = (bool) $value;
                        } elseif (is_array($value)) {
                            $value = json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                        }
                        $data[$field] = $value;
                    }
                    $id = $row['_id']['$oid'] ?? $row['_id'] ?? null;
                    if (!is_string($id) || $id === '' || strlen($id) > 255) {
                        throw new RuntimeException('Dokumen MongoDB tidak memiliki _id string yang valid.');
                    }
                    $data['source_mongo_id'] = $id;
                    $data['updated_at'] = now();
                    $data['created_at'] = $this->normalizeDate($row['created_at'] ?? null) ?? now();
                    if (!$this->option('dry-run')) {
                        DB::table($collection)->updateOrInsert(['source_mongo_id' => $id], $data);
                    }
                    $count++;
                }
            } catch (Throwable $e) {
                $this->error("Import {$collection} berhenti di baris " . ($count + 1) . ': ' . $e->getMessage());
                fclose($handle);
                return self::FAILURE;
            }
            fclose($handle);
            $total += $count;
            $this->info("{$collection}: {$count} data " . ($this->option('dry-run') ? 'tervalidasi' : 'diproses (idempotent)'));
        }
        $this->info("Total {$total} dokumen " . ($this->option('dry-run') ? 'diperiksa' : 'diproses') . '.');
        return self::SUCCESS;
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') return null;
        if (is_array($value) && isset($value['$date'])) $value = $value['$date'];
        if (is_array($value) && isset($value['$numberLong'])) $value = $value['$numberLong'];
        if (is_numeric($value)) {
            // mongoexport Extended JSON epoch milliseconds.
            return Carbon::createFromTimestampMs((int) $value, 'UTC')->format('Y-m-d H:i:s');
        }
        return Carbon::parse((string) $value)->utc()->format('Y-m-d H:i:s');
    }
}
