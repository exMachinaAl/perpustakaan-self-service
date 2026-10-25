<?php

namespace App\Http\Middleware;

use App\Models\ReaderDevice;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifyReaderSignature
{
    public function handle(Request $request, Closure $next)
    {
        // UID dan HMAC headers tidak boleh melintas plaintext di lingkungan produksi.
        // Jika memakai HTTPS reverse proxy, konfigurasi TrustProxies dengan benar.
        if (app()->environment('production') && !$request->isSecure()) {
            return response()->json(['message' => 'HTTPS wajib untuk reader'], 403);
        }

        $serial = (string) $request->header('X-Reader-ID', '');
        $timestamp = (string) $request->header('X-Reader-Time', '');
        $nonce = (string) $request->header('X-Reader-Nonce', '');
        $provided = (string) $request->header('X-Reader-Signature', '');

        if (!preg_match('/\A[A-Za-z0-9._-]{1,64}\z/D', $serial)
            || !preg_match('/\A[0-9]{10}\z/D', $timestamp)
            || !preg_match('/\A[a-fA-F0-9]{32,64}\z/D', $nonce)
            || !preg_match('/\A[a-fA-F0-9]{64}\z/D', $provided)) {
            return response()->json(['message' => 'Autentikasi perangkat gagal'], 401);
        }
        if (abs(time() - (int) $timestamp) > 90) {
            return response()->json(['message' => 'Waktu perangkat tidak sinkron'], 401);
        }
        $device = ReaderDevice::where('serial', $serial)->where('active', true)->first();
        if (!$device) {
            return response()->json(['message' => 'Perangkat tidak dikenal'], 401);
        }

        $canonical = implode("\n", [
            $request->method(),
            $request->getPathInfo(),
            $timestamp,
            strtolower($nonce),
            hash('sha256', $request->getContent()),
        ]);
        $expected = hash_hmac('sha256', $canonical, $device->secret);
        if (!hash_equals($expected, strtolower($provided))) {
            return response()->json(['message' => 'Signature tidak sesuai'], 401);
        }

        try {
            DB::table('reader_request_nonces')->insert([
                'reader_device_id' => $device->id,
                'nonce' => strtolower($nonce),
                'created_at' => now(),
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return response()->json(['message' => 'Replay request ditolak'], 409);
        }

        $request->attributes->set('verified_reader', $device);
        return $next($request);
    }
}
