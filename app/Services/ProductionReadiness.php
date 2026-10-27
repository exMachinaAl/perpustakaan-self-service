<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Pemeriksaan read-only. Tidak mengklaim bahwa tes beban, fisik, atau keamanan telah selesai. */
final class ProductionReadiness
{
    public function report(): array
    {
        $checks = [];
        $add = function (string $status, string $title, string $detail) use (&$checks): void {
            $checks[] = compact('status', 'title', 'detail');
        };
        $framework = app()->version();
        $laravelMajor = (int) explode('.', $framework)[0];
        if ($laravelMajor < 12) {
            $add('BLOCK', 'Framework tidak lagi didukung',
                'Laravel '.$framework.' perlu upgrade bertahap ke versi dengan security support.');
        } else {
            $add('PASS', 'Framework modern', 'Laravel '.$framework.'; cocokkan dengan jadwal dukungan resmi.');
        }
        $php = PHP_VERSION;
        if (PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION === 2 && version_compare($php, '8.2.35', '<')) {
            $add('BLOCK', 'PHP perlu patch keamanan', 'PHP '.$php.' terdeteksi; gunakan sekurangnya 8.2.35 untuk cabang 8.2.');
        } elseif (PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION === 3 && version_compare($php, '8.3.36', '<')) {
            $add('BLOCK', 'PHP perlu patch keamanan', 'PHP '.$php.' terdeteksi; gunakan sekurangnya 8.3.36 untuk cabang 8.3.');
        } elseif (PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION === 4 && version_compare($php, '8.4.27', '<')) {
            $add('BLOCK', 'PHP perlu patch keamanan', 'PHP '.$php.' terdeteksi; gunakan sekurangnya 8.4.27 untuk cabang 8.4.');
        } else {
            $add('PASS', 'PHP CLI', 'PHP '.$php.'; pastikan web server memakai PHP yang sama dan terbarui.');
        }
        if (! app()->environment('production')) {
            $add('BLOCK', 'Lingkungan masih non-production',
                'APP_ENV harus production saat go-live, tetapi pilot awal memang wajib diuji di staging lebih dulu.');
        } else {
            $add('PASS', 'APP_ENV produksi', 'APP_ENV=production; mode pilot seharusnya tidak aktif.');
        }
        if (! config('app.key')) {
            $add('BLOCK', 'APP_KEY belum ada', 'Konfigurasi APP_KEY harus tersedia; jangan regenerasi key aplikasi yang sudah berjalan.');
        } else {
            $add('PASS', 'APP_KEY tersedia', 'Pemeriksa tidak menampilkan key.');
        }
        if (config('app.debug')) {
            $add('BLOCK', 'APP_DEBUG aktif', 'Matikan debug sebelum publikasi.');
        } else {
            $add('PASS', 'APP_DEBUG mati', 'Detail stack trace tidak ditampilkan oleh debug mode.');
        }
        $url = (string) config('app.url');
        if (str_starts_with($url, 'https://')) {
            $add('PASS', 'APP_URL HTTPS', 'Alamat APP_URL menggunakan HTTPS.');
        } else {
            $add('BLOCK', 'APP_URL bukan HTTPS', 'Gunakan domain/IP dengan TLS tepercaya dan reverse proxy.');
        }
        if (config('session.secure')) {
            $add('PASS', 'Cookie secure', 'SESSION_SECURE_COOKIE aktif.');
        } else {
            $add('BLOCK', 'Cookie belum secure', 'Aktifkan SESSION_SECURE_COOKIE dan uji proxy TLS.');
        }
        if (config('database.default') !== 'mysql') {
            $add('BLOCK', 'Database tidak terpilih MySQL', 'Periksa DB_CONNECTION.');
        } else {
            $add('PASS', 'MySQL configured', 'Driver default MySQL.');
        }
        try {
            DB::connection()->getPdo();
            $add('PASS', 'Database terhubung', 'PDO terhubung; tidak menjalankan migrasi.');
            foreach (['library_kiosk_devices', 'library_kiosk_device_sessions', 'library_kiosk_book_copies',
                'library_member_credentials', 'library_kiosk_self_checkouts', 'library_kiosk_pilot_returns'] as $table) {
                $add(Schema::hasTable($table) ? 'PASS' : 'BLOCK', 'Tabel '.$table,
                    Schema::hasTable($table) ? 'Tabel tersedia.' : 'Tabel belum tersedia; periksa migrasi.');
            }
        } catch (\Throwable $e) {
            $add('BLOCK', 'MySQL tidak dapat dibaca', 'Periksa koneksi dan konfigurasi; detail teknis ada di log lokal.');
        }
        $add(! config('kiosk_pilot.enabled') ? 'PASS' : 'BLOCK', 'Mode pilot checkout',
            config('kiosk_pilot.enabled') ? 'Pilot checkout aktif; nonaktifkan sebelum publikasi.' : 'Mode pilot nonaktif.');
        $add('MANUAL', 'Checkout mandiri pada perangkat fisik', 'Buktikan kartu + PIN, QR/barcode eksemplar, pencabutan device, request ulang, dan dua kiosk bersamaan.');
        $add('MANUAL', 'Backup dan restore', 'Uji restore MySQL + unggahan dari backup pada perangkat kedua.');
        $add('MANUAL', 'Uji multi-device dan beban', 'Uji scanner bersamaan, listrik putus, Wi-Fi mati, dan transaksi ganda.');
        $add('MANUAL', 'Pengembalian mandiri', 'Belum ada verifikasi penerimaan fisik otomatis; petugas tetap diperlukan.');
        $counts = ['BLOCK' => 0, 'PASS' => 0, 'MANUAL' => 0];
        foreach ($checks as $item) {
            $counts[$item['status']]++;
        }

        return ['checks' => $checks, 'counts' => $counts, 'production_ready' => false,
            'message' => 'SELF-SERVICE SUDAH DIIMPLEMENTASIKAN, BELUM GO-LIVE: item manual membutuhkan bukti pengujian perangkat dan operasional.'];
    }
}
