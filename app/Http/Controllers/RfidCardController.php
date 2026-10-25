<?php

namespace App\Http\Controllers;

use App\Models\RfidCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RfidCardController extends Controller
{
    public function index()
    {
        $cards = RfidCard::with('user')->latest()->paginate(30);
        $pendingScans = DB::table('rfid_scan_events as e')
            ->join('reader_devices as d', 'd.id', '=', 'e.reader_device_id')
            ->where('e.outcome', 'unknown_or_inactive')
            ->whereNull('e.enrolled_at')
            ->where('e.created_at', '>=', now()->subMinutes(2))
            ->select(['e.id', 'e.uid_last4', 'e.created_at', 'd.name as reader_name'])
            ->orderByDesc('e.id')->limit(10)->get();

        return response()->view('admin.rfid.index', compact('cards', 'pendingScans'))
            ->header('Cache-Control', 'private, no-store');
    }

    /** Metode manual disediakan untuk migrasi kartu; scan-to-enroll lebih dianjurkan. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'uid' => 'required|string|max:64',
        ]);
        try {
            $uid = RfidCard::normalizeUid($data['uid']);
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages(['uid' => $e->getMessage()]);
        }

        $this->assignCard((int) $data['user_id'], RfidCard::digest($uid), substr($uid, -4));
        return back()->with('success', 'Kartu berhasil didaftarkan, kartu lama dinonaktifkan.');
    }

    /** Daftar langsung dari scan reader yang terautentikasi; UID asli tidak perlu diketik. */
    public function enroll(Request $request)
    {
        $data = $request->validate([
            'scan_event_id' => 'required|integer|exists:rfid_scan_events,id',
            'user_id' => 'required|integer|exists:users,id',
            'confirmed' => 'accepted',
        ]);

        try {
            DB::transaction(function () use ($data, $request) {
                $event = DB::table('rfid_scan_events')
                    ->where('id', (int) $data['scan_event_id'])
                    ->lockForUpdate()->first();

                if (!$event || $event->outcome !== 'unknown_or_inactive'
                    || $event->enrolled_at !== null || !$event->uid_fingerprint
                    || \Carbon\Carbon::parse($event->created_at)->lessThan(now()->subMinutes(2))) {
                    throw ValidationException::withMessages([
                        'scan_event_id' => 'Scan tidak tersedia, sudah dipakai, atau lebih dari 2 menit. Tap ulang kartu.',
                    ]);
                }

                $this->assignCardLocked((int) $data['user_id'], $event->uid_fingerprint, $event->uid_last4);

                DB::table('rfid_scan_events')->where('id', $event->id)->update([
                    'user_id' => (int) $data['user_id'],
                    'outcome' => 'enrolled',
                    'enrolled_at' => now(),
                    'enrolled_by' => $request->user()->id,
                    'updated_at' => now(),
                ]);
            }, 3);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            throw ValidationException::withMessages(['scan_event_id' => 'Kartu sudah terdaftar.']);
        }

        return redirect()->route('admin.rfid.index')
            ->with('success', 'Kartu telah ditautkan. Petugas wajib memastikan kartu fisik milik siswa.');
    }

    private function assignCard(int $userId, string $fingerprint, string $last4): void
    {
        try {
            DB::transaction(fn () => $this->assignCardLocked($userId, $fingerprint, $last4), 3);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            throw ValidationException::withMessages(['uid' => 'Kartu sudah terdaftar.']);
        }
    }

    private function assignCardLocked(int $userId, string $fingerprint, string $last4): void
    {
        $user = User::whereKey($userId)->lockForUpdate()->first();
        if (!$user || $user->role !== 'user' || $user->status !== 'aktif') {
            throw ValidationException::withMessages(['user_id' => 'Hanya anggota aktif yang dapat memiliki kartu.']);
        }
        if (RfidCard::where('uid_hash', $fingerprint)->exists()) {
            throw ValidationException::withMessages(['uid' => 'Kartu sudah ada, termasuk kartu yang pernah dicabut.']);
        }

        RfidCard::where('user_id', $userId)->where('status', 'aktif')
            ->update(['status' => 'dicabut']);
        RfidCard::create([
            'user_id' => $userId,
            'uid_hash' => $fingerprint,
            'uid_last4' => $last4,
            'status' => 'aktif',
        ]);
    }

    public function revoke(RfidCard $card)
    {
        $card->update(['status' => 'dicabut']);
        return back()->with('success', 'Kartu berhasil dinonaktifkan.');
    }
}
