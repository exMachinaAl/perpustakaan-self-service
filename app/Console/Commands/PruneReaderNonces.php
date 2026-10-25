<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class PruneReaderNonces extends Command {
 protected $signature='perpus:prune-reader-nonces';
 protected $description='Hapus nonce lama agar tabel anti-replay tidak tumbuh tanpa batas';
 public function handle():int {
  $deleted=DB::table('reader_request_nonces')->where('created_at','<',now()->subHours(24))->delete();
  $this->info("$deleted nonce lama dihapus.");return 0;
 }
}
