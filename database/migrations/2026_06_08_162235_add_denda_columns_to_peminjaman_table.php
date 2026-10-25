<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->unsignedBigInteger('denda')->default(0)->after('tanggal_kembali');
            $table->unsignedBigInteger('denda_dibayar')->default(0)->after('denda');
            $table->enum('status_denda', ['tidak_ada', 'belum_lunas', 'lunas'])
                  ->default('tidak_ada')
                  ->after('denda_dibayar');
            $table->date('tanggal_bayar_denda')->nullable()->after('status_denda');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn([
                'denda',
                'denda_dibayar',
                'status_denda',
                'tanggal_bayar_denda',
            ]);
        });
    }
};
