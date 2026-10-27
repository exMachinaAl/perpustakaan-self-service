<?php
namespace App\Http\Controllers;

use App\Services\KioskPilotReturn;
use DomainException;
use Illuminate\Http\Request;

final class KioskPilotReturnController extends Controller
{
    public function receive(Request $request, KioskPilotReturn $returner, int $loanId)
    {
        $validated = $request->validate([
            'physical_received' => ['required', 'accepted'],
            'condition' => ['nullable', 'string', 'max:255'],
        ]);
        try {
            $result = $returner->receive($loanId, (int) $request->user()->id,
                true, $validated['condition'] ?? null);
            return redirect()->route('admin.kiosk.pilot')->with('notice',
                ($result['already_done'] ? 'Sudah dikembalikan' : 'Buku fisik diterima, pengembalian tercatat') .
                ' — pinjaman #' . $result['loan_id']);
        } catch (DomainException $e) {
            return redirect()->route('admin.kiosk.pilot')->withErrors(['return' => $e->getMessage()]);
        }
    }
}
