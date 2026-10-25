<?php

namespace Tests\Feature;

use App\Models\ReaderDevice;
use App\Models\RfidCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RfidDeskPatchTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'admin'): User
    {
        return User::create([
            'nama' => $role === 'admin' ? 'Admin Petugas' : 'Siswa Test',
            'nim' => $role . '-' . bin2hex(random_bytes(4)),
            'email' => bin2hex(random_bytes(5)) . '@example.test',
            'password' => Hash::make('TestPassword123!'),
            'role' => $role,
            'status' => 'aktif',
        ]);
    }

    private function signedHeaders(string $uid, string $nonce, string $secret): array
    {
        $body = json_encode(['uid' => $uid], JSON_UNESCAPED_SLASHES);
        $timestamp = (string) time();
        $canonical = implode("\n", ['POST', '/api/v1/reader/scan', $timestamp, $nonce, hash('sha256', $body)]);
        return [
            'X-Reader-ID' => 'TEST-RFID-01',
            'X-Reader-Time' => $timestamp,
            'X-Reader-Nonce' => $nonce,
            'X-Reader-Signature' => hash_hmac('sha256', $canonical, $secret),
            'Content-Type' => 'application/json',
        ];
    }

    public function test_rfid_monitor_requires_admin(): void
    {
        $this->get('/admin/rfid/events')->assertRedirect('/login');
        $this->actingAs($this->makeUser('user'))
            ->get('/admin/rfid/events')->assertForbidden();
    }

    public function test_signed_reader_event_is_queued_without_exposing_member_name_and_deduplicated(): void
    {
        $secret = bin2hex(random_bytes(32));
        ReaderDevice::create(['serial' => 'TEST-RFID-01', 'name' => 'Meja 1', 'secret' => $secret, 'active' => true]);
        $member = $this->makeUser('user');
        RfidCard::create([
            'user_id' => $member->id,
            'uid_hash' => RfidCard::digest('04AABBCC'),
            'uid_last4' => 'BBCC',
            'status' => 'aktif',
        ]);

        $first = $this->withHeaders($this->signedHeaders('04AABBCC', bin2hex(random_bytes(16)), $secret))
            ->postJson('/api/v1/reader/scan', ['uid' => '04AABBCC']);
        $first->assertStatus(202)->assertJson(['accepted' => true, 'queued' => true]);
        $this->assertStringNotContainsString($member->nama, $first->getContent());

        $second = $this->withHeaders($this->signedHeaders('04AABBCC', bin2hex(random_bytes(16)), $secret))
            ->postJson('/api/v1/reader/scan', ['uid' => '04AABBCC']);
        $second->assertStatus(202)->assertJson(['accepted' => true, 'queued' => false]);
        $this->assertSame(1, DB::table('rfid_scan_events')->count());
    }

    public function test_reader_replay_nonce_is_rejected(): void
    {
        $secret = bin2hex(random_bytes(32));
        ReaderDevice::create(['serial' => 'TEST-RFID-01', 'name' => 'Meja 1', 'secret' => $secret, 'active' => true]);
        $headers = $this->signedHeaders('04AABBCC', bin2hex(random_bytes(16)), $secret);
        $this->withHeaders($headers)->postJson('/api/v1/reader/scan', ['uid' => '04AABBCC'])->assertStatus(202);
        $this->withHeaders($headers)->postJson('/api/v1/reader/scan', ['uid' => '04AABBCC'])->assertStatus(409);
    }

    public function test_admin_can_pair_fresh_unknown_scan_but_cannot_reuse_it(): void
    {
        $admin = $this->makeUser('admin');
        $member = $this->makeUser('user');
        $device = ReaderDevice::create([
            'serial' => 'TEST-RFID-01', 'name' => 'Meja 1',
            'secret' => bin2hex(random_bytes(32)), 'active' => true,
        ]);
        $eventId = DB::table('rfid_scan_events')->insertGetId([
            'reader_device_id' => $device->id,
            'uid_last4' => 'BBCC',
            'uid_fingerprint' => RfidCard::digest('04AABBCC'),
            'outcome' => 'unknown_or_inactive',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($admin)->post('/admin/rfid/enroll', [
            'scan_event_id' => $eventId, 'user_id' => $member->id, 'confirmed' => 1,
        ])->assertRedirect(route('admin.rfid.index'));

        $this->assertDatabaseHas('rfid_cards', [
            'user_id' => $member->id, 'uid_hash' => RfidCard::digest('04AABBCC'), 'status' => 'aktif',
        ]);
        $this->actingAs($admin)->post('/admin/rfid/enroll', [
            'scan_event_id' => $eventId, 'user_id' => $member->id, 'confirmed' => 1,
        ])->assertSessionHasErrors('scan_event_id');
    }
}
