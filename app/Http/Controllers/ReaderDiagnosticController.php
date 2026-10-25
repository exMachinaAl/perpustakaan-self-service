<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReaderDiagnosticController extends Controller
{
    public function store(Request $request)
    {
        // Device authorization, clock skew and replay prevention are performed by
        // reader.signature. Never accept arbitrary log messages or raw card UIDs.
        $reader = $request->attributes->get('verified_reader');
        if (array_diff(array_keys($request->all()),
            ['event', 'firmware', 'http_status', 'wifi_rssi'])) {
            return response()->json(['message' => 'Field tidak diizinkan'], 422);
        }
        $data = $request->validate([
            'event' => ['required', Rule::in([
                'boot', 'wifi_up', 'wifi_down', 'ntp_ready', 'reader_error',
                'reader_ready', 'oled_error', 'api_error', 'api_ready', 'scan_error',
            ])],
            'firmware' => ['required', 'string', 'max:32', 'regex:/\A[A-Za-z0-9._-]+\z/D'],
            'http_status' => ['nullable', 'integer', 'between:100,599'],
            'wifi_rssi' => ['nullable', 'integer', 'between:-120,0'],
        ]);
        DB::table('reader_diagnostics')->insert([
            'reader_device_id' => $reader->id,
            'event' => $data['event'],
            'firmware' => $data['firmware'],
            'http_status' => $data['http_status'] ?? null,
            'wifi_rssi' => $data['wifi_rssi'] ?? null,
            'created_at' => now(),
        ]);
        return response()->json(['accepted' => true], 202)->header('Cache-Control', 'no-store');
    }
}
