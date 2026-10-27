<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Anggota - {{ $anggota->nama }}</title>
    <style>
        *{box-sizing:border-box}body{font-family:Arial,sans-serif;background:#eef2f7;color:#071b33;margin:0;padding:24px}.toolbar{max-width:860px;margin:0 auto 18px;display:flex;flex-wrap:wrap;gap:10px;align-items:center}.toolbar a,.toolbar button{border:2px solid #071b33;background:#fff;color:#071b33;padding:10px 15px;font-weight:bold;text-decoration:none;cursor:pointer}.toolbar .danger{background:#fee2e2}.notice{width:100%;padding:10px;border:2px solid #047857;background:#ecfdf5;color:#065f46;font-weight:bold}.card{width:86mm;height:54mm;margin:auto;background:#fff;border:2px solid #071b33;border-radius:4mm;display:grid;grid-template-columns:1fr 34mm;overflow:hidden;box-shadow:5px 5px 0 #071b33}.info{padding:6mm;display:flex;flex-direction:column}.brand{font-weight:900;font-size:14px}.subtitle{font-size:8px;text-transform:uppercase;letter-spacing:1px;color:#475569}.name{font-weight:900;font-size:15px;margin-top:auto}.meta{font-size:9px;margin-top:2px}.qr{background:#f8fafc;border-left:1px solid #071b33;padding:3mm;display:flex;flex-direction:column;align-items:center;justify-content:center}.qr svg{width:28mm;height:28mm}.qr small{font-size:7px;text-align:center;font-weight:bold}@media print{body{background:#fff;padding:0}.toolbar{display:none}.card{box-shadow:none;margin:0}}
    </style>
</head>
<body>
    <div class="toolbar">
        @if(session('success'))<div class="notice">{{ session('success') }}</div>@endif
        <button onclick="window.print()">Cetak Kartu</button>
        <a href="{{ route('admin.anggota.show', $anggota->id) }}">Kembali</a>
        <form method="POST" action="{{ route('admin.anggota.card.reissue', $anggota->id) }}" onsubmit="return confirm('Kartu lama akan langsung tidak berlaku. Terbitkan kartu baru?')">
            @csrf
            <button class="danger">Terbitkan Ulang</button>
        </form>
    </div>
    <article class="card">
        <div class="info">
            <div class="brand">UMG DIGILIB</div>
            <div class="subtitle">Kartu anggota perpustakaan</div>
            <div class="name">{{ $anggota->nama }}</div>
            <div class="meta">NIM: {{ $anggota->nim }}</div>
            <div class="meta">Prodi: {{ $anggota->prodi->nama ?? '-' }}</div>
        </div>
        <div class="qr">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(160)->margin(1)->generate($payload) !!}
            <small>SCAN DI KIOS<br>RAHASIAKAN PIN</small>
        </div>
    </article>
</body>
</html>
