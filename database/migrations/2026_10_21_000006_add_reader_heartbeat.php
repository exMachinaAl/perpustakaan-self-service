<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reader_devices', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('last_reader_ok')->nullable();
            $table->string('last_firmware_version', 32)->nullable();
            $table->smallInteger('last_wifi_rssi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reader_devices', function (Blueprint $table) {
            $table->dropColumn(['last_seen_at', 'last_reader_ok', 'last_firmware_version', 'last_wifi_rssi']);
        });
    }
};
