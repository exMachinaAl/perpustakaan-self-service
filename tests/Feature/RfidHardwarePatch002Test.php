<?php

namespace Tests\Feature;

use App\Models\ReaderDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RfidHardwarePatch002Test extends TestCase
{
    use RefreshDatabase;

    private function headers(string $path, string $body, string $secret, string $nonce): array
    {
        $ts = (string) time();
        $canonical = implode("\n", ['POST', $path, $ts, $nonce, hash('sha256', $body)]);
        return [
            'X-Reader-ID' => 'ESP32-TEST-002',
            'X-Reader-Time' => $ts,
            'X-Reader-Nonce' => $nonce,
            'X-Reader-Signature' => hash_hmac('sha256', $canonical, $secret),
        ];
    }

    public function test_heartbeat_requires_valid_signature_and_updates_reader_status(): void
    {
        $secret = bin2hex(random_bytes(32));
        $reader = ReaderDevice::create([
            'serial' => 'ESP32-TEST-002', 'name' => 'Meja Tes', 'secret' => $secret, 'active' => true,
        ]);
        $data = ['firmware' => '2.0.0', 'reader_ok' => true, 'wifi_rssi' => -56];
        $body = json_encode($data, JSON_UNESCAPED_SLASHES);
        $path = '/api/v1/reader/heartbeat';
        $this->postJson($path, $data)->assertStatus(401);
        $nonce = bin2hex(random_bytes(16));
        $headers = $this->headers($path, $body, $secret, $nonce);
        $this->withHeaders($headers)->postJson($path, $data)->assertOk()->assertJson(['accepted' => true]);
        $this->assertDatabaseHas('reader_devices', [
            'id' => $reader->id, 'last_reader_ok' => 1,
            'last_firmware_version' => '2.0.0', 'last_wifi_rssi' => -56,
        ]);
        $this->assertNotNull(DB::table('reader_devices')->where('id', $reader->id)->value('last_seen_at'));
        $this->withHeaders($headers)->postJson($path, $data)->assertStatus(409);
    }

    public function test_admin_monitor_contains_reader_status_and_non_admin_denied(): void
    {
        $admin = User::create([
            'nama' => 'Petugas', 'nim' => 'RFID-ADMIN-002', 'email' => 'admin002@example.test',
            'password' => Hash::make('Password123!'), 'role' => 'admin', 'status' => 'aktif',
        ]);
        $student = User::create([
            'nama' => 'Siswa', 'nim' => 'RFID-STUDENT-002', 'email' => 'student002@example.test',
            'password' => Hash::make('Password123!'), 'role' => 'user', 'status' => 'aktif',
        ]);
        ReaderDevice::create(['serial'=>'ESP32-TEST-002', 'name'=>'Meja Tes',
            'secret'=>bin2hex(random_bytes(32)), 'active'=>true]);
        $this->actingAs($student)->get('/admin/rfid/events')->assertForbidden();
        $this->actingAs($admin)->get('/admin/rfid/events')
            ->assertOk()->assertJsonStructure(['events', 'readers' => [['serial','status']]]);
    }
}
