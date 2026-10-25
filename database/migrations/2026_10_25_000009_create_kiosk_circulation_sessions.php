<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('library_kiosk_circulation_sessions')) Schema::create('library_kiosk_circulation_sessions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('admin_id')->unique()->constrained('users')->cascadeOnDelete();
            $t->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $t->unsignedBigInteger('copy_id')->nullable();
            $t->string('status',20)->default('waiting');
            $t->unsignedBigInteger('last_loan_id')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });
        if (!Schema::hasTable('library_kiosk_checkout_audit')) Schema::create('library_kiosk_checkout_audit', function (Blueprint $t) {
            $t->id();
            $t->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $t->unsignedBigInteger('copy_id');
            $t->unsignedBigInteger('loan_id')->nullable();
            $t->string('mode',16);
            $t->timestamp('created_at')->useCurrent();
            $t->index(['copy_id','created_at']);
            $t->index(['loan_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('library_kiosk_checkout_audit');
        Schema::dropIfExists('library_kiosk_circulation_sessions');
    }
};
