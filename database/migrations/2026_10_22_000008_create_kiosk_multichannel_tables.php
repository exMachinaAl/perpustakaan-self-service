<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('library_kiosk_member_passes', function(Blueprint $t) {
            $t->id();$t->foreignId('user_id')->constrained('users')->cascadeOnDelete()->unique();
            $t->char('token_hash',64)->unique();$t->timestamp('expires_at');$t->timestamp('consumed_at')->nullable();$t->timestamps();
        });
        Schema::create('library_kiosk_book_copies', function(Blueprint $t) {
            $t->id();$t->unsignedBigInteger('book_id');$t->string('label',80);
            $t->char('qr_hash',64)->unique();$t->text('qr_encrypted');$t->boolean('active')->default(true);
            $t->timestamps();$t->foreign('book_id')->references('id_buku')->on('buku')->cascadeOnDelete();
            $t->index(['book_id','active']);
        });
        Schema::create('library_kiosk_pairs', function(Blueprint $t) {
            $t->id();$t->char('token_hash',64)->unique();
            $t->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('expires_at');$t->timestamp('used_at')->nullable();$t->timestamps();
        });
        Schema::create('library_kiosk_events', function(Blueprint $t) {
            $t->id();$t->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('reader_device_id')->nullable()->constrained('reader_devices')->nullOnDelete();
            $t->string('channel',24);$t->string('kind',16);$t->string('outcome',32);
            $t->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $t->unsignedBigInteger('book_id')->nullable();$t->unsignedBigInteger('copy_id')->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->index(['admin_id','id']);$t->index(['reader_device_id','id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('library_kiosk_events');Schema::dropIfExists('library_kiosk_pairs');
        Schema::dropIfExists('library_kiosk_book_copies');Schema::dropIfExists('library_kiosk_member_passes');
    }
};
