<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prodi', function (Blueprint $table) {
            $table->string('kode_prodi')->nullable()->after('id');
            $table->string('fakultas')->nullable()->after('nama');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('fakultas');
        });
    }

    public function down(): void
    {
        Schema::table('prodi', function (Blueprint $table) {
            $table->dropColumn(['kode_prodi', 'fakultas', 'status']);
        });
    }
};