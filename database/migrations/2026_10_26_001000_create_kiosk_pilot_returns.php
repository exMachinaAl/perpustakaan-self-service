<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('library_kiosk_pilot_returns')) {
            Schema::create('library_kiosk_pilot_returns', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('loan_id')->unique();
                $t->unsignedBigInteger('copy_id');
                $t->unsignedBigInteger('accepted_by');
                $t->string('physical_condition', 255)->nullable();
                $t->timestamp('received_at')->useCurrent();
                $t->index(['copy_id','received_at']);
            });
        }
    }
    // Data transaksi harus dipertahankan. Rollback source tidak menghapus log penerimaan.
    public function down(): void {}
};
