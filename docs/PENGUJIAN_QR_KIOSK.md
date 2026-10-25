# Checklist pengujian nyata QR/Kiosk (bukan simulasi)

## Persiapan

1. Admin dan browser kiosk/HP harus **berbeda**. Pastikan HTTPS LAN dan izin kamera sudah berfungsi.
2. Backup MySQL. Pastikan **mode peminjaman live belum diaktifkan** pada lingkungan produksi.
3. Admin masuk `/admin/kiosk/hub`, pastikan satu siswa aktif dan satu eksemplar QR buku terdaftar (`LIBBOOK1`, bukan QR bukti peminjaman `LIBQR1`).
4. Login siswa terpisah lalu buka `/user/kiosk/pass` untuk QR anggota dinamis (`LIBMEM1`). QR anggota kedaluwarsa/sekali pakai, terbitkan ulang setiap percobaan.
5. Admin membuka `/admin/kiosk/devices` dan menerbitkan kode aktivasi sekali pakai. Di perangkat scanner berbeda buka `/kiosk/setup`, masukkan kode, lalu buka `/kiosk`.

## Tes terurut

- [ ] Kiosk `/kiosk` dapat dibuka **tanpa login admin** setelah aktivasi.
- [ ] Perangkat yang belum diaktivasi ditolak, tidak diberi akses admin.
- [ ] Scan anggota (`LIBMEM1`) diikuti buku (`LIBBOOK1`): status verifikasi server sukses.
- [ ] Reset sesi. Scan buku diikuti QR anggota baru: sukses.
- [ ] Scan QR anggota yang sudah digunakan: ditolak.
- [ ] QR anggota kedaluwarsa: ditolak.
- [ ] Buku terdaftar yang salah/tidak ada: ditolak.
- [ ] QR lama bukti peminjaman (`LIBQR1`) tidak diterima sebagai buku.
- [ ] Cabut credential perangkat di `/admin/kiosk/devices`; perangkat tidak bisa mengirim scan.
- [ ] Hitung stok dan pinjaman sebelum/sesudah di MySQL; **tetap sama saat validasi non-live**.
- [ ] Putus Wi-Fi/server: alat menampilkan gagal, bukan 'peminjaman sukses'.
- [ ] Perangkat kamera HP menyala lewat HTTPS tepercaya (uji izin browser, bukan dari HTTP LAN).

## Pengembalian

Sistem kiosk versi terakhir masih fokus **validasi dua scan**, bukan bukti buku fisik telah dikembalikan. Jangan menandai pengembalian final hanya berdasarkan foto QR buku tanpa mekanisme serah terima fisik.

## Error cepat

- `302 login`: kemungkinan memakai `/admin/kiosk/hub` di browser kiosk. Gunakan `/kiosk/setup`.
- `401/403`: kode belum diaktivasi/cookie hilang atau akses tidak sesuai role.
- `419`: CSRF/session/cookie; cek origin, APP_KEY, SESSION_SECURE_COOKIE, HTTPS proxy.
- `426`: Laravel tidak melihat request sebagai HTTPS; cek Caddy/TrustProxies dan forwarded headers.
- `404`: periksa `CEK_KIOSK.bat` dan route Laravel PATCH 007.
