<?php
namespace App\Http\Controllers;
use App\Services\KioskQrResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class KioskOmnichannelController extends Controller {
    // A public kiosk must NEVER keep an administrator logged in. This station is LAB only.
    private function onlyLab():void {
        abort_if(app()->environment('production'),403,'PATCH 004 test kiosk is disabled in production until kiosk-scoped authentication is built.');
    }
    public function index(Request $request) {
        $this->onlyLab();
        $readers=DB::table('reader_devices')->where('active',1)->orderBy('name')->get(['id','name','serial']);
        $books=DB::table('buku')->orderBy('judul')->limit(150)->get(['id_buku','judul','stock']);
        $members=DB::table('users')->where('role','user')->where('status','aktif')
            ->orderBy('nama')->limit(120)->get(['id','nama']);
        $copies=DB::table('library_kiosk_book_copies as c')->join('buku as b','c.book_id','=','b.id_buku')
            ->orderByDesc('c.id')->limit(24)->get(['c.id','c.label','c.qr_encrypted','b.judul']);
        foreach($copies as $copy) $copy->qr_value=Crypt::decryptString($copy->qr_encrypted);
        // PATCH 005B: status kesiapan LAB, read-only terhadap transaksi/persediaan.
        $readiness=$this->labStats();
        $pair=$this->newPair($request->user()->id);
        $qr=$pair['qr'];
        $pairUrl=$pair['pair_url'];
        $phoneBaseUrl=(string)config('kiosk.phone_base_url', config('app.url'));
        return response()->view('admin.kiosk.hub',compact('readers','books','copies','members','qr','pairUrl','phoneBaseUrl','readiness'))
            ->header('Cache-Control','private, no-store');
    }
    // PATCH 005B — laporan ringkas yang hanya MEMBACA data, tidak membuat pinjaman.
    private function labStats(): array {
        return [
            'active_members'=>(int) DB::table('users')->where('role','user')->where('status','aktif')->count(),
            'all_members'=>(int) DB::table('users')->where('role','user')->count(),
            'catalog_books'=>(int) DB::table('buku')->count(),
            'copies'=>(int) DB::table('library_kiosk_book_copies')->where('active',1)->count(),
            'loans'=>(int) DB::table('peminjaman')->count(),
            'stock_total'=>(int) DB::table('buku')->sum('stock'),
        ];
    }
    public function labHealth(Request $request) {
        $this->onlyLab();
        return response()->json(['ok'=>true,'dry_run'=>true,'stats'=>$this->labStats()])
            ->header('Cache-Control','private, no-store');
    }
    // Sementara hanya untuk admin di APP_ENV local/staging; akun siswa asli harus aktif.
    // Tidak membuat user palsu, tidak membuat peminjaman atau mengubah stok.
    public function debugMemberPass(Request $request) {
        $this->onlyLab();
        $data=$request->validate(['member_id'=>'required|integer|min:1']);
        $user=DB::table('users')->where('id',$data['member_id'])
            ->where('role','user')->where('status','aktif')->first(['id','nama']);
        if (!$user) return response()->json(['message'=>'Anggota aktif tidak ditemukan. Buat/aktifkan anggota di menu Data Anggota.'],422);
        $raw=bin2hex(random_bytes(32));
        DB::transaction(function()use($raw,$user){
            DB::table('users')->where('id',$user->id)->lockForUpdate()->first();
            DB::table('library_kiosk_member_passes')->updateOrInsert(['user_id'=>$user->id],
                ['token_hash'=>hash('sha256',$raw),'expires_at'=>now()->addSeconds(90),
                 'consumed_at'=>null,'updated_at'=>now()]);
        },3);
        $svg=QrCode::format('svg')->size(220)->margin(2)->generate('LIBMEM1:'.$raw);
        return response()->json(['svg'=>$svg,'member_label'=>strtok($user->nama,' '),
            'expires_seconds'=>90,'dry_run'=>true])->header('Cache-Control','private, no-store');
    }
    private function newPair(int $adminId):array {
        $raw=bin2hex(random_bytes(32));
        DB::table('library_kiosk_pairs')->insert(['admin_id'=>$adminId,'token_hash'=>hash('sha256',$raw),
            'expires_at'=>now()->addMinutes(2),'created_at'=>now(),'updated_at'=>now()]);
        $origin=rtrim((string)config('kiosk.phone_base_url', config('app.url')),'/');
        // Only accept a real http(s) origin. Never embed credentials in the URL.
        if (!filter_var($origin,FILTER_VALIDATE_URL) || !in_array(parse_url($origin,PHP_URL_SCHEME),['http','https'],true)) {
            $origin=rtrim((string)config('app.url'),'/');
        }
        $url=$origin.route('user.kiosk.connect',$raw,false);
        return ['qr'=>QrCode::format('svg')->size(192)->margin(1)->generate($url), 'pair_url'=>$url];
    }
    public function newPairAjax(Request $request) {
        $this->onlyLab();
        $pair=$this->newPair((int)$request->user()->id);
        return response()->json(['svg'=>$pair['qr'],'pair_url'=>$pair['pair_url']])->header('Cache-Control','no-store');
    }
    public function inspect(Request $request,KioskQrResolver $resolver) {
        $this->onlyLab();
        $data=$request->validate(['payload'=>'required|string|max:500', 'channel'=>'required|in:webcam,usb,debug']);
        $resolved=$resolver->resolve($data['payload']);
        $id=$resolver->log($resolved,$data['channel'],(int)$request->user()->id);
        return response()->json(['event_id'=>$id,'result'=>$resolved,'dry_run'=>true])->header('Cache-Control','no-store');
    }
    public function events(Request $request) {
        $this->onlyLab();
        $data=$request->validate(['after_id'=>'nullable|integer|min:0','reader_id'=>'nullable|integer|min:1']);
        $q=DB::table('library_kiosk_events as e')
            ->leftJoin('users as u','e.member_id','=','u.id')
            ->leftJoin('buku as b','e.book_id','=','b.id_buku')
            ->where('e.created_at','>=',now()->subMinutes(30))
            ->where(function($q)use($request,$data){
                $q->where('e.admin_id',(int)$request->user()->id);
                if(!empty($data['reader_id']))$q->orWhere('e.reader_device_id',(int)$data['reader_id']);
            })->where('e.id','>',(int)($data['after_id']??0));
        $rows=$q->orderBy('e.id')->limit(30)->get(['e.id','e.channel','e.kind','e.outcome','e.member_id','e.book_id','e.copy_id','u.nama','b.judul']);
        return response()->json(['events'=>$rows->map(fn($r)=>[
            'id'=>$r->id,'channel'=>$r->channel,'kind'=>$r->kind,'outcome'=>$r->outcome,
            'member_id'=>$r->member_id,'book_id'=>$r->book_id,'copy_id'=>$r->copy_id,
            'label'=>$r->kind==='member'&&$r->nama?strtok($r->nama,' '):($r->judul?:$r->outcome),
        ])])->header('Cache-Control','private, no-store');
    }
    // PATCH 005B: registrasi eksemplar bekerja meskipun JavaScript tidak dimuat.
    public function createCopyFromForm(Request $request) {
        $data=$request->validate(['book_id'=>'required|integer|min:1|exists:buku,id_buku']);
        return $this->createCopy($request,(int)$data['book_id']);
    }
    public function createCopy(Request $request,int $bookId) {
        $this->onlyLab();
        $data=$request->validate(['label'=>['required','string','max:80', \Illuminate\Validation\Rule::unique('library_kiosk_book_copies','label')->where('book_id',$bookId)]]);
        $book=DB::table('buku')->where('id_buku',$bookId)->first(['id_buku','judul']);
        if (!$book) return back()->withErrors(['book'=>'Judul buku tidak ditemukan. Buat katalog buku terlebih dahulu.'])->withInput();
        $raw=bin2hex(random_bytes(32));
        $copyId=DB::table('library_kiosk_book_copies')->insertGetId(['book_id'=>$bookId,'label'=>$data['label'],
            'qr_hash'=>hash('sha256',$raw),'qr_encrypted'=>Crypt::encryptString('LIBBOOK1:'.$raw),
            'created_at'=>now(),'updated_at'=>now()]);
        return redirect()->route('admin.kiosk.hub')
            ->with('success','QR eksemplar '.$data['label'].' dibuat untuk '.$book->judul.'. Cetak QR di bagian bawah.')
            ->with('new_copy_id',$copyId);
    }
    // PATCH 005: authorized debug-only simulation; never issues a credential or changes a loan.
    public function simulate(Request $request,KioskQrResolver $resolver) {
        $this->onlyLab();
        $data=$request->validate([
            'kind'=>'required|in:member,book',
            'member_id'=>'nullable|integer|min:1',
            'copy_id'=>'nullable|integer|min:1',
        ]);
        if ($data['kind']==='member') {
            $m=DB::table('users')->where('id',$data['member_id']??0)
                ->where('role','user')->where('status','aktif')->first(['id','nama']);
            if (!$m) return response()->json(['message'=>'Siswa aktif tidak ditemukan.'],422);
            $result=['kind'=>'member','outcome'=>'identified','member_id'=>(int)$m->id,'label'=>strtok($m->nama,' ')];
        } else {
            $c=DB::table('library_kiosk_book_copies as c')
                ->join('buku as b','b.id_buku','=','c.book_id')
                ->where('c.id',$data['copy_id']??0)->where('c.active',1)
                ->first(['c.id','c.book_id','c.label','b.judul']);
            if (!$c) return response()->json(['message'=>'Buat QR eksemplar buku terlebih dahulu.'],422);
            $result=['kind'=>'book','outcome'=>'identified','book_id'=>(int)$c->book_id,
                'copy_id'=>(int)$c->id,'label'=>$c->judul.' / '.$c->label];
        }
        $eventId=$resolver->log($result,'debug',(int)$request->user()->id);
        return response()->json(['result'=>$result,'event_id'=>$eventId,'dry_run'=>true])->header('Cache-Control','no-store');
    }
    public function memberPass(Request $request) {
        $raw=bin2hex(random_bytes(32));$uid=(int)$request->user()->id;
        DB::transaction(function()use($raw,$uid){
            DB::table('users')->where('id',$uid)->lockForUpdate()->first();
            DB::table('library_kiosk_member_passes')->updateOrInsert(['user_id'=>$uid],
                ['token_hash'=>hash('sha256',$raw),'expires_at'=>now()->addSeconds(90),
                 'consumed_at'=>null,'created_at'=>now(),'updated_at'=>now()]);
        },3);
        $qr=QrCode::format('svg')->size(260)->margin(2)->generate('LIBMEM1:'.$raw);
        return response()->view('user.kiosk.member-pass',compact('qr'))->header('Cache-Control','no-store');
    }
    public function connect(Request $request,string $token) {
        abort_unless((bool)preg_match('/\A[a-f0-9]{64}\z/D',$token),404);
        return response()->view('user.kiosk.connect',compact('token'))->header('Cache-Control','no-store');
    }
    public function connectSubmit(Request $request,string $token) {
        abort_unless((bool)preg_match('/\A[a-f0-9]{64}\z/D',$token),404);
        $data=DB::transaction(function()use($token,$request){
            $p=DB::table('library_kiosk_pairs')->where('token_hash',hash('sha256',$token))->lockForUpdate()->first();
            if(!$p || $p->used_at || \Carbon\Carbon::parse($p->expires_at)->isPast()) return null;
            DB::table('library_kiosk_pairs')->where('id',$p->id)->update(['member_id'=>$request->user()->id,'used_at'=>now(),'updated_at'=>now()]);
            return $p;
        },3);
        if(!$data)return response()->view('user.kiosk.expired')->setStatusCode(410);
        $request->session()->put('kiosk_pair_id',$data->id);
        (new KioskQrResolver)->log(['kind'=>'member','outcome'=>'identified','member_id'=>(int)$request->user()->id], 'phone',(int)$data->admin_id);
        return redirect()->route('user.kiosk.phone');
    }
    public function phone(Request $request) {
        $pair=DB::table('library_kiosk_pairs')->where('id',$request->session()->get('kiosk_pair_id',0))
            ->where('member_id',$request->user()->id)->where('used_at','>=',now()->subMinutes(5))->first();
        if(!$pair)return redirect()->route('user.dashboard')->with('error','Sesi kiosk tidak tersedia. Scan QR kiosk terbaru.');
        return response()->view('user.kiosk.phone')->header('Cache-Control','no-store');
    }
    public function phoneScan(Request $request,KioskQrResolver $resolver) {
        $data=$request->validate(['payload'=>'required|string|max:500']);
        $pair=DB::table('library_kiosk_pairs')->where('id',$request->session()->get('kiosk_pair_id',0))
            ->where('member_id',$request->user()->id)->where('used_at','>=',now()->subMinutes(5))->first();
        abort_unless($pair,403,'Sesi kiosk kedaluwarsa.');
        $resolved=$resolver->resolve($data['payload'],false);
        if($resolved['kind']!=='book')return response()->json(['message'=>'Gunakan QR stiker buku LIBBOOK1.'],422);
        $resolver->log($resolved,'phone',(int)$pair->admin_id);
        $request->session()->forget('kiosk_pair_id');
        return response()->json(['result'=>$resolved,'dry_run'=>true]);
    }
    public function deviceQr(Request $request,KioskQrResolver $resolver) {
        $data=$request->validate(['payload'=>'required|string|max:500']);
        $device=$request->attributes->get('verified_reader');
        $result=$resolver->resolve($data['payload']);
        $resolver->log($result,'esp32cam',null,$device->id);
        return response()->json(['accepted'=>true,'kind'=>$result['kind'],'outcome'=>$result['outcome'],'dry_run'=>true],202)
            ->header('Cache-Control','no-store');
    }
}
