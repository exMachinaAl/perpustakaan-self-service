<?php

namespace App\Services;

use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

final class KioskStationService
{
    public function __construct(
        private readonly KioskQrResolver $resolver,
        private readonly MemberIdentity $members,
        private readonly SelfServiceCheckout $checkout,
    ) {}

    private function reply(string $state, $row, string $message): array
    {
        $member = null;
        $book = null;
        if ($row && $row->member_id) {
            $user = DB::table('users')->where('id', $row->member_id)->first(['nama']);
            $member = $user ? strtok($user->nama, ' ') : null;
        }
        if ($row && $row->copy_id) {
            $copy = DB::table('library_kiosk_book_copies as c')->join('buku as b', 'b.id_buku', '=', 'c.book_id')
                ->where('c.id', $row->copy_id)->first(['b.judul', 'c.label']);
            $book = $copy ? $copy->judul.' — '.$copy->label : null;
        }

        return [
            'state' => $state,
            'member' => $member,
            'book' => $book,
            'message' => $message,
            'needs_pin' => $state === 'waiting_pin',
            'can_checkout' => $state === 'ready_checkout',
            'loan_created' => $state === 'borrowed',
            'loan_id' => $row->checkout_loan_id ?? null,
            'due_date' => $row->checkout_due_date ?? null,
        ];
    }

    private function fresh(int $deviceId): object
    {
        DB::table('library_kiosk_device_sessions')->updateOrInsert(['device_id' => $deviceId], [
            'member_id' => null,
            'member_authenticated_at' => null,
            'copy_id' => null,
            'state' => 'ready',
            'validated_event_id' => null,
            'checkout_loan_id' => null,
            'checkout_due_date' => null,
            'expires_at' => now()->addSeconds((int) config('library.kiosk_session_seconds', 90)),
            'updated_at' => now(),
        ]);

        return DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)->lockForUpdate()->first();
    }

    public function reset(int $deviceId): array
    {
        return DB::transaction(function () use ($deviceId) {
            DB::table('library_kiosk_devices')->where('id', $deviceId)->lockForUpdate()->first();

            return $this->reply('ready', $this->fresh($deviceId), 'Sesi dihapus. Scan kartu anggota atau buku.');
        }, 3);
    }

    public function status(int $deviceId): array
    {
        $session = DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)->first();
        if (! $session || ($session->expires_at && Carbon::parse($session->expires_at)->isPast())) {
            return $this->reply('ready', null, 'Menunggu kartu anggota atau buku.');
        }
        if ($session->state === 'borrowed') {
            return $this->reply('borrowed', $session, 'PEMINJAMAN BERHASIL. Batas kembali: '.($session->checkout_due_date ?: '-').'.');
        }

        return $this->reply($session->state, $session, match ($session->state) {
            'waiting_pin' => 'Masukkan PIN kiosk anggota.',
            'waiting_book' => 'Anggota terverifikasi. Scan QR atau barcode eksemplar.',
            'waiting_member' => 'Buku dikenali. Scan kartu anggota.',
            'ready_checkout' => 'Data lengkap. Periksa buku lalu tekan PINJAM.',
            default => 'Menunggu kartu anggota atau buku.',
        });
    }

    public function scan(int $deviceId, string $payload, string $channel): array
    {
        return DB::transaction(function () use ($deviceId, $payload) {
            $device = DB::table('library_kiosk_devices')->where('id', $deviceId)->lockForUpdate()->first();
            if (! $device || ! $device->active) {
                return $this->reply('rejected', null, 'Perangkat dinonaktifkan.');
            }

            $session = DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)->lockForUpdate()->first();
            if (! $session || ($session->expires_at && Carbon::parse($session->expires_at)->isPast())) {
                $session = $this->fresh($deviceId);
            }
            if ($session->state === 'borrowed') {
                return $this->reply('borrowed', $session, 'Peminjaman sudah tercatat. Reset untuk transaksi baru.');
            }

            $resolved = $this->resolver->resolve($payload, true);
            $eventId = DB::table('library_kiosk_device_events')->insertGetId([
                'device_id' => $deviceId,
                'kind' => $resolved['kind'] ?? 'unknown',
                'outcome' => $resolved['outcome'] ?? 'invalid',
                'member_id' => $resolved['member_id'] ?? null,
                'copy_id' => $resolved['copy_id'] ?? null,
                'created_at' => now(),
            ]);
            if (($resolved['outcome'] ?? null) !== 'identified' || ! in_array($resolved['kind'] ?? null, ['member', 'book'], true)) {
                $message = ($resolved['outcome'] ?? null) === 'isbn_requires_copy'
                    ? 'ISBN dikenali, tetapi bukan identitas buku fisik. Scan label eksemplar unik.'
                    : 'Kode tidak valid, tidak aktif, sudah dipakai, atau tidak didukung.';

                return $this->reply('rejected', $session, $message);
            }

            $memberId = $session->member_id;
            $copyId = $session->copy_id;
            $authenticatedAt = $session->member_authenticated_at;
            if ($resolved['kind'] === 'member') {
                if ($memberId && (int) $memberId !== (int) $resolved['member_id']) {
                    return $this->reply('rejected', $session, 'Kartu anggota berbeda. Reset sesi.');
                }
                $memberId = (int) $resolved['member_id'];
                if ($resolved['authenticated'] ?? false) {
                    $authenticatedAt = now();
                }
            } else {
                if ($copyId && (int) $copyId !== (int) $resolved['copy_id']) {
                    return $this->reply('rejected', $session, 'Buku berbeda. Reset sesi.');
                }
                $copyId = (int) $resolved['copy_id'];
            }

            if ($memberId && ! $authenticatedAt) {
                $state = 'waiting_pin';
            } elseif ($memberId && $copyId) {
                $state = 'ready_checkout';
            } elseif ($memberId) {
                $state = 'waiting_book';
            } else {
                $state = 'waiting_member';
            }

            DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)->update([
                'member_id' => $memberId,
                'member_authenticated_at' => $authenticatedAt,
                'copy_id' => $copyId,
                'state' => $state,
                'validated_event_id' => $state === 'ready_checkout' ? $eventId : null,
                'checkout_loan_id' => null,
                'checkout_due_date' => null,
                'expires_at' => now()->addSeconds((int) config('library.kiosk_session_seconds', 90)),
                'updated_at' => now(),
            ]);
            $session = DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)->first();

            return $this->reply($state, $session, match ($state) {
                'waiting_pin' => 'Kartu anggota dikenali. Masukkan PIN kiosk.',
                'waiting_member' => 'Buku dikenali. Scan kartu anggota.',
                'waiting_book' => 'Anggota terverifikasi. Scan QR atau barcode eksemplar.',
                default => 'Data lengkap. Periksa buku lalu tekan PINJAM.',
            });
        }, 3);
    }

    public function authenticate(int $deviceId, string $pin): array
    {
        return DB::transaction(function () use ($deviceId, $pin) {
            $session = DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)->lockForUpdate()->first();
            if (! $session || ! $session->member_id || ! $session->expires_at || Carbon::parse($session->expires_at)->isPast()) {
                throw new DomainException('Sesi anggota tidak tersedia atau kedaluwarsa.');
            }
            if (! $this->members->verifyPin((int) $session->member_id, $pin)) {
                throw new DomainException('PIN anggota salah.');
            }

            $state = $session->copy_id ? 'ready_checkout' : 'waiting_book';
            DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)->update([
                'member_authenticated_at' => now(),
                'state' => $state,
                'expires_at' => now()->addSeconds((int) config('library.kiosk_session_seconds', 90)),
                'updated_at' => now(),
            ]);
            $session = DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)->first();

            return $this->reply($state, $session, $state === 'ready_checkout'
                ? 'PIN benar. Periksa buku lalu tekan PINJAM.'
                : 'PIN benar. Scan QR atau barcode eksemplar.');
        }, 3);
    }

    public function checkout(int $deviceId, string $requestId): array
    {
        $result = $this->checkout->checkout($deviceId, $requestId);
        $session = DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)->first();

        return $this->reply('borrowed', $session, 'PEMINJAMAN BERHASIL. Batas kembali: '.$result['due_date'].'.');
    }
}
