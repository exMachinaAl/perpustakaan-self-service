<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_member_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->char('card_hash', 64)->unique();
            $table->text('card_encrypted');
            $table->string('pin_hash')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });

        Schema::table('buku', function (Blueprint $table) {
            $table->string('isbn', 20)->nullable()->index()->after('kode');
        });

        Schema::table('library_kiosk_book_copies', function (Blueprint $table) {
            $table->string('inventory_code', 80)->nullable()->unique()->after('label');
            $table->string('barcode', 120)->nullable()->unique()->after('inventory_code');
            $table->string('status', 24)->default('available')->index()->after('active');
        });

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->unsignedBigInteger('copy_id')->nullable()->after('id_buku');
            $table->unsignedBigInteger('kiosk_device_id')->nullable()->after('copy_id');
            $table->string('source', 24)->default('web')->after('kiosk_device_id');
            $table->date('due_date')->nullable()->after('tanggal_pinjam');
            $table->index(['copy_id', 'status']);
        });

        Schema::table('library_kiosk_device_sessions', function (Blueprint $table) {
            $table->timestamp('member_authenticated_at')->nullable()->after('member_id');
        });

        Schema::create('library_kiosk_self_checkouts', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id')->unique();
            $table->foreignId('device_id')->constrained('library_kiosk_devices')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('copy_id');
            $table->unsignedBigInteger('loan_id')->unique();
            $table->date('due_date');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['device_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_kiosk_self_checkouts');

        Schema::table('library_kiosk_device_sessions', function (Blueprint $table) {
            $table->dropColumn('member_authenticated_at');
        });

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropIndex(['copy_id', 'status']);
            $table->dropColumn(['copy_id', 'kiosk_device_id', 'source', 'due_date']);
        });

        Schema::table('library_kiosk_book_copies', function (Blueprint $table) {
            $table->dropColumn(['inventory_code', 'barcode', 'status']);
        });

        Schema::table('buku', function (Blueprint $table) {
            $table->dropColumn('isbn');
        });

        Schema::dropIfExists('library_member_credentials');
    }
};
