<?php

namespace Tests\Feature;

use App\Models\ReaderDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReaderDiagnosticsPatch003Test extends TestCase
{
    use RefreshDatabase;

    private function signedHeaders(string $path, array $data, string $secret, string $nonce): array
    {
        $ts = (string) time();
        $body = json_encode($data, JSON_UNESCAPED_SLASHES);
        $canonical = "POST\n{$path}\n{$ts}\n{$nonce}\n" . hash('sha256', $body);
        return [
            'X-Reader-ID' => 'ESP32-PATCH003',
            'X-Reader-Time' => $ts,
            'X-Reader-Nonce' => $nonce,
            'X-Reader-Signature' => hash_hmac('sha256', $canonical, $secret),
        ];
    }

    public function test_device_diagnostic_requires_signed_request_without_raw_uid(): void
    {
        $secret = bin2hex(random_bytes(32));
        ReaderDevice::create(['serial' => 'ESP32-PATCH003', 'name' => 'Meja Tes',
            'secret' => $secret, 'active' => true]);
        $data = ['event' => 'api_ready', 'firmware' => '3.0.0-debug', 'http_status' => 200];
        $path = '/api/v1/reader/diagnostic';
        $this->postJson($path, $data)->assertStatus(401);
        $headers = $this->signedHeaders($path, $data, $secret, bin2hex(random_bytes(16)));
        $this->withHeaders($headers)->postJson($path, $data)->assertStatus(202);
        $this->assertDatabaseHas('reader_diagnostics', ['event' => 'api_ready',
            'firmware' => '3.0.0-debug']);
        $this->withHeaders($headers)->postJson($path, $data)->assertStatus(409);
        $withUid = ['event' => 'api_ready', 'firmware' => '3.0.0-debug', 'uid' => 'AABBCCDD'];
        // Strict reject unknown payload keys in diagnostics (raw UID must not be logged).
        $this->withHeaders($this->signedHeaders($path, $withUid, $secret, bin2hex(random_bytes(16))))
            ->postJson($path, $withUid)->assertStatus(422);
    }

    public function test_only_admin_can_view_diagnostics(): void
    {
        $admin = User::create(['nama'=>'Admin','nim'=>'ADMIN-003','email'=>'admin003@example.test',
            'password'=>Hash::make('Password123!'),'role'=>'admin','status'=>'aktif']);
        $student = User::create(['nama'=>'Siswa','nim'=>'USER-003','email'=>'user003@example.test',
            'password'=>Hash::make('Password123!'),'role'=>'user','status'=>'aktif']);
        $this->actingAs($student)->get('/admin/rfid/events')->assertForbidden();
        $this->actingAs($admin)->get('/admin/rfid/events')
            ->assertOk()->assertJsonStructure(['readers', 'events', 'diagnostics']);
    }
}
