<?php
namespace App\Http\Controllers;

use App\Services\KioskCheckoutPilot;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class KioskPilotController extends Controller
{
    public function index(KioskCheckoutPilot $pilot)
    {
        $devices = DB::table('library_kiosk_devices as d')
            ->leftJoin('library_kiosk_device_sessions as s', 's.device_id', '=', 'd.id')
            ->leftJoin('users as u', 'u.id', '=', 's.member_id')
            ->leftJoin('library_kiosk_book_copies as c', 'c.id', '=', 's.copy_id')
            ->leftJoin('buku as b', 'b.id_buku', '=', 'c.book_id')
            ->orderBy('d.id')->select('d.id', 'd.name', 'd.active', 's.state', 's.expires_at',
                's.validated_event_id', 's.checkout_loan_id', 's.checkout_due_date',
                'u.nama as member_name', 'b.judul as book_title', 'c.label as copy_label')->limit(100)->get();
        $checkouts = DB::table('library_kiosk_pilot_checkouts as p')
            ->join('library_kiosk_devices as d', 'd.id', '=', 'p.device_id')
            ->orderByDesc('p.id')->select('d.name', 'p.loan_id', 'p.due_date', 'p.created_at')
            ->limit(20)->get();
        return response()->view('admin.kiosk.pilot', [
            'devices' => $devices, 'checkouts' => $checkouts, 'pilotEnabled' => $pilot->enabled(),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function approve(Request $request, KioskCheckoutPilot $pilot, int $deviceId)
    {
        try {
            $result = $pilot->approve($deviceId, (int) $request->user()->id);
            return redirect()->route('admin.kiosk.pilot')->with('notice',
                ($result['already_done'] ? 'Transaksi sudah tercatat' : 'Peminjaman berhasil')
                . ' #' . $result['loan_id'] . ' — batas kembali ' . $result['due_date']);
        } catch (DomainException $error) {
            return redirect()->route('admin.kiosk.pilot')->withErrors(['checkout' => $error->getMessage()]);
        }
    }
}
