<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Smart Library — QR Live Station</title>
<style>
:root{font-family:system-ui,Arial;background:#0b1324;color:#ecf4ff}*{box-sizing:border-box}body{margin:0;padding:24px}
header,section{max-width:1200px;margin:0 auto 18px}header{display:flex;justify-content:space-between;align-items:center;gap:16px}
a{color:#7cd2ff}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:16px}
.card{background:#16253f;border:1px solid #2a456b;border-radius:18px;padding:20px;min-width:0}
button,select,input{font:inherit;border-radius:9px;padding:10px;border:1px solid #547393}button{background:#2669ad;color:white;cursor:pointer}
button:hover{background:#347ecc}button:disabled{opacity:.5;cursor:not-allowed}input,select{background:#071323;color:#fff;width:100%}
#stage{text-align:center;padding:50px 20px;border-radius:22px;background:linear-gradient(145deg,#172c4d,#10344d);box-shadow:0 8px 40px #0004}
#stage h1{font-size:clamp(26px,5vw,50px);margin:0 0 12px;transition:all .3s}#stage p{font-size:18px;color:#bcd5f4}#stage.ready{animation:pulse 2.5s infinite}#stage.ok{border:2px solid #25bf80}#stage.error{border:2px solid #ff7676}
@keyframes pulse{50%{box-shadow:0 0 0 7px #1a557325}}
.status{font-weight:bold;color:#79ffc9}.row{display:flex;gap:8px;align-items:center;flex-wrap:wrap}.pill{font-size:12px;border:1px solid #41668d;border-radius:30px;padding:5px 12px}
small{color:#b0c6e0}#reader{width:100%;min-height:160px;overflow:hidden;border-radius:10px;background:#000}.qrwrap{display:inline-block;padding:12px;border-radius:12px;background:#fff;color:#111}
#events{max-height:280px;overflow:auto}#events>div{padding:8px 0;border-bottom:1px solid #2a456b}.hide{display:none}
@media(max-width:600px){body{padding:12px}}
@media print{body{background:#fff!important;color:#000!important;padding:0!important}header,#stage,#tests,.grid:not(#labels-grid),.noprint{display:none!important}#labels-grid{display:block!important}#labels-grid .card{border:0;background:#fff;color:#111}#labels-grid #events-wrap{display:none}#label-sheet{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}#label-sheet>div{break-inside:avoid;border:1px dashed #aaa}}
.alert{border-radius:12px;padding:12px;background:#273c55;border:1px solid #5b7899;margin:10px 0}.warn{border-color:#efa96a;background:#51341b}.good{border-color:#36c994;background:#164c3d}.scroll{overflow-wrap:anywhere}
</style>
</head><body>
<header><div><h2 style="margin:0">Smart Library • QR Live Station</h2><small>PATCH 006 • Scan QR asli • sesi tersimpan di Laravel • pilot hanya jika diaktifkan petugas</small></div><a href="{{ route('admin.rfid.desk') }}">Monitor RFID lama →</a></header>
<nav aria-label="Navigasi cepat QR LAB" style="max-width:1200px;margin:0 auto 18px;padding:12px 16px;background:#153557;border:1px solid #337aa4;border-radius:14px;display:flex;flex-wrap:wrap;gap:10px;align-items:center">
 <strong style="width:100%">PATCH 006A • Cari fitur dengan cepat</strong>
 <a href="#kiosk006-mode" style="padding:8px 11px;background:#21517a;border-radius:9px;color:#fff">Status / Alur scan</a>
 <a href="{{ route('admin.kiosk.devices') }}" style="padding:8px 11px;background:#287751;border-radius:9px;color:#fff">Daftarkan Kiosk</a>
 <a href="#panel-kamera" style="padding:8px 11px;background:#21517a;border-radius:9px;color:#fff">Mulai kamera</a>
 <a href="#panel-anggota" style="padding:8px 11px;background:#21517a;border-radius:9px;color:#fff">QR Anggota Uji</a>
 <a href="#copy-form" style="padding:8px 11px;background:#21517a;border-radius:9px;color:#fff">Daftarkan QR Buku</a>
 <a href="#labels-grid" style="padding:8px 11px;background:#21517a;border-radius:9px;color:#fff">Cetak QR Buku</a>
 <span style="width:100%;font-size:13px;color:#c0d5e9">Pengujian dari PC sendiri: <code>http://localhost:8000</code>. HP lain perlu HTTPS tepercaya agar kamera dapat aktif.</span>
</nav>
@if($errors->any())
<section class="alert warn" role="alert"><strong>Formulir belum berhasil:</strong><ul>
@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
</ul><p>Periksa buku, label, dan akun siswa yang tersedia. Data peminjaman tidak berubah.</p></section>
@endif
@if(session('success'))<section class="alert good" role="status">{{ session('success') }}</section>@endif
<section class="card" id="lab-onboarding" style="max-width:1200px;margin:0 auto 18px">
  <h2>Registrasi anggota dan buku — fondasi scan QR asli</h2>
  <p class="alert">QR siswa dan QR buku dibaca dari kamera atau scanner. Laravel menyimpan sesi, melakukan validasi, dan dapat membuat peminjaman hanya jika dua pengaturan pilot diaktifkan. <b>Default: validasi QR asli tanpa mengubah stok.</b></p>
  <div class="grid" style="margin-bottom:12px">
   <div class="card"><strong>Anggota aktif: {{ $readiness['active_members'] }}</strong><br>
    <small>Total anggota: {{ $readiness['all_members'] }}. QR aktif hanya menerima role <code>user</code> dengan status <code>aktif</code>.</small>
    <p><a href="{{ route('admin.anggota.create') }}" target="_blank" rel="noopener">Daftarkan siswa di Data Anggota ↗</a></p>
    @if($readiness['active_members']===0)<p class="alert warn">Tidak ada siswa aktif. Langkah scan QR siswa dan simulasi anggota belum bisa dilanjutkan.</p>@endif
   </div>
   <div class="card"><strong>Katalog: {{ $readiness['catalog_books'] }} judul</strong><br>
    <small>Eksemplar QR aktif: {{ $readiness['copies'] }}. Buku katalog tidak otomatis memiliki QR eksemplar.</small>
    <p><a href="{{ route('admin.databuku.create') }}" target="_blank" rel="noopener">Tambah judul buku ↗</a></p>
    @if($readiness['catalog_books']===0)<p class="alert warn">Belum ada buku di katalog, registrasi QR belum bisa berjalan.</p>
    @elseif($readiness['copies']===0)<p class="alert warn">Belum ada eksemplar QR. Pilih judul pada formulir Registrasi QR Buku di bawah.</p>@endif
   </div>
   <div class="card"><strong>Bukti tidak mengubah transaksi</strong><br>
    <small>Snapshot awal: <b id="snapshot-loans">{{ $readiness['loans'] }}</b> transaksi; stok total <b id="snapshot-stock">{{ $readiness['stock_total'] }}</b>.</small>
    <p><button id="lab-health-btn" type="button">Periksa lagi setelah simulasi</button></p>
    <p class="alert" id="lab-health-result">Snapshot stok dan transaksi dibaca dari MySQL. Perubahan akibat aktivitas lain juga ikut terhitung.</p>
   </div>
  </div>
  <p><b>Urutan scan NYATA:</b> (1) daftar QR buku, (2) terbitkan QR anggota dari akun siswa atau menu admin uji, (3) buka kamera pada HTTPS/localhost atau gunakan USB 2D, (4) scan kedua QR dalam urutan bebas, (5) server mengembalikan hasil validasi / nomor pinjaman.</p>
</section>
<section class="card" id="kiosk006-mode" style="max-width:1200px;margin:0 auto 18px;border-color:#51bba4">
<h2>Scan QR Sungguhan — PATCH 006</h2>
<p><b>Mode: {{ config('kiosk_circulation.live_loans') && config('kiosk_circulation.staff_pilot') && !app()->environment('production') ? 'PILOT LIVE — peminjaman mengubah stok' : 'VALIDASI QR ASLI — transaksi belum diaktifkan' }}</b></p>
<p>Tidak ada tombol simulasi untuk menuntaskan sesi ini. <strong>QR LIBMEM1 dan LIBBOOK1 harus benar-benar dibaca</strong> dari kamera, foto, atau input scanner. Kiosk menggunakan sesi MySQL dengan timeout 45 detik.</p>
<p class="alert" id="kiosk006-note">Silakan mulai kamera atau gunakan QR scanner.</p>
<small>Alur peminjaman otomatis hanya untuk <b>pilot didampingi petugas</b>; jangan tinggalkan akun admin login di kiosk publik.</small>
</section>
<section id="stage" class="ready" aria-live="polite"><div style="font-size:48px">◎</div><h1 id="stage-title">SIAP UNTUK SCAN</h1>
<p id="stage-description">Scan QR anggota dan QR buku dengan kamera, unggah foto, atau scanner USB. RFID merupakan jalur lama terpisah.</p>
<div class="row" style="justify-content:center"><span class="pill">ANGGOTA: <b id="member-name">BELUM ADA</b></span><span class="pill">BUKU: <b id="book-name">BELUM ADA</b></span></div>
<p class="status" id="result-state">MODE VALIDASI QR ASLI — BELUM MEMINJAM</p><button id="reset" type="button">Batalkan / Reset Sesi</button></section>
<section class="alert" style="max-width:1200px;margin:0 auto 18px"><span id="js-check">Memeriksa JavaScript Scan Hub...</span><noscript>JavaScript tidak aktif. Registrasi QR buku tetap bisa dilakukan, tetapi simulasi perlu JavaScript.</noscript></section>
<section id="tests" class="grid">
<div class="card"><h3>Mulai pengujian dari sini</h3>
<ol><li>Login sebagai <b>admin</b> lalu buka halaman ini.</li><li>Di bagian <b>Registrasi QR Buku</b>, pilih judul dan buat label, misalnya <code>BK-TEST-001</code>.</li><li>Tekan <b>Cetak QR buku</b> atau tunjukkan label pada layar lain.</li><li>Login akun siswa di browser profil lain/HP, buka <a href="{{ route('user.kiosk.pass') }}">/user/kiosk/pass</a> untuk QR anggota.</li><li>Gunakan kamera, unggah foto QR, atau scanner USB. Scan anggota dan buku dalam urutan apa pun.</li><li>Verifikasi layar menyebut <b>QR ASLI VALID</b> dan stok tidak berubah.</li></ol>
<p class="alert">Jika kamera tidak tersedia, gunakan scanner USB atau uji scan dari foto QR asli. Tombol simulasi lama tidak lagi menggerakkan sesi scan utama.</p></div>
<div class="card"><h3>Diagnosis kamera otomatis</h3><div id="camera-diagnostics" class="alert" role="status">Sedang memeriksa browser...</div>
<small id="origin-help" class="scroll"></small><p><button id="diag-recheck" type="button">Periksa ulang</button></p>
<small>Gunakan <b>localhost pada komputer kiosk</b> atau <b>HTTPS tepercaya pada HP/LAN</b>. Tidak ada pengaturan aman di Laravel yang bisa menghapus pembatasan kamera HTTP LAN.</small></div>
<div class="card hide"><h3>Simulasi lama (nonaktif di PATCH 006)</h3><label>Anggota aktif</label><select id="sim-member"><option value="">Pilih siswa</option>@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->nama }}</option>@endforeach</select>
<button id="sim-member-btn" type="button" style="margin-top:8px">Simulasikan scan anggota</button>
<label style="display:block;margin-top:12px">QR eksemplar yang sudah didaftarkan</label><select id="sim-book"><option value="">Pilih eksemplar</option>@foreach($copies as $copy)<option value="{{ $copy->id }}">{{ $copy->label }}</option>@endforeach</select>
<button id="sim-book-btn" type="button" style="margin-top:8px">Simulasikan scan buku</button>
<p><small>Simulasi menggunakan data anggota/buku terdaftar, tanpa pembuatan pinjaman. Untuk uji QR sungguhan, gunakan generator LAB berikut.</small></p></div>
<div class="card" id="panel-anggota"><h3>QR Anggota Uji — khusus Admin LAB</h3>
<p><small>Pilih <b>anggota aktif yang benar-benar ada di MySQL</b>. Ini hanya menerbitkan credential QR sementara, tidak membuat akun palsu atau peminjaman.</small></p>
<label for="lab-qr-member">Siswa</label><select id="lab-qr-member"><option value="">Pilih anggota aktif</option>
@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->nama }}</option>@endforeach</select>
<p><button id="lab-qr-generate" type="button">Terbitkan QR Anggota Uji (90 detik)</button></p>
<div id="lab-qr-generated" class="qrwrap hide" aria-live="polite"></div>
<p id="lab-qr-note" class="alert">QR hanya berlaku sekali, 90 detik, untuk keperluan LAB. Setelah dipindai, token tidak bisa dipakai ulang.</p>
<small>Produksi: siswa sendiri login melalui <code>/user/kiosk/pass</code> di sesi browser terpisah. Akun admin tidak boleh dipakai sebagai QR siswa.</small>
</div>
</section>
<section class="grid">
<div class="card" id="panel-kamera"><h3>1. Kamera browser / webcam</h3><p><small>Gunakan kamera laptop atau webcam untuk QR anggota dan QR buku (HTTPS/localhost diperlukan).</small></p>
<div class="row"><button id="cam-start" type="button">Mulai kamera</button><button id="cam-stop" type="button">Hentikan</button></div>
<div id="reader" style="margin-top:12px"></div>
<p><label for="qr-photo"><b>Alternatif saat kamera diblokir: scan dari foto QR</b></label>
<input id="qr-photo" type="file" accept="image/*" capture="environment"></p>
<p class="alert" id="camera-note" role="status">Jika kamera tidak aktif, periksa diagnosis di bagian atas, gunakan localhost atau HTTPS tepercaya.</p>
<small>File foto diproses lokal di browser (tidak diunggah). Pemindaian memerlukan library decoder QR; jika CDN tidak tersedia, gunakan scanner USB atau tempel kode dari alat uji.</small>
</div>
<div class="card"><h3>2. Scanner QR USB 2D</h3><p><small>Scanner mode keyboard-wedge. Klik kolom, scan QR, hasil diproses saat Enter.</small></p>
<input id="wedge" type="text" placeholder="Fokus di sini, kemudian scan..." autocomplete="off" spellcheck="false" />
<p><small>Input ini untuk scanner USB keyboard-wedge. Jangan menyalin token ke log atau membagikannya.</small></p></div>
<div class="card"><h3>3. RFID RC522 + ESP32</h3><p><small>Tap otomatis dari event terverifikasi PATCH 001–003. UID tidak diketik pada kiosk.</small></p>
<label for="reader-select">Pilih reader</label><select id="reader-select"><option value="">Tanpa reader</option>
@foreach($readers as $r)<option value="{{ $r->id }}">{{ $r->name }} ({{ $r->serial }})</option>@endforeach</select>
<p><small>UID MFRC522 bisa dikloning: hanya pengenal berkepercayaan rendah dalam mode debug ini.</small></p></div>
<div class="card"><h3>4. HP siswa (dua scan)</h3>
<p><small>Siswa login di HP, scan QR kiosk ini, lalu scan QR buku langsung dari HP. Tidak perlu scan QR anggota lagi.</small></p>
<div class="qrwrap" id="pair-qr">{!! $qr !!}</div>
<p><small>URL pairing untuk HP:</small> <code id="pair-link" class="scroll">{{ $pairUrl }}</code></p>
@if(str_contains($phoneBaseUrl,'localhost') || str_contains($phoneBaseUrl,'127.0.0.1'))
<div class="alert warn">Alamat HP masih localhost. HP lain <b>tidak bisa</b> mengakses server dari alamat itu. Isi <code>KIOSK_PHONE_BASE_URL=https://alamat-sekolah</code> pada <code>.env</code>, lalu jalankan <code>php artisan optimize:clear</code>. HTTPS harus valid/tepercaya.</div>
@endif
<p><small>QR pairing berlaku 2 menit dan diperbarui otomatis.</small></p></div>
<div class="card"><h3>5. ESP32-CAM (opsional)</h3><p><small>Endpoint HMAC: <code>POST /api/v1/kiosk/qr</code>, payload JSON <code>{"payload":"LIBBOOK1:..."}</code>. Pilih reader yang sudah diregistrasi untuk menampilkan event.</small></p>
<p><small>Kamera ESP32-CAM dan firmware decoding QR belum diuji; integrasi API tersedia untuk pengujian nanti.</small></p></div>
<div class="card"><h3>Registrasi QR buku fisik (langkah 1)</h3><p><small>Setiap buku fisik membutuhkan label unik. Ini bukan QR bukti peminjaman <code>LIBQR1</code>.</small></p><form method="post" id="copy-form" action="{{ route('admin.kiosk.copies.create-form') }}"><input type="hidden" name="_token" value="{{ csrf_token() }}">
<label for="book-select">Buku (harus dipilih sebelum submit)</label><select id="book-select" name="book_id" required><option value="">Pilih judul</option>@foreach($books as $b)
<option value="{{ $b->id_buku }}">{{ $b->judul }} (stok agregat {{ $b->stock }})</option>@endforeach</select>
<label style="display:block;margin-top:10px">Label eksemplar</label><input name="label" placeholder="Contoh: BK-FISIKA-001" maxlength="80" required>
<button type="submit" style="margin-top:10px">Daftarkan dan Buat QR Buku</button></form>
<p><small>Belum mengurangi stok atau membuat relasi transaksi eksemplar. Jangan gunakan QR bukti LIBQR1 sebagai label buku.</small></p></div>
</section>

<section class="grid" id="labels-grid">
<div class="card"><h3>Label QR buku untuk dicetak (langkah 2)</h3>
<p class="noprint"><button type="button" onclick="window.print()">Cetak QR buku</button> <small>Gunakan print browser, pilih label yang ingin dicetak.</small></p>
<div id="label-sheet" style="display:flex;gap:12px;flex-wrap:wrap">
@foreach($copies as $copy)<div style="background:white;color:#111;text-align:center;border-radius:8px;padding:7px;max-width:146px">
{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(116)->margin(1)->generate($copy->qr_value) !!}
<div style="font-size:11px;overflow-wrap:anywhere">{{ $copy->label }}</div></div>@endforeach</div></div>
<div class="card" id="events-wrap"><h3>Event terbaru</h3><div id="events"><small>Belum ada event baru.</small></div></div>
</section>
<script>
window.kiosk006 = {
 csrf: @json(csrf_token()),
 scan: @json(route('admin.kiosk.circulation.scan', [], false)),
 reset: @json(route('admin.kiosk.circulation.reset', [], false)),
 status: @json(route('admin.kiosk.circulation.status', [], false)),
 memberPass: @json(route('admin.kiosk.debug.member-pass', [], false)),
 health: @json(route('admin.kiosk.health', [], false)),
 pair: @json(route('admin.kiosk.pair.new', [], false)),
 localJs: @json(asset('vendor/html5-qrcode.min.js')),
 live: @json((bool)config('kiosk_circulation.live_loans') && (bool)config('kiosk_circulation.staff_pilot') && !app()->environment('production'))
};
</script>
<script src="{{ asset('js/kiosk-scan-006.js') }}"></script></body></html>