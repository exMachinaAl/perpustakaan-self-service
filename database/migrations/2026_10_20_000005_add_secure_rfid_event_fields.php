<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rfid_scan_events', function (Blueprint $table) {
            $table->char('uid_fingerprint', 64)->nullable()->after('uid_last4');
            $table->timestamp('enrolled_at')->nullable()->after('outcome');
            $table->unsignedBigInteger('enrolled_by')->nullable()->after('enrolled_at');
            $table->index(['reader_device_id', 'uid_fingerprint', 'created_at'], 'rfid_event_cooldown_idx');
        });
    }

    public function down(): void
    {
        Schema::table('rfid_scan_events', function (Blueprint $table) {
            $table->dropIndex('rfid_event_cooldown_idx');
            $table->dropColumn(['uid_fingerprint', 'enrolled_at', 'enrolled_by']);
        });
    }
};
