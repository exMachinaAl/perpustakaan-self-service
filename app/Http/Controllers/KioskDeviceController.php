<?php
namespace App\Http\Controllers;

use App\Services\KioskStationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Browser kiosk credential is separate from admin/student authentication. */
class KioskDeviceController extends Controller {
    private const COOKIE='perpus_kiosk_device';

    private function https(Request $request): void {
        abort_unless($request->isSecure(), 426, 'Kiosk memerlukan HTTPS tepercaya. Gunakan Caddy/TLS yang telah diuji.');
    }
    private function station(Request $r) {
        $raw=(string)$r->cookie(self::COOKIE, '');
        if (!preg_match('/\A[a-f0-9]{64}\z/D',$raw)) return null;
        return DB::table('library_kiosk_devices')->where('credential_hash',hash('sha256',$raw))
            ->where('active',1)->first();
    }
    private function requireStation(Request $r) {
        $this->https($r);
        $d=$this->station($r);
        abort_unless($d,401,'Perangkat belum terdaftar atau sudah dicabut. Buka /kiosk/setup.');
        return $d;
    }
    public function devices(Request $r) {
        $rows=DB::table('library_kiosk_devices')->orderByDesc('id')->limit(100)->get();
        return response()->view('admin.kiosk.devices',['devices'=>$rows])->header('Cache-Control','private, no-store');
    }
    public function create(Request $r) {
        $v=$r->validate(['name'=>'required|string|min:3|max:100']);
        $raw=strtoupper(bin2hex(random_bytes(16)));
        $id=DB::table('library_kiosk_devices')->insertGetId([
            'name'=>$v['name'],'created_by'=>$r->user()->id,'enroll_code_hash'=>hash('sha256',strtolower($raw)),
            'enroll_expires_at'=>now()->addMinutes(10),'active'=>false,'created_at'=>now(),'updated_at'=>now()]);
        return redirect()->route('admin.kiosk.devices')->with('new_enroll_code',$raw)->with('new_device_id',$id);
    }
    public function reissue(Request $r,int $id) {
        $raw=strtoupper(bin2hex(random_bytes(16)));
        $count=DB::table('library_kiosk_devices')->where('id',$id)->update([
            'credential_hash'=>null,'active'=>false,'enrolled_at'=>null,
            'enroll_code_hash'=>hash('sha256',strtolower($raw)),
            'enroll_expires_at'=>now()->addMinutes(10),'updated_at'=>now()]);
        abort_unless($count,404);
        DB::table('library_kiosk_device_sessions')->where('device_id',$id)->delete();
        return redirect()->route('admin.kiosk.devices')->with('new_enroll_code',$raw)->with('new_device_id',$id);
    }
    public function revoke(Request $r,int $id) {
        $count=DB::table('library_kiosk_devices')->where('id',$id)->update([
            'active'=>false,'credential_hash'=>null,'enroll_code_hash'=>null,
            'enroll_expires_at'=>null,'updated_at'=>now()]);
        abort_unless($count,404);
        DB::table('library_kiosk_device_sessions')->where('device_id',$id)->delete();
        return back()->with('notice','Perangkat telah dicabut; cookie kiosk tidak berlaku lagi.');
    }
    public function setup(Request $r) {
        $this->https($r);
        if ($this->station($r)) return redirect()->route('kiosk.station');
        return response()->view('kiosk.setup')->header('Cache-Control','no-store');
    }
    public function enroll(Request $r) {
        $this->https($r);
        $v=$r->validate(['enrollment_code'=>['required','string','regex:/\A[a-fA-F0-9]{32}\z/D']]);
        $digest=hash('sha256',strtolower($v['enrollment_code']));
        $credential=bin2hex(random_bytes(32));
        $ok=DB::transaction(function()use($digest,$credential){
            $dev=DB::table('library_kiosk_devices')->where('enroll_code_hash',$digest)->lockForUpdate()->first();
            if (!$dev || !$dev->enroll_expires_at || now()->greaterThan($dev->enroll_expires_at)) return false;
            DB::table('library_kiosk_devices')->where('id',$dev->id)->update([
                'enroll_code_hash'=>null,'enroll_expires_at'=>null,
                'credential_hash'=>hash('sha256',$credential),'active'=>true,
                'enrolled_at'=>now(),'last_seen_at'=>now(),'updated_at'=>now()]);
            return true;
        },3);
        if (!$ok) return back()->withErrors(['enrollment_code'=>'Kode salah, sudah digunakan, atau melewati 10 menit.'])->withInput();
        $cookie=cookie(self::COOKIE,$credential,60*24*30,'/kiosk',null,true,true,false,'strict');
        return redirect()->route('kiosk.station')->withCookie($cookie);
    }
    public function stationPage(Request $r) {
        $this->https($r);
        $dev=$this->station($r);
        if (!$dev) return redirect()->route('kiosk.setup');
        return response()->view('kiosk.station',['device'=>$dev])->header('Cache-Control','private, no-store');
    }
    public function scan(Request $r,KioskStationService $svc) {
        $dev=$this->requireStation($r);
        $v=$r->validate(['payload'=>'required|string|max:500','channel'=>'required|in:webcam,usb,photo']);
        DB::table('library_kiosk_devices')->where('id',$dev->id)->update(['last_seen_at'=>now()]);
        return response()->json($svc->scan((int)$dev->id,$v['payload'],$v['channel']))->header('Cache-Control','no-store');
    }
    public function reset(Request $r,KioskStationService $svc) {
        $dev=$this->requireStation($r);
        return response()->json($svc->reset((int)$dev->id))->header('Cache-Control','no-store');
    }
    public function status(Request $r,KioskStationService $svc) {
        $dev=$this->requireStation($r);
        return response()->json($svc->status((int)$dev->id))->header('Cache-Control','no-store');
    }
}
