<?php

namespace App\Http\Controllers;

use App\Models\ReaderDevice;
use App\Models\RfidCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReaderScanController extends Controller
{
    public function scan(Request $request)
    {
        $data = $request->validate(['uid' => 'required|string|max:64']);

        try {
            $uid = RfidCard::normalizeUid($data['uid']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => 'Format UID tidak valid'], 422);
        }

        $device = $request->attributes->get('verified_reader');
        $fingerprint = RfidCard::digest($uid);

        // Kunci per reader menserialkan pemeriksaan cooldown pada permintaan paralel.
        $queued = DB::transaction(function () use ($device, $fingerprint, $uid) {
            $reader = ReaderDevice::whereKey($device->id)->lockForUpdate()->firstOrFail();
            if (!$reader->active) {
                abort(401, 'Reader sudah dinonaktifkan.');
            }
            // Scan berhasil diverifikasi perangkat; update status online juga.
            DB::table('reader_devices')->where('id', $reader->id)->update([
                'last_seen_at' => now(),
                'last_reader_ok' => true,
                'updated_at' => now(),
            ]);

            $duplicate = DB::table('rfid_scan_events')
                ->where('reader_device_id', $reader->id)
                ->where('uid_fingerprint', $fingerprint)
                ->where('created_at', '>=', now()->subSeconds(8))
                ->exists();
            if ($duplicate) {
                return false;
            }

            $card = RfidCard::with('user')
                ->where('uid_hash', $fingerprint)
                ->where('status', 'aktif')
                ->first();
            $identified = $card && $card->user
                && $card->user->status === 'aktif'
                && $card->user->role === 'user';

            DB::table('rfid_scan_events')->insert([
                'reader_device_id' => $reader->id,
                'user_id' => $identified ? $card->user_id : null,
                'uid_last4' => substr($uid, -4),
                // Digest dikunci dengan APP_KEY; UID asli tidak disimpan.
                'uid_fingerprint' => $fingerprint,
                'outcome' => $identified ? 'identified' : 'unknown_or_inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return true;
        }, 3);

        // Jangan kirim data pribadi siswa melalui response ESP32.
        // Semua tindakan peminjaman hanya bisa diselesaikan oleh petugas yang login.
        return response()->json(['accepted' => true, 'queued' => $queued], 202)
            ->header('Cache-Control', 'no-store');
    }
}
