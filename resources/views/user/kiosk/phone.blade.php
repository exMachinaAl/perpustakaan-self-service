<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Scan Buku via HP</title>
<style>body{font-family:system-ui;background:#0d1930;color:white;text-align:center;padding:16px}.panel{max-width:470px;margin:auto;background:#1b304b;border-radius:20px;padding:20px}
button{padding:12px 20px;color:white;background:#2872b9;border:0;border-radius:12px}#reader{background:#000;min-height:220px}small{color:#abc9e1}</style>
</head><body><div class="panel">
<h2>Kiosk Terhubung</h2><p>Identitas Anda sudah dikenali dari akun perpustakaan. Sekarang scan QR yang menempel pada buku.</p>
<div id="camera-advice" style="margin:10px;padding:10px;border:1px solid #6795b5;border-radius:10px" role="status">Memeriksa akses kamera...</div>
<button id="go">Mulai scan buku</button>
<p><label>Atau scan QR dari foto/galeri:</label><input id="photo" type="file" accept="image/*" capture="environment" style="max-width:100%;margin-top:8px"></p>
<div id="reader" style="margin-top:14px"></div><h3 id="result">Menunggu QR buku...</h3>
<small>Mode uji: belum membuat transaksi pinjaman. Sesi berlaku maksimal 5 menit.</small>
</div><script>window.kioskPhone005={url:@json(route('user.kiosk.phone.scan')),csrf:@json(csrf_token()),jsLocal:@json(asset('vendor/html5-qrcode.min.js')),allowCdn:@json(!app()->environment('production'))};</script>
<script src="{{ asset('js/kiosk-phone-005.js') }}"></script></body></html>