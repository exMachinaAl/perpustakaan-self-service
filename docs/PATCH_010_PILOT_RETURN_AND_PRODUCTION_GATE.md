# PATCH 010 — Pengembalian fisik pilot & Production Readiness

Versi ini **tidak** mengaktifkan self-service unattended. Sistem tetap menggunakan scan QR dan sesi kiosk dari PATCH 007–009. Pengembalian baru khusus pinjaman yang berasal dari pilot kiosk dan **wajib menerima buku fisik lebih dulu**.

## Syarat

- PATCH 009 sudah terpasang; halaman `/admin/kiosk/pilot` dapat dibuka.
- Backup MySQL yang terverifikasi; proses migrasi dijalankan pada salinan database sebelum database sekolah.
- Laravel dan MySQL berjalan; HTTPS tersedia.
- File `tools/runtime/CEK_PRODUKSI_010.bat` sudah terpasang.

## Test transaksi satu buku (staging)

1. Periksa admin `GET /admin/kiosk/pilot`, kiosk `GET /kiosk` sudah teraktivasi dan halaman tidak meminta login admin.
2. Nonaktifkan mode live, lakukan scan siswa dan scan buku pada perangkat kiosk. Status harus **validated**, tanpa menambah pinjaman atau mengurangi stok.
3. Untuk pilot terawasi pada database salinan saja, atur `KIOSK_PILOT_CHECKOUT=true` dan `APP_ENV=local`. Jalankan `php artisan config:clear`.
4. Scan ulang QR siswa baru dan QR buku fisik; konfirmasikan pinjam di panel admin. Pastikan tepat satu transaksi berstatus `dipinjam`, stok turun tepat 1, jatuh tempo 7 hari, dan ada baris `library_kiosk_pilot_checkouts`.
5. Bawa buku fisik ke petugas, buka panel admin bagian **Terima Pengembalian Fisik**, cocokkan judul dan label, centang penerimaan fisik, tambahkan catatan kondisi bila diperlukan, lalu konfirmasi.
6. Harus ada tepat satu baris `library_kiosk_pilot_returns`, pinjaman berstatus `dikembalikan`, stok kembali +1, perhitungan denda menggunakan `LoanWorkflow`. Ulangi permintaan identik untuk mengecek idempotensi; stok TIDAK boleh naik dua kali.
7. Uji bahwa pengembalian tanpa checkbox ditolak, pengembalian pinjaman biasa ditolak, dan petugas tidak bisa mengembalikan buku yang belum diterima. Matikan mode pilot setelah uji.

## Pemeriksaan produksi

Jalankan `tools/runtime/CEK_PRODUKSI_010.bat`. Hasil berada di `tools/runtime/_reports/READINESS_010.txt` (tidak berada di direktori induk proyek). Pemeriksa **tidak melakukan migrate atau update database**.

Kondisi blocking: Laravel 10 sudah melewati masa dukungan keamanan; PHP 8.2.34 perlu update ke 8.2.35 atau PHP cabang lain yang telah ditambal; APP_DEBUG=true; tidak memakai HTTPS/cookie secure; database tidak tersambung; migrasi belum terpasang; mode pilot menyala.

Tes manual wajib: rollback dan restore backup; multi-kiosk concurrency; outage Wi-Fi/Caddy/MySQL; penolakan QR lama; QR kartu disalin; kontrol autentikasi perangkat; buku fisik benar-benar diterima; uji kebijakan denda dan privilege; update dependensi; pencatatan hasil uji. Checker tidak dapat memverifikasi poin-poin tersebut secara otomatis.

**Catatan teknis:** Pada Laravel 10, dukungan security berakhir 4 Februari 2025. Laravel 12 menerima security fixes hingga 24 Februari 2027 tetapi migrasi perlu diuji dari Laravel 10 secara bertahap. Laravel 13 membutuhkan PHP 8.3+; jangan menaikkan versi Laravel/PHP otomatis melalui patch operasional ini.

## Batas rollback

`ROLLBACK.bat` hanya mengembalikan file source yang belum dimodifikasi sejak patch. Tabel MySQL dan data pengembalian **tidak** dihapus. Menghapus data audit atau membalik stok secara manual dapat menyebabkan korupsi data; konsultasikan backup dan laporan migrasi.
