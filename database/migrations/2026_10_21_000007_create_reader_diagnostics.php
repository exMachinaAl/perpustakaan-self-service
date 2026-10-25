<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reader_diagnostics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reader_device_id')->constrained('reader_devices')->cascadeOnDelete();
            $table->string('event', 32);
            $table->string('firmware', 32);
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->smallInteger('wifi_rssi')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['reader_device_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reader_diagnostics');
    }
};
