<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\User;
use App\Services\KioskQrResolver;
use App\Services\MemberIdentity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_physical_copies_and_print_qr_labels(): void
    {
        $admin = $this->user('admin', 'ADM-01');
        $book = $this->book(1);
        $original = DB::table('library_kiosk_book_copies')->where('book_id', $book->id_buku)->first();
        $oldQrPayload = Crypt::decryptString($original->qr_encrypted);

        $this->actingAs($admin)
            ->post(route('admin.databuku.inventory.store', $book), [
                'inventory_code' => 'INV-002',
                'barcode' => '9786021234567',
            ])->assertRedirect();

        $this->assertDatabaseHas('library_kiosk_book_copies', [
            'book_id' => $book->id_buku,
            'inventory_code' => 'INV-002',
            'barcode' => '9786021234567',
            'status' => 'available',
            'active' => true,
        ]);
        $this->assertSame(2, (int) $book->fresh()->stock);

        $copy = DB::table('library_kiosk_book_copies')->where('inventory_code', 'INV-002')->first();
        $this->actingAs($admin)
            ->patch(route('admin.databuku.inventory.update', [$book, $copy->id]), [
                'inventory_code' => 'INV-002-A',
                'barcode' => 'CODE-002-A',
            ])->assertRedirect();
        $this->assertDatabaseHas('library_kiosk_book_copies', [
            'id' => $copy->id, 'inventory_code' => 'INV-002-A', 'barcode' => 'CODE-002-A',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.databuku.inventory.print', ['book' => $book, 'copy_ids' => [$copy->id]]))
            ->assertOk()->assertSee('INV-002-A');

        $oldHash = $original->qr_hash;
        $this->actingAs($admin)
            ->post(route('admin.databuku.inventory.reissue', [$book, $original->id]))
            ->assertRedirect();
        $this->assertNotSame($oldHash, DB::table('library_kiosk_book_copies')->where('id', $original->id)->value('qr_hash'));
        $newCopy = DB::table('library_kiosk_book_copies')->where('id', $original->id)->first();
        $this->assertSame('not_registered', app(KioskQrResolver::class)->resolve($oldQrPayload)['outcome']);
        $this->assertSame('identified', app(KioskQrResolver::class)->resolve(Crypt::decryptString($newCopy->qr_encrypted))['outcome']);

        $this->actingAs($admin)
            ->patch(route('admin.databuku.inventory.toggle', [$book, $copy->id]), ['active' => false])
            ->assertRedirect();
        $this->assertDatabaseHas('library_kiosk_book_copies', ['id' => $copy->id, 'active' => false, 'status' => 'inactive']);
        $this->assertSame(1, (int) $book->fresh()->stock);
    }

    public function test_borrowed_copy_cannot_be_deactivated(): void
    {
        $admin = $this->user('admin', 'ADM-02');
        $book = $this->book(1);
        $copy = DB::table('library_kiosk_book_copies')->where('book_id', $book->id_buku)->first();
        DB::table('library_kiosk_book_copies')->where('id', $copy->id)->update(['status' => 'borrowed']);

        $this->actingAs($admin)
            ->patch(route('admin.databuku.inventory.toggle', [$book, $copy->id]), ['active' => false])
            ->assertRedirect()->assertSessionHas('error');

        $this->assertDatabaseHas('library_kiosk_book_copies', ['id' => $copy->id, 'active' => true, 'status' => 'borrowed']);
    }

    public function test_reissuing_member_card_invalidates_the_old_qr(): void
    {
        $admin = $this->user('admin', 'ADM-03');
        $member = $this->user('user', 'S-03');
        $identity = app(MemberIdentity::class);
        $oldPayload = $identity->payload($member);

        $this->actingAs($admin)
            ->post(route('admin.anggota.card.reissue', $member->id))
            ->assertRedirect(route('admin.anggota.card', $member->id));

        $newPayload = $identity->payload($member);
        $this->assertNotSame($oldPayload, $newPayload);
        $this->assertSame('inactive_or_unknown', app(KioskQrResolver::class)->resolve($oldPayload)['outcome']);
        $this->assertSame('identified', app(KioskQrResolver::class)->resolve($newPayload)['outcome']);
    }

    private function user(string $role, string $nim): User
    {
        return User::create([
            'nama' => strtoupper($role).' Uji',
            'nim' => $nim,
            'email' => strtolower($nim).'@example.test',
            'password' => Hash::make('TestPassword!123'),
            'role' => $role,
            'status' => 'aktif',
        ]);
    }

    private function book(int $stock): Buku
    {
        return Buku::create([
            'kode' => 'BK-OPS-1',
            'isbn' => '9786020000001',
            'judul' => 'Buku Operasional',
            'pengarang' => 'Penulis',
            'penerbit' => 'Penerbit',
            'tahun_terbit' => '2026',
            'kategori' => 'Uji',
            'stock' => $stock,
            'lokasi' => 'A1',
        ]);
    }
}
