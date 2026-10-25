<?php

namespace App\Console\Commands;

use App\Models\ReaderDevice;
use Illuminate\Console\Command;

class ManageReaderDevice extends Command
{
    protected $signature = 'perpus:reader-manage {serial : ID perangkat} {action : disable|enable|rotate}';
    protected $description = 'Nonaktifkan, aktifkan, atau rotasi secret reader RFID';

    public function handle(): int
    {
        $reader = ReaderDevice::where('serial', $this->argument('serial'))->first();
        if (!$reader) {
            $this->error('Reader tidak ditemukan.');
            return self::FAILURE;
        }
        $action = $this->argument('action');
        if ($action === 'disable') {
            $reader->update(['active' => false]);
            $this->info('Reader dinonaktifkan.');
        } elseif ($action === 'enable') {
            $reader->update(['active' => true]);
            $this->info('Reader diaktifkan. Pastikan kredensial perangkat aman.');
        } elseif ($action === 'rotate') {
            if (!$this->confirm('Secret lama tidak akan berlaku lagi. Lanjutkan rotasi?', false)) {
                return self::FAILURE;
            }
            $secret = bin2hex(random_bytes(32));
            $reader->update(['secret' => $secret, 'active' => false]);
            $this->line('READER_ID=' . $reader->serial);
            $this->line('NEW_READER_SECRET=' . $secret);
            $this->warn('Secret hanya ditampilkan sekali. Pasang pada firmware, lalu aktifkan kembali perangkat.');
        } else {
            $this->error('Action harus disable, enable, atau rotate.');
            return self::FAILURE;
        }
        return self::SUCCESS;
    }
}
