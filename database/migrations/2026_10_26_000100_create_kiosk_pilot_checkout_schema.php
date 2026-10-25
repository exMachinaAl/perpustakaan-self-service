<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('library_kiosk_device_sessions', 'validated_event_id')) {
            Schema::table('library_kiosk_device_sessions', function (Blueprint $t) {
                $t->unsignedBigInteger('validated_event_id')->nullable()->unique();
                $t->unsignedBigInteger('checkout_loan_id')->nullable();
                $t->date('checkout_due_date')->nullable();
            });
        }
        if (!Schema::hasTable('library_kiosk_pilot_checkouts')) {
            Schema::create('library_kiosk_pilot_checkouts', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('device_id');
                $t->unsignedBigInteger('scan_event_id')->unique();
                $t->unsignedBigInteger('approved_by');
                $t->unsignedBigInteger('member_id');
                $t->unsignedBigInteger('copy_id');
                $t->unsignedBigInteger('loan_id')->unique();
                $t->date('due_date');
                $t->timestamp('created_at')->useCurrent();
                $t->index(['copy_id', 'created_at']);
            });
        }
    }
    public function down(): void {
        // Manual DB review is required before rollback if any pilot loan exists.
        if (Schema::hasTable('library_kiosk_pilot_checkouts')) Schema::drop('library_kiosk_pilot_checkouts');
        if (Schema::hasColumn('library_kiosk_device_sessions','validated_event_id')) {
            Schema::table('library_kiosk_device_sessions', function (Blueprint $t) {
                $t->dropColumn(['validated_event_id','checkout_loan_id','checkout_due_date']);
            });
        }
    }
};
