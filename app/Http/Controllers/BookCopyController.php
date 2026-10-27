<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Buku;
use App\Services\BookInventory;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookCopyController extends Controller
{
    public function index(Buku $book)
    {
        $copies = DB::table('library_kiosk_book_copies')
            ->where('book_id', $book->id_buku)
            ->orderBy('inventory_code')
            ->get();
        $counts = [
            'total' => $copies->count(),
            'available' => $copies->where('active', true)->where('status', 'available')->count(),
            'borrowed' => $copies->where('active', true)->where('status', 'borrowed')->count(),
            'inactive' => $copies->where('active', false)->count(),
        ];

        return view('admin.databuku.inventory', compact('book', 'copies', 'counts'));
    }

    public function store(Request $request, Buku $book, BookInventory $inventory)
    {
        $data = $request->validate([
            'inventory_code' => ['nullable', 'string', 'max:80'],
            'barcode' => ['nullable', 'string', 'max:120'],
        ]);

        try {
            $copy = $inventory->createCopy($book, $data['inventory_code'] ?? null, $data['barcode'] ?? null);
            $this->audit('Tambah Eksemplar', 'Admin menambahkan eksemplar buku untuk self-service.', $book, $copy);

            return back()->with('success', "Eksemplar {$copy->inventory_code} berhasil ditambahkan dan QR dibuat otomatis.");
        } catch (DomainException $exception) {
            return back()->withErrors(['inventory_code' => $exception->getMessage()])->withInput();
        }
    }

    public function update(Request $request, Buku $book, int $copy, BookInventory $inventory)
    {
        $data = $request->validate([
            'inventory_code' => ['required', 'string', 'max:80'],
            'barcode' => ['nullable', 'string', 'max:120'],
        ]);

        try {
            $inventory->updateIdentifiers($book, $copy, $data['inventory_code'], $data['barcode'] ?? null);
            $updated = DB::table('library_kiosk_book_copies')->where('id', $copy)->first();
            $this->audit('Update Eksemplar', 'Admin memperbarui kode inventaris atau barcode eksemplar.', $book, $updated);

            return back()->with('success', 'Identitas eksemplar berhasil diperbarui.');
        } catch (DomainException $exception) {
            return back()->withErrors(['inventory_code' => $exception->getMessage()])->withInput();
        }
    }

    public function reissue(Buku $book, int $copy, BookInventory $inventory)
    {
        try {
            $updated = $inventory->reissueQr($book, $copy);
            $this->audit('Terbit Ulang QR Buku', 'Admin mengganti QR eksemplar; QR lama tidak berlaku.', $book, $updated);

            return back()->with('success', 'QR baru berhasil diterbitkan. Label QR lama sudah tidak berlaku.');
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function toggle(Request $request, Buku $book, int $copy, BookInventory $inventory)
    {
        $data = $request->validate(['active' => ['required', 'boolean']]);

        try {
            $inventory->setActive($book, $copy, (bool) $data['active']);
            $updated = DB::table('library_kiosk_book_copies')->where('id', $copy)->first();
            $this->audit('Status Eksemplar', 'Admin mengubah status aktif eksemplar.', $book, $updated);

            return back()->with('success', $data['active'] ? 'Eksemplar berhasil diaktifkan.' : 'Eksemplar berhasil dinonaktifkan.');
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function print(Request $request, Buku $book, BookInventory $inventory)
    {
        $data = $request->validate([
            'copy_ids' => ['nullable', 'array', 'max:100'],
            'copy_ids.*' => ['integer', 'distinct'],
        ]);
        $copies = DB::table('library_kiosk_book_copies')
            ->where('book_id', $book->id_buku)
            ->where('active', true)
            ->when($data['copy_ids'] ?? [], fn ($query, $ids) => $query->whereIn('id', $ids))
            ->orderBy('inventory_code')
            ->get();
        abort_if($copies->isEmpty(), 404, 'Tidak ada eksemplar aktif untuk dicetak.');
        foreach ($copies as $copy) {
            $copy->qr_payload = $inventory->payload($copy);
        }

        return response()->view('admin.databuku.inventory-print', compact('book', 'copies'))
            ->header('Cache-Control', 'private, no-store');
    }

    private function audit(string $activity, string $description, Buku $book, ?object $copy): void
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'nama_user' => Auth::user()->nama ?? 'Admin',
                'role' => 'admin',
                'aktivitas' => $activity,
                'keterangan' => $description,
                'data' => [
                    'book_id' => $book->id_buku,
                    'copy_id' => $copy->id ?? null,
                    'inventory_code' => $copy->inventory_code ?? null,
                    'barcode' => $copy->barcode ?? null,
                    'active' => $copy->active ?? null,
                    'ip_address' => request()->ip(),
                ],
                'waktu' => now(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Audit eksemplar gagal disimpan.', ['message' => $exception->getMessage()]);
        }
    }
}
