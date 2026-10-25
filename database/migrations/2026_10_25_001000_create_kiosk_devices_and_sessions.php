<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('library_kiosk_devices')) Schema::create('library_kiosk_devices', function (Blueprint $t) {
            $t->id();
            $t->string('name',100);
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->char('enroll_code_hash',64)->nullable()->unique();
            $t->timestamp('enroll_expires_at')->nullable();
            $t->char('credential_hash',64)->nullable()->unique();
            $t->boolean('active')->default(false);
            $t->timestamp('enrolled_at')->nullable();
            $t->timestamp('last_seen_at')->nullable();
            $t->timestamps();
        });
        if (!Schema::hasTable('library_kiosk_device_sessions')) Schema::create('library_kiosk_device_sessions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('device_id')->unique()->constrained('library_kiosk_devices')->cascadeOnDelete();
            $t->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $t->unsignedBigInteger('copy_id')->nullable();
            $t->string('state',24)->default('ready');
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });
        if (!Schema::hasTable('library_kiosk_device_events')) Schema::create('library_kiosk_device_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('device_id')->constrained('library_kiosk_devices')->cascadeOnDelete();
            $t->string('kind',24);
            $t->string('outcome',40);
            $t->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $t->unsignedBigInteger('copy_id')->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->index(['device_id','id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('library_kiosk_device_events');
        Schema::dropIfExists('library_kiosk_device_sessions');
        Schema::dropIfExists('library_kiosk_devices');
    }
};
