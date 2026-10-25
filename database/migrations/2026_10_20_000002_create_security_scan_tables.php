<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('loan_scan_tokens',function(Blueprint $t){
   $t->id();$t->foreignId('peminjaman_id')->constrained('peminjaman','peminjaman_id')->cascadeOnDelete();
   $t->char('token_hash',64)->unique();$t->timestamp('expires_at')->index();$t->timestamp('used_at')->nullable();$t->timestamps();
  });
  Schema::create('scan_events',function(Blueprint $t){
   $t->id();$t->foreignId('peminjaman_id')->nullable()->constrained('peminjaman','peminjaman_id')->nullOnDelete();
   $t->unsignedBigInteger('actor_user_id')->nullable();$t->string('origin',32);$t->string('outcome',40);
   $t->string('ip_address',45)->nullable();$t->timestamps();
  });
 }
 public function down():void {Schema::dropIfExists('scan_events');Schema::dropIfExists('loan_scan_tokens');}
};
