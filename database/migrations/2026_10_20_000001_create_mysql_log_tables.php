<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('nama_user')->nullable();
            $table->string('role', 30)->nullable();
            $table->string('aktivitas');
            $table->text('keterangan')->nullable();
            $table->json('data')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->dateTime('waktu')->nullable()->index();
            $table->string('source_mongo_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('analytics_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('nama_user')->nullable();
            $table->string('role', 30)->nullable();
            $table->string('event')->nullable();
            $table->string('halaman')->nullable();
            $table->string('aksi')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('data')->nullable();
            $table->dateTime('waktu')->nullable()->index();
            $table->string('source_mongo_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('search_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('nama_user')->nullable();
            $table->string('role', 30)->nullable();
            $table->text('keyword')->nullable();
            $table->string('kategori')->nullable();
            $table->string('sort')->nullable();
            $table->string('halaman')->nullable();
            $table->text('keterangan')->nullable();
            $table->json('data')->nullable();
            $table->dateTime('waktu')->nullable()->index();
            $table->string('source_mongo_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('nama_user')->nullable();
            $table->string('judul')->nullable();
            $table->text('pesan')->nullable();
            $table->string('tipe')->nullable();
            $table->string('status', 30)->default('belum_dibaca')->index();
            $table->boolean('dibaca')->default(false);
            $table->json('data')->nullable();
            $table->dateTime('waktu')->nullable()->index();
            $table->string('source_mongo_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Only the new log tables are touched; users / books / loans remain intact.
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('search_history');
        Schema::dropIfExists('analytics_logs');
        Schema::dropIfExists('activity_logs');
    }
};
