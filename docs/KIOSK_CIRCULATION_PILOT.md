# Perpustakaan — Circulation Engine / Pilot Petugas (PATCH 009)

## Ringkasan status
- **Validasi QR kios publik tetap berjalan** (`/kiosk`) dengan credential perangkat dari PATCH 007.
- Peminjaman **nyata** tersedia di panel admin `/admin/kiosk/pilot`, tetapi **MATI secara default**.
- Pilot hanya dapat dijalankan di `APP_ENV=local` atau `staging` (ditolak pada `production`), setelah admin mengaktifkan `KIOSK_PILOT_CHECKOUT=true` secara sadar.
- Tidak ada pengembalian otomatis, keranjang multi-buku, maupun auto-loan unattended pada versi ini.

## Prasyarat
- Full Secure + PATCH 001–007, MySQL berjalan, `vendor/` tersedia, APP_KEY lama tetap dipertahankan.
- PATCH 008 toolkit dianjurkan untuk `START.bat`, Caddy, `CHECK_SYSTEM.bat`.
- HTTPS tepercaya berfungsi di browser kiosk, perangkat sudah diaktivasi melalui `/kiosk/setup`.
- Minimal satu siswa aktif dengan QR anggota dinamis, satu eksemplar QR buku terdaftar.

## Langkah pengujian TANPA menulis pinjaman
1. Jalankan sistem lewat `tools/runtime/START.bat` jika PATCH 008 sudah terpasang.
2. Buka admin `/admin/kiosk/pilot`. Pastikan status **VALIDASI SAJA**.
3. Pada browser kiosk teraktivasi buka `/kiosk`; scan QR siswa, lalu QR buku. Pastikan status **TERVALIDASI**.
4. Pada admin `/admin/kiosk/pilot` refresh; pasangan siswa-buku seharusnya muncul, tetapi tombol **Konfirmasi Pinjam** tidak ada.
5. Reset kiosk, ulangi dengan urutan buku lalu anggota. Pastikan stok di katalog dan tabel `peminjaman` **tidak berubah**.

## Pilot nyata dengan PETUGAS (jangan pada database produksi)
1. Backup MySQL secara terpisah; gunakan database staging/salinan untuk pengujian.
2. Edit `.env`: set `KIOSK_PILOT_CHECKOUT=true` dan `APP_ENV=local`/`staging`.
3. Jalankan `php artisan config:clear`. Jangan mengubah APP_KEY.
4. Login admin di **perangkat browser berbeda**, buka `/admin/kiosk/pilot`.
5. Scan QR siswa dan QR eksemplar pada kiosk; minta petugas mencocokkan buku fisik.
6. Sebelum 45 detik, pada panel admin klik **Konfirmasi Pinjam** dan setujui dialog konfirmasi.
7. Kiosk menampilkan **PEMINJAMAN BERHASIL** (polling maksimal sekitar 5 detik). ID pinjaman dan tenggat 7 hari tercatat.
8. Periksa tabel `peminjaman`: hanya SATU baris baru berstatus `dipinjam`; stok judul berkurang SATU.
9. Klik tombol browser ulang (resubmit) / refresh panel, pastikan tidak ada pinjaman ganda.
10. Nonaktifkan lagi: `KIOSK_PILOT_CHECKOUT=false`; `php artisan config:clear`.

## Tes negatif wajib
- Kode anggota yang sama dipakai ulang → ditolak.
- QR stiker buku yang sudah dipinjam → ditolak untuk pinjaman aktif lain.
- Perangkat kiosk dicabut → endpoint kiosk terblokir.
- Siswa tidak aktif, stok 0, lebih dari tiga pinjaman aktif, pinjaman terlambat → tidak boleh mencatat peminjaman.
- Klik konfirmasi dua kali / admin dua browser → satu transaksi atau ditolak sebagai sudah diproses.
- Wi-Fi putus setelah scan → tidak ada pesan sukses sampai transaksi tercatat.
- Setelah 45 detik tanpa konfirmasi, sesi ditolak; perlu scan ulang.

## Aturan data dan keamanan
- `peminjaman` tetap tabel transaksi bisnis utama dan `LoanWorkflow` tetap yang mengelola stok.
- `library_kiosk_checkout_audit` tetap mencatat eksemplar per transaksi.
- `library_kiosk_pilot_checkouts` menyimpan idempotensi per event scan, `approved_by`, `loan_id`, dan `due_date`.
- Tidak menyimpan payload QR mentah dalam log.
- Siswa tidak dapat membuka endpoint persetujuan; hanya route admin terautentikasi.
- RFID RC522 UID tidak dianggap bukti keaslian kartu; QR buku stiker dapat difoto.
- Sistem pengembalian fisik harus dibuat sebelum produksi tanpa petugas.
- Laravel 10 perlu ditingkatkan ke versi yang didukung sebelum dibuka untuk akses publik.

## Rollback
`ROLLBACK.bat` pada folder PATCH 009 hanya mengembalikan FILE SOURCE. Migrasi MySQL **tidak otomatis dibatalkan**, apalagi jika sudah ada pinjaman nyata. Audit `library_kiosk_pilot_checkouts`/`peminjaman` sebelum keputusan rollback skema. Backup database secara terpisah sebelum pilot.
