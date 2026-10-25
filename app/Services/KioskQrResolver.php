<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
class KioskQrResolver {
    /** Resolves input without leaking QR credentials into logs. No loan or stock mutation. */
    public function resolve(string $payload, bool $consumeMember = true): array {
        $payload = trim($payload);
        if (strlen($payload)>500) return ['kind'=>'unknown','outcome'=>'too_long'];
        if (preg_match('/\ALIBMEM1:([a-f0-9]{64})\z/iD',$payload,$m)) {
            $digest=hash('sha256',strtolower($m[1]));
            return DB::transaction(function()use($digest,$consumeMember){
                $query=DB::table('library_kiosk_member_passes')->where('token_hash',$digest);
                if ($consumeMember) $query->lockForUpdate();
                $row=$query->first();
                if (!$row || $row->consumed_at || \Carbon\Carbon::parse($row->expires_at)->isPast())
                    return ['kind'=>'member','outcome'=>'expired_or_used'];
                $member=DB::table('users')->where('id',$row->user_id)->where('role','user')->where('status','aktif')->first(['id','nama']);
                if (!$member) return ['kind'=>'member','outcome'=>'inactive'];
                if ($consumeMember) DB::table('library_kiosk_member_passes')->where('id',$row->id)->update(['consumed_at'=>now(),'updated_at'=>now()]);
                return ['kind'=>'member','outcome'=>'identified','member_id'=>(int)$member->id,
                        'label'=>strtok($member->nama,' ')];
            },3);
        }
        if (preg_match('/\ALIBBOOK1:([a-f0-9]{64})\z/iD',$payload,$m)) {
            $row=DB::table('library_kiosk_book_copies as c')->join('buku as b','b.id_buku','=','c.book_id')
                ->where('c.qr_hash',hash('sha256',strtolower($m[1])))->where('c.active',1)
                ->first(['c.id','c.book_id','c.label','b.judul']);
            if (!$row)return ['kind'=>'book','outcome'=>'not_registered'];
            return ['kind'=>'book','outcome'=>'identified','book_id'=>(int)$row->book_id,
                    'copy_id'=>(int)$row->id,'label'=>$row->judul.' / '.$row->label];
        }
        if (str_starts_with($payload,'LIBQR1:')) return ['kind'=>'legacy','outcome'=>'loan_proof_not_book'];
        return ['kind'=>'unknown','outcome'=>'unsupported_qr'];
    }
    public function log(array $resolved,string $source,?int $adminId=null,?int $readerId=null):int {
        return (int) DB::table('library_kiosk_events')->insertGetId([
            'admin_id'=>$adminId,'reader_device_id'=>$readerId,'channel'=>$source,
            'kind'=>$resolved['kind'],'outcome'=>$resolved['outcome'],
            'member_id'=>$resolved['member_id']??null,'book_id'=>$resolved['book_id']??null,
            'copy_id'=>$resolved['copy_id']??null,'created_at'=>now(),
        ]);
    }
}
