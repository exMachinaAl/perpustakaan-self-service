<?php
namespace App\Services;
use App\Models\LoanScanToken;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;
class SecureLoanQr {
 public function issue(Peminjaman $loan):string {
  abort_unless($loan->status==='dipinjam',403);
  return DB::transaction(function() use ($loan){
   LoanScanToken::where('peminjaman_id',$loan->peminjaman_id)->whereNull('used_at')->update(['expires_at'=>now()]);
   $token=bin2hex(random_bytes(32));
   LoanScanToken::create(['peminjaman_id'=>$loan->peminjaman_id,'token_hash'=>hash('sha256',$token),'expires_at'=>now()->addMinutes(3)]);
   return 'LIBQR1:'.$token;
  });
 }
 public function consume(string $raw,?int $actorId):?Peminjaman {
  if(!preg_match('/^LIBQR1:([a-f0-9]{64})$/D',$raw,$m)){return null;}
  return DB::transaction(function()use($m,$actorId){
   $qr=LoanScanToken::where('token_hash',hash('sha256',$m[1]))->lockForUpdate()->first();
   if(!$qr || $qr->used_at || !$qr->expires_at->isFuture())return null;
   $loan=Peminjaman::with(['user','buku'])->find($qr->peminjaman_id);
   if(!$loan || $loan->status!=='dipinjam' || !$loan->user || $loan->user->status!=='aktif')return null;
   $qr->update(['used_at'=>now()]);
   DB::table('scan_events')->insert(['peminjaman_id'=>$loan->peminjaman_id,'actor_user_id'=>$actorId,'origin'=>'qr','outcome'=>'valid','ip_address'=>request()->ip(),'created_at'=>now(),'updated_at'=>now()]);
   return $loan;
  });
 }
}
