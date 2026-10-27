<?php

namespace App\Services;

use App\Models\Buku;
use DomainException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

final class BookInventory
{
    public function ensureCopies(Buku $book, ?int $target = null): void
    {
        $target ??= (int) $book->stock;
        $active = DB::table('library_kiosk_book_copies')
            ->where('book_id', $book->id_buku)->where('active', true)->count();
        $existing = DB::table('library_kiosk_book_copies')->where('book_id', $book->id_buku)->count();
        $toCreate = max(0, $target - $active);

        for ($offset = 1; $offset <= $toCreate; $offset++) {
            $sequence = $existing + $offset;
            $payload = 'LIBBOOK1:'.bin2hex(random_bytes(32));
            $code = sprintf('BK-%06d-%03d', $book->id_buku, $sequence);
            DB::table('library_kiosk_book_copies')->insert([
                'book_id' => $book->id_buku,
                'label' => $code,
                'inventory_code' => $code,
                'qr_hash' => hash('sha256', strtolower(substr($payload, 9))),
                'qr_encrypted' => Crypt::encryptString($payload),
                'active' => true,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function assignBarcodes(Buku $book, array $barcodes): void
    {
        $barcodes = array_values(array_filter(array_map([$this, 'normalize'], $barcodes)));
        if ($barcodes === []) {
            return;
        }

        if (count($barcodes) !== count(array_unique($barcodes))) {
            throw new DomainException('Barcode eksemplar tidak boleh duplikat.');
        }

        $copies = DB::table('library_kiosk_book_copies')->where('book_id', $book->id_buku)
            ->where('active', true)->orderBy('id')->get();
        if (count($barcodes) > $copies->count()) {
            throw new DomainException('Jumlah barcode melebihi jumlah eksemplar.');
        }

        foreach ($barcodes as $index => $barcode) {
            if (DB::table('library_kiosk_book_copies')->where('barcode', $barcode)
                ->where('id', '!=', $copies[$index]->id)->exists()) {
                throw new DomainException('Barcode '.$barcode.' sudah dipakai eksemplar lain.');
            }
            DB::table('library_kiosk_book_copies')->where('id', $copies[$index]->id)
                ->update(['barcode' => $barcode, 'updated_at' => now()]);
        }
    }

    public function createCopy(Buku $book, ?string $inventoryCode = null, ?string $barcode = null): object
    {
        return DB::transaction(function () use ($book, $inventoryCode, $barcode): object {
            $lockedBook = DB::table('buku')->where('id_buku', $book->id_buku)->lockForUpdate()->first();
            if (! $lockedBook) {
                throw new DomainException('Buku tidak ditemukan.');
            }

            $inventoryCode = $this->normalize($inventoryCode);
            $barcode = $this->normalize($barcode);
            if ($inventoryCode === '') {
                $inventoryCode = $this->nextInventoryCode((int) $book->id_buku);
            }
            $barcode = $barcode !== '' ? $barcode : null;

            $this->assertIdentifiersAvailable($inventoryCode, $barcode);
            $payload = 'LIBBOOK1:'.bin2hex(random_bytes(32));
            $id = DB::table('library_kiosk_book_copies')->insertGetId([
                'book_id' => $book->id_buku,
                'label' => $inventoryCode,
                'inventory_code' => $inventoryCode,
                'barcode' => $barcode,
                'qr_hash' => hash('sha256', strtolower(substr($payload, 9))),
                'qr_encrypted' => Crypt::encryptString($payload),
                'active' => true,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('buku')->where('id_buku', $book->id_buku)->increment('stock');

            return DB::table('library_kiosk_book_copies')->where('id', $id)->first();
        });
    }

    public function updateIdentifiers(Buku $book, int $copyId, string $inventoryCode, ?string $barcode): void
    {
        DB::transaction(function () use ($book, $copyId, $inventoryCode, $barcode): void {
            $copy = $this->copyForBook($book, $copyId, true);
            $inventoryCode = $this->normalize($inventoryCode);
            $barcode = $this->normalize($barcode);
            $barcode = $barcode !== '' ? $barcode : null;
            $this->assertIdentifiersAvailable($inventoryCode, $barcode, (int) $copy->id);

            DB::table('library_kiosk_book_copies')->where('id', $copy->id)->update([
                'label' => $inventoryCode,
                'inventory_code' => $inventoryCode,
                'barcode' => $barcode,
                'updated_at' => now(),
            ]);
        });
    }

    public function reissueQr(Buku $book, int $copyId): object
    {
        return DB::transaction(function () use ($book, $copyId): object {
            $copy = $this->copyForBook($book, $copyId, true);
            if (! $copy->active) {
                throw new DomainException('Aktifkan eksemplar sebelum menerbitkan ulang QR.');
            }

            $payload = 'LIBBOOK1:'.bin2hex(random_bytes(32));
            DB::table('library_kiosk_book_copies')->where('id', $copy->id)->update([
                'qr_hash' => hash('sha256', strtolower(substr($payload, 9))),
                'qr_encrypted' => Crypt::encryptString($payload),
                'updated_at' => now(),
            ]);

            return DB::table('library_kiosk_book_copies')->where('id', $copy->id)->first();
        });
    }

    public function setActive(Buku $book, int $copyId, bool $active): void
    {
        DB::transaction(function () use ($book, $copyId, $active): void {
            DB::table('buku')->where('id_buku', $book->id_buku)->lockForUpdate()->first();
            $copy = $this->copyForBook($book, $copyId, true);

            if ((bool) $copy->active === $active) {
                return;
            }
            if (! in_array($copy->status, ['available', 'inactive'], true)) {
                throw new DomainException('Eksemplar yang sedang dipinjam tidak dapat dinonaktifkan.');
            }

            DB::table('library_kiosk_book_copies')->where('id', $copy->id)->update([
                'active' => $active,
                'status' => $active ? 'available' : 'inactive',
                'updated_at' => now(),
            ]);
            $active
                ? DB::table('buku')->where('id_buku', $book->id_buku)->increment('stock')
                : DB::table('buku')->where('id_buku', $book->id_buku)->where('stock', '>', 0)->decrement('stock');
        });
    }

    public function payload(object $copy): string
    {
        return Crypt::decryptString($copy->qr_encrypted);
    }

    public function syncAvailable(Buku $book, int $desired): void
    {
        $available = DB::table('library_kiosk_book_copies')->where('book_id', $book->id_buku)
            ->where('active', true)->where('status', 'available')->count();
        if ($desired > $available) {
            $active = DB::table('library_kiosk_book_copies')->where('book_id', $book->id_buku)
                ->where('active', true)->count();
            $this->ensureCopies($book, $active + ($desired - $available));
        } elseif ($desired < $available) {
            $ids = DB::table('library_kiosk_book_copies')->where('book_id', $book->id_buku)
                ->where('active', true)->where('status', 'available')->orderByDesc('id')
                ->limit($available - $desired)->pluck('id');
            DB::table('library_kiosk_book_copies')->whereIn('id', $ids)
                ->update(['active' => false, 'status' => 'inactive', 'updated_at' => now()]);
        }
        DB::table('buku')->where('id_buku', $book->id_buku)->update(['stock' => $desired]);
        $book->stock = $desired;
    }

    public function normalize(?string $value): string
    {
        return mb_strtoupper(trim((string) $value));
    }

    public function normalizeIsbn(?string $value): string
    {
        return mb_strtoupper((string) preg_replace('/[^0-9X]/i', '', (string) $value));
    }

    private function nextInventoryCode(int $bookId): string
    {
        $sequence = DB::table('library_kiosk_book_copies')->where('book_id', $bookId)->count() + 1;
        do {
            $code = sprintf('BK-%06d-%03d', $bookId, $sequence++);
        } while (DB::table('library_kiosk_book_copies')->where('inventory_code', $code)->exists());

        return $code;
    }

    private function assertIdentifiersAvailable(string $inventoryCode, ?string $barcode, ?int $ignoreId = null): void
    {
        $inventoryQuery = DB::table('library_kiosk_book_copies')->where('inventory_code', $inventoryCode);
        if ($ignoreId) {
            $inventoryQuery->where('id', '!=', $ignoreId);
        }
        if ($inventoryQuery->exists()) {
            throw new DomainException('Kode inventaris sudah dipakai eksemplar lain.');
        }

        if ($barcode !== null) {
            $barcodeQuery = DB::table('library_kiosk_book_copies')->where('barcode', $barcode);
            if ($ignoreId) {
                $barcodeQuery->where('id', '!=', $ignoreId);
            }
            if ($barcodeQuery->exists()) {
                throw new DomainException('Barcode sudah dipakai eksemplar lain.');
            }
        }
    }

    private function copyForBook(Buku $book, int $copyId, bool $lock = false): object
    {
        $query = DB::table('library_kiosk_book_copies')
            ->where('id', $copyId)->where('book_id', $book->id_buku);
        if ($lock) {
            $query->lockForUpdate();
        }
        $copy = $query->first();
        if (! $copy) {
            throw new DomainException('Eksemplar buku tidak ditemukan.');
        }

        return $copy;
    }
}
