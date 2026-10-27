<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\User;
use App\Services\BookInventory;
use App\Services\KioskStationService;
use App\Services\MemberIdentity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class SelfServiceCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_and_book_are_provisioned_and_checkout_is_idempotent(): void
    {
        $admin = User::create([
            'nama' => 'Admin', 'nim' => 'ADM-1', 'email' => 'admin@example.test',
            'password' => Hash::make('TestPassword!123'), 'role' => 'admin', 'status' => 'aktif',
        ]);
        $member = User::create([
            'nama' => 'Siswa', 'nim' => 'S-1', 'email' => 'siswa@example.test',
            'password' => Hash::make('TestPassword!123'), 'role' => 'user', 'status' => 'aktif',
        ]);
        app(MemberIdentity::class)->setPin($member, '123456');
        $this->actingAs($member)->get(route('user.kiosk.pass'))->assertOk()->assertSee('Kartu QR Anggota');

        $book = Buku::create([
            'kode' => 'BK-1', 'isbn' => '9786020000000', 'judul' => 'Buku Uji',
            'pengarang' => 'Penulis', 'penerbit' => 'Penerbit', 'tahun_terbit' => '2026',
            'kategori' => 'Uji', 'stock' => 1, 'lokasi' => 'A1',
        ]);
        app(BookInventory::class)->assignBarcodes($book, ['INV-0001']);

        $credential = DB::table('library_member_credentials')->where('user_id', $member->id)->first();
        $copy = DB::table('library_kiosk_book_copies')->where('book_id', $book->id_buku)->first();
        $deviceId = DB::table('library_kiosk_devices')->insertGetId([
            'name' => 'Kiosk Uji', 'created_by' => $admin->id, 'active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $station = app(KioskStationService::class);
        $this->assertSame('waiting_pin', $station->scan($deviceId, Crypt::decryptString($credential->card_encrypted), 'usb')['state']);
        $this->assertSame('waiting_book', $station->authenticate($deviceId, '123456')['state']);
        $this->assertSame('ready_checkout', $station->scan($deviceId, 'INV-0001', 'usb')['state']);

        $requestId = (string) Str::uuid();
        $first = $station->checkout($deviceId, $requestId);
        $second = $station->checkout($deviceId, $requestId);

        $this->assertSame('borrowed', $first['state']);
        $this->assertSame($first['loan_id'], $second['loan_id']);
        $this->assertDatabaseCount('peminjaman', 1);
        $this->assertDatabaseHas('library_kiosk_book_copies', ['id' => $copy->id, 'status' => 'borrowed']);
        $this->assertSame(0, (int) $book->fresh()->stock);
    }
}
