<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ReaderSecurityTest extends TestCase {
 use RefreshDatabase;
 public function test_unsigned_reader_is_rejected():void {
  $this->postJson('/api/v1/reader/scan',['uid'=>'04AABBCC'])->assertUnauthorized();
 }
 public function test_reset_requires_token():void {
  $this->post('/reset-password',['email'=>'x@example.test','password'=>'SafePassword123!','password_confirmation'=>'SafePassword123!'])
   ->assertSessionHasErrors('token');
 }
}
