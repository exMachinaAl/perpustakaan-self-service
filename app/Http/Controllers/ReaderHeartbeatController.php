<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReaderHeartbeatController extends Controller
{
    public function store(Request $request)
    {
        // 'reader.signature' has validated HMAC + bounded clock skew + nonce.
        $device = $request->attributes->get('verified_reader');
        $data = $request->validate([
            'firmware' => ['required', 'string', 'max:32', 'regex:/\A[A-Za-z0-9._-]+\z/D'],
            'reader_ok' => ['required', 'boolean'],
            'wifi_rssi' => ['nullable', 'integer', 'between:-120,0'],
        ]);

        DB::table('reader_devices')->where('id', $device->id)->where('active', true)->update([
            'last_seen_at' => now(),
            'last_reader_ok' => $data['reader_ok'],
            'last_firmware_version' => $data['firmware'],
            'last_wifi_rssi' => $data['wifi_rssi'] ?? null,
            'updated_at' => now(),
        ]);

        return response()->json(['accepted' => true], 200)->header('Cache-Control', 'no-store');
    }
}
