<?php

namespace App\Http\Controllers;

use App\Models\ReaderDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RfidDeskController extends Controller
{
    public function index()
    {
        $readers = ReaderDevice::query()
            ->select(['id', 'serial', 'name', 'active'])
            ->orderBy('name')
            ->get();

        return response()->view('admin.rfid.desk', compact('readers'))
            ->header('Cache-Control', 'private, no-store');
    }

    public function events(Request $request)
    {
        $data = $request->validate([
            'after_id' => 'nullable|integer|min:0|max:2147483647',
            'reader_id' => 'nullable|integer|exists:reader_devices,id',
        ]);

        $after = (int) ($data['after_id'] ?? 0);
        $query = DB::table('rfid_scan_events as e')
            ->leftJoin('reader_devices as d', 'e.reader_device_id', '=', 'd.id')
            ->leftJoin('users as u', 'e.user_id', '=', 'u.id')
            ->where('e.created_at', '>=', now()->subHours(1))
            ->select([
                'e.id', 'e.uid_last4', 'e.user_id', 'e.outcome', 'e.created_at',
                'e.enrolled_at', 'd.name as reader_name', 'd.serial as reader_serial',
                'u.nama as user_name',
            ]);

        if (!empty($data['reader_id'])) {
            $query->where('e.reader_device_id', (int) $data['reader_id']);
        }

        // Tanpa cursor: muat 30 terbaru. Dengan cursor: maksimal 30 baru per poll.
        if ($after > 0) {
            $rows = $query->where('e.id', '>', $after)->orderBy('e.id')->limit(30)->get();
        } else {
            $rows = $query->orderByDesc('e.id')->limit(30)->get()->reverse()->values();
        }

        $events = $rows->map(function ($row) {
            $fresh = \Carbon\Carbon::parse($row->created_at)->greaterThanOrEqualTo(now()->subMinutes(2));
            return [
                'id' => (int) $row->id,
                'reader_name' => $row->reader_name ?: '(Reader dihapus)',
                'reader_serial' => $row->reader_serial,
                'uid_last4' => $row->uid_last4,
                'outcome' => $row->outcome,
                'member_name' => $row->user_name,
                'member_id' => $row->user_id,
                'occurred_at' => \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i:s'),
                'can_enroll' => $fresh && $row->outcome === 'unknown_or_inactive'
                    && $row->enrolled_at === null,
            ];
        });

        $readers = ReaderDevice::query()
            ->select(['id', 'serial', 'name', 'active', 'last_seen_at',
                'last_reader_ok', 'last_firmware_version', 'last_wifi_rssi'])
            ->orderBy('name')->get()->map(function ($reader) {
                $lastSeen = $reader->last_seen_at
                    ? \Carbon\Carbon::parse($reader->last_seen_at) : null;
                $online = $reader->active && $lastSeen
                    && $lastSeen->greaterThanOrEqualTo(now()->subSeconds(120));
                return [
                    'id' => $reader->id,
                    'serial' => $reader->serial,
                    'name' => $reader->name,
                    'status' => !$reader->active ? 'disabled'
                        : (!$online ? 'offline' : ($reader->last_reader_ok ? 'online' : 'reader_error')),
                    'firmware' => $reader->last_firmware_version,
                    'wifi_rssi' => $reader->last_wifi_rssi,
                    'last_seen_at' => $lastSeen ? $lastSeen->format('d/m/Y H:i:s') : null,
                ];
            });

        $diagnosticQuery = DB::table('reader_diagnostics as diag')
            ->join('reader_devices as d', 'd.id', '=', 'diag.reader_device_id')
            ->where('diag.created_at', '>=', now()->subHours(24));
        if (!empty($data['reader_id'])) {
            $diagnosticQuery->where('diag.reader_device_id', (int) $data['reader_id']);
        }
        $diagnostics = $diagnosticQuery
            ->orderByDesc('diag.id')->limit(15)
            ->get(['diag.id', 'diag.event', 'diag.firmware', 'diag.http_status',
                'diag.wifi_rssi', 'diag.created_at', 'd.name as reader_name'])
            ->map(fn ($row) => [
                'id' => (int) $row->id,
                'reader_name' => $row->reader_name,
                'event' => $row->event,
                'firmware' => $row->firmware,
                'http_status' => $row->http_status,
                'wifi_rssi' => $row->wifi_rssi,
                'occurred_at' => \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i:s'),
            ]);

        return response()->json(['events' => $events, 'readers' => $readers, 'diagnostics' => $diagnostics], 200)
            ->header('Cache-Control', 'private, no-store');
    }
}
