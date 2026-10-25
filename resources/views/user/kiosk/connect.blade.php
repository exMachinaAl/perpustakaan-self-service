<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Hubungkan HP ke kiosk</title></head>
<body style="background:#0d1930;color:white;text-align:center;padding:40px;font:16px system-ui"><h2>Kiosk Perpustakaan</h2>
<p>Anda telah masuk ke akun siswa. Tekan tombol berikut untuk menghubungkan HP dengan kiosk dan memindai QR buku.</p>
<form method="post" action="{{ route('user.kiosk.connect.submit',$token) }}">@csrf
<button type="submit" style="padding:16px;background:#2669ad;color:white;border:0;border-radius:12px">Mulai Scan Buku</button></form><small>QR kiosk berlaku 2 menit dan hanya sekali pakai.</small></body></html>