<?php
namespace App\Http\Controllers;
use App\Services\KioskCirculation;
use Illuminate\Http\Request;
class KioskCirculationController extends Controller {
    private function labOnly(): void {
        abort_if(app()->environment('production'),403,'Kiosk berbasis akun admin belum aman untuk produksi tanpa autentikasi kiosk tersendiri.');
    }
    public function scan(Request $r,KioskCirculation $service) {
        $this->labOnly();
        $data=$r->validate(['payload'=>'required|string|max:500','channel'=>'required|in:webcam,usb,photo']);
        return response()->json($service->scan((int)$r->user()->id,$data['payload'],$data['channel']))
            ->header('Cache-Control','private, no-store');
    }
    public function reset(Request $r,KioskCirculation $service) {
        $this->labOnly();
        return response()->json($service->reset((int)$r->user()->id))->header('Cache-Control','private, no-store');
    }
    public function status(Request $r,KioskCirculation $service) {
        $this->labOnly();
        return response()->json($service->status((int)$r->user()->id))->header('Cache-Control','private, no-store');
    }
}
