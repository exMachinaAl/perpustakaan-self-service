<?php
namespace App\Http\Controllers;
use App\Models\Peminjaman;
use App\Services\SecureLoanQr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ScanController extends Controller {
 public function index(){return view('admin.scan.index');}
 public function validasi($id){
  $peminjaman=Peminjaman::with(['user','buku'])->where('peminjaman_id',$id)->firstOrFail();
  return view('admin.scan.validasi',compact('peminjaman'));
 }
 public function verify(Request $request,SecureLoanQr $qr){
  $data=$request->validate(['payload'=>'required|string|max:128']);
  $loan=$qr->consume($data['payload'],$request->user()->id);
  if(!$loan){
   DB::table('scan_events')->insert(['actor_user_id'=>$request->user()->id,'origin'=>'qr','outcome'=>'invalid_or_replayed','ip_address'=>$request->ip(),'created_at'=>now(),'updated_at'=>now()]);
   return response()->json(['message'=>'QR tidak valid, telah digunakan atau kedaluwarsa.'],422);
  }
  return response()->json(['message'=>'QR valid. Periksa identitas dan buku sebelum menyerahkan.','url'=>route('admin.scan.validasi',$loan->peminjaman_id)]);
 }
}
