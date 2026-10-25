<?php
namespace App\Services;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DomainException;
class LoanWorkflow {
    public function apply(int $id, string $target):Peminjaman {
        return DB::transaction(function() use($id,$target){
            $lookup=Peminjaman::findOrFail($id);
            $book=Buku::whereKey($lookup->id_buku)->lockForUpdate()->firstOrFail();
            $loan=Peminjaman::whereKey($id)->lockForUpdate()->firstOrFail();
            $from=$loan->status;
            $allowed=['menunggu'=>['dipinjam','ditolak','dibatalkan'], 'dipinjam'=>['dikembalikan']];
            if($from===$target) return $loan;
            if(!in_array($target,$allowed[$from]??[],true))throw new DomainException('Perubahan status tidak diizinkan: '.$from.' → '.$target);
            if($target==='dipinjam'){
                if($book->stock<1)throw new DomainException('Stok buku sudah habis.');
                if(!$loan->user || $loan->user->status!=='aktif')throw new DomainException('Akun peminjam tidak aktif.');
                $book->decrement('stock');
                $loan->tanggal_pinjam=today();$loan->tanggal_kembali=null;
            }
            if($target==='dikembalikan'){
                $book->increment('stock');
                $date=today();$loan->tanggal_kembali=$date;
                $due=Carbon::parse($loan->tanggal_pinjam)->startOfDay()->addDays(7);
                $late=max(0,(int)$due->diffInDays($date,false));
                $loan->denda=$late*2000;
                $loan->denda_dibayar=0;
                $loan->status_denda=$loan->denda>0?'belum_lunas':'tidak_ada';
                $loan->tanggal_bayar_denda=null;
            }
            if(in_array($target,['ditolak','dibatalkan'],true)){
                $loan->tanggal_pinjam=null;$loan->tanggal_kembali=null;
            }
            $loan->status=$target;$loan->save();
            return $loan->fresh(['user','buku']);
        },3);
    }
    public function request(int $userId, int $bookId):Peminjaman {
        return DB::transaction(function()use($userId,$bookId){
            $book=Buku::whereKey($bookId)->lockForUpdate()->firstOrFail();
            if($book->stock<1)throw new DomainException('Stok buku tidak tersedia.');
            if(Peminjaman::where('user_id',$userId)->where('id_buku',$bookId)
                ->whereIn('status',['menunggu','dipinjam'])->exists())throw new DomainException('Anda sudah mengajukan atau sedang meminjam buku ini.');
            return Peminjaman::create(['user_id'=>$userId,'id_buku'=>$bookId,'status'=>'menunggu','tanggal_pinjam'=>null,'tanggal_kembali'=>null,'denda'=>0,'denda_dibayar'=>0,'status_denda'=>'tidak_ada']);
        },3);
    }
    public function payFine(int $id,int $paid):Peminjaman {
        return DB::transaction(function()use($id,$paid){
            $loan=Peminjaman::whereKey($id)->lockForUpdate()->firstOrFail();
            $remaining=(int)$loan->denda-(int)$loan->denda_dibayar;
            if($loan->status!=='dikembalikan'||$paid<=0||$remaining<=0||$paid>$remaining)
                throw new DomainException('Jumlah pembayaran tidak valid atau denda sudah lunas.');
            $loan->denda_dibayar=(int)$loan->denda_dibayar+$paid;
            $loan->status_denda=$loan->denda_dibayar >= $loan->denda?'lunas':'belum_lunas';
            $loan->tanggal_bayar_denda=$loan->status_denda==='lunas'?today():null;
            $loan->save();return $loan->fresh(['user','buku']);
        },3);
    }
    public function deletePending(int $id):void {
        DB::transaction(function()use($id){
            $lookup=Peminjaman::findOrFail($id);
            Buku::whereKey($lookup->id_buku)->lockForUpdate()->firstOrFail();
            $loan=Peminjaman::whereKey($id)->lockForUpdate()->firstOrFail();
            if(!in_array($loan->status,['menunggu','ditolak','dibatalkan'],true))
                throw new DomainException('Riwayat peminjaman aktif/selesai tidak dapat dihapus.');
            $loan->delete();
        },3);
    }
}
