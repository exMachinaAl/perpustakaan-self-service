<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kartu QR Anggota • Perpustakaan</title><style>body{font-family:system-ui;background:#0d1930;color:white;text-align:center;padding:24px}
article{max-width:390px;margin:4vh auto;background:#1a3150;border-radius:22px;padding:26px}.qr{background:white;padding:20px;border-radius:14px;display:inline-block}a{color:#83d5ff}</style></head><body><article>
<h2>QR Anggota Perpustakaan</h2><p>Tunjukkan QR ini ke kamera kiosk atau scanner buku mandiri.</p>
<div class="qr">{!! $qr !!}</div><p>QR berlaku <b>90 detik</b>, sekali pakai.</p><a href="{{ route('user.kiosk.pass') }}">Perbarui QR</a>
<p><small>Jangan berikan screenshot QR ke orang lain. Jika tidak membawa HP, gunakan kartu RFID di kiosk.</small></p>
</article></body></html>