<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/** Validates real QR payloads on a *device-scoped* MySQL session.
 * No loans, stock, or returns are mutated by this service.
 */
final class KioskStationService {
    public function __construct(private readonly KioskQrResolver $resolver) {}

    private function reply(string $state, $row, string $message): array {
        $member=null; $book=null;
        if ($row && $row->member_id) {
            $u=DB::table('users')->where('id',$row->member_id)->first(['nama']);
            $member=$u ? strtok($u->nama,' ') : null;
        }
        if ($row && $row->copy_id) {
            $b=DB::table('library_kiosk_book_copies as c')->join('buku as b','b.id_buku','=','c.book_id')
                ->where('c.id',$row->copy_id)->first(['b.judul','c.label']);
            $book=$b ? $b->judul.' — '.$b->label : null;
        }
        return ['state'=>$state,'member'=>$member,'book'=>$book,'message'=>$message,
            'validated_only'=>true,'loan_created'=>false,'return_created'=>false];
    }
    private function fresh(int $deviceId): object {
        DB::table('library_kiosk_device_sessions')->updateOrInsert(['device_id'=>$deviceId],
            ['member_id'=>null,'copy_id'=>null,'state'=>'ready','expires_at'=>now()->addSeconds(45),
            'updated_at'=>now()]);
        return DB::table('library_kiosk_device_sessions')->where('device_id',$deviceId)->lockForUpdate()->first();
    }
    public function reset(int $deviceId): array {
        return DB::transaction(function() use($deviceId){
            DB::table('library_kiosk_devices')->where('id',$deviceId)->lockForUpdate()->first();
            $row=$this->fresh($deviceId);
            return $this->reply('ready',$row,'Sesi dihapus. Silakan scan QR anggota atau buku.');
        },3);
    }
    public function status(int $deviceId): array {
        $r=DB::table('library_kiosk_device_sessions')->where('device_id',$deviceId)->first();
        if (!$r || ($r->expires_at && Carbon::parse($r->expires_at)->isPast()))
            return $this->reply('ready',null,'Menunggu QR anggota atau QR buku.');
        return $this->reply($r->state,$r,'Sesi aktif.');
    }
    public function scan(int $deviceId, string $payload, string $channel): array {
        return DB::transaction(function() use($deviceId,$payload,$channel){
            $dev=DB::table('library_kiosk_devices')->where('id',$deviceId)->lockForUpdate()->first();
            if (!$dev || !$dev->active) return $this->reply('rejected',null,'Perangkat dinonaktifkan.');
            $r=DB::table('library_kiosk_device_sessions')->where('device_id',$deviceId)->lockForUpdate()->first();
            if (!$r || ($r->expires_at && Carbon::parse($r->expires_at)->isPast())) $r=$this->fresh($deviceId);
            if ($r->state==='validated') return $this->reply('validated',$r,'QR tervalidasi; reset sesi sebelum pengguna selanjutnya.');
            $resolved=$this->resolver->resolve($payload,true);
            // Do not store the raw QR payload in any database event.
            DB::table('library_kiosk_device_events')->insert(['device_id'=>$deviceId,
                'kind'=>$resolved['kind']??'unknown','outcome'=>$resolved['outcome']??'invalid',
                'member_id'=>$resolved['member_id']??null,'copy_id'=>$resolved['copy_id']??null,'created_at'=>now()]);
            if (($resolved['outcome']??null)!=='identified' || !in_array($resolved['kind']??null,['member','book'],true))
                return $this->reply('rejected',$r,'QR tidak valid, kedaluwarsa, sudah dipakai, atau bukan QR anggota/buku.');
            if ($resolved['kind']==='member') {
                if ($r->member_id && (int)$r->member_id!==(int)$resolved['member_id'])
                    return $this->reply('rejected',$r,'Anggota berbeda terdeteksi. Reset sesi.');
                $memberId=(int)$resolved['member_id']; $copyId=$r->copy_id;
            } else {
                if ($r->copy_id && (int)$r->copy_id!==(int)$resolved['copy_id'])
                    return $this->reply('rejected',$r,'Buku berbeda terdeteksi. Reset sesi.');
                $memberId=$r->member_id; $copyId=(int)$resolved['copy_id'];
            }
            $state=$memberId && $copyId ? 'validated' : ($memberId ? 'waiting_book' : 'waiting_member');
            if ($state==='validated') {
                // Only validate that the copy is available, without writing a loan.
                $book=DB::table('library_kiosk_book_copies as c')->join('buku as b','b.id_buku','=','c.book_id')
                    ->where('c.id',$copyId)->where('c.active',1)->first(['b.stock','b.id_buku']);
                $member=DB::table('users')->where('id',$memberId)->where('role','user')->where('status','aktif')->exists();
                if (!$book || !$member || (int)$book->stock<=0)
                    return $this->reply('rejected',$r,'Anggota atau eksemplar tidak tersedia.');
            }
            DB::table('library_kiosk_device_sessions')->where('device_id',$deviceId)->update([
                'member_id'=>$memberId,'copy_id'=>$copyId,'state'=>$state,
                'expires_at'=>now()->addSeconds(45),'updated_at'=>now()]);
            $r=DB::table('library_kiosk_device_sessions')->where('device_id',$deviceId)->first();
            $msg=match($state){
                'validated'=>'QR ASLI TERVALIDASI. Belum membuat transaksi peminjaman.',
                'waiting_member'=>'Buku dikenali. Silakan scan QR anggota.',
                default=>'Anggota dikenali. Silakan scan QR buku.'
            };
            return $this->reply($state,$r,$msg);
        },3);
    }
}
