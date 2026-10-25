<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('rfid_cards',function(Blueprint $t){
   $t->id();$t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
   $t->char('uid_hash',64)->unique();$t->string('uid_last4',4);$t->string('status',20)->default('aktif')->index();
   $t->timestamps();
  });
  Schema::create('reader_devices',function(Blueprint $t){
   $t->id();$t->string('serial')->unique();$t->string('name');
   $t->text('secret')->comment('Encrypted by Laravel encrypted cast; do not dump');
   $t->boolean('active')->default(true);$t->timestamps();
  });
  Schema::create('reader_request_nonces',function(Blueprint $t){
   $t->id();$t->foreignId('reader_device_id')->constrained('reader_devices')->cascadeOnDelete();
   $t->string('nonce',80);$t->timestamp('created_at')->useCurrent();
   $t->unique(['reader_device_id','nonce']);
   $t->index('created_at');
  });
  Schema::create('rfid_scan_events',function(Blueprint $t){
   $t->id();$t->foreignId('reader_device_id')->nullable()->constrained('reader_devices')->nullOnDelete();
   $t->unsignedBigInteger('user_id')->nullable();$t->string('uid_last4',4)->nullable();
   $t->string('outcome',24);$t->timestamps();
  });
 }
 public function down():void {
  Schema::dropIfExists('rfid_scan_events');Schema::dropIfExists('reader_request_nonces');
  Schema::dropIfExists('reader_devices');Schema::dropIfExists('rfid_cards');
 }
};
