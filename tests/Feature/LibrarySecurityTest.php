<?php
namespace Tests\Feature;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\LoanWorkflow;
use App\Services\SecureLoanQr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class LibrarySecurityTest extends TestCase {
 use RefreshDatabase;
 private function member(string $role='user',string $status='aktif'):User {
  return User::create(['nama'=>'Tester '.$role,'nim'=>'N-'.bin2hex(random_bytes(5)),
   'email'=>bin2hex(random_bytes(4)).'@example.test','password'=>Hash::make('TestPassword!123'),
   'role'=>$role,'status'=>$status]);
 }
 private function book(int $stock=1):Buku {return Buku::create([
  'kode'=>'B-'.bin2hex(random_bytes(4)),'judul'=>'Testing','pengarang'=>'Tester','penerbit'=>'Test',
  'tahun_terbit'=>'2026','kategori'=>'Umum','stock'=>$stock,'lokasi'=>'A1']);}
 public function test_member_cannot_reach_admin():void {
  $this->actingAs($this->member())->get('/admin/dashboard')->assertForbidden();
 }
 public function test_admin_cannot_reach_member_dashboard():void {
  $this->actingAs($this->member('admin'))->get('/user/dashboard')->assertForbidden();
 }
 public function test_blocked_account_is_logged_out():void {
  $this->actingAs($this->member('user','diblokir'))->get('/user/dashboard')->assertRedirect(route('login'));
 }
 public function test_stock_is_changed_once_only():void {
  $user=$this->member();$book=$this->book(1);
  $flow=app(LoanWorkflow::class);
  $loan=$flow->request($user->id,$book->id_buku);
  $flow->apply($loan->peminjaman_id,'dipinjam');
  $flow->apply($loan->peminjaman_id,'dipinjam');
  $this->assertEquals(0,$book->fresh()->stock);
  $flow->apply($loan->peminjaman_id,'dikembalikan');
  $this->assertEquals(1,$book->fresh()->stock);
  $this->expectException(\DomainException::class);
  $flow->apply($loan->peminjaman_id,'dipinjam');
 }
 public function test_scan_token_is_single_use():void {
  $user=$this->member();$book=$this->book();$flow=app(LoanWorkflow::class);
  $loan=$flow->request($user->id,$book->id_buku);
  $loan=$flow->apply($loan->peminjaman_id,'dipinjam');
  $qr=app(SecureLoanQr::class);
  $token=$qr->issue($loan);
  $this->assertNotNull($qr->consume($token,$user->id));
  $this->assertNull($qr->consume($token,$user->id));
 }
}
