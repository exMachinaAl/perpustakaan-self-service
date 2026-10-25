<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasi_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('id_buku')
                ->constrained('buku', 'id_buku')
                ->cascadeOnDelete();
            $table->date('tanggal_pengajuan');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])
                ->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_peminjaman');
    }

};
