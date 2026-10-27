<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label QR - {{ $book->judul }}</title>
    <style>
        *{box-sizing:border-box}body{font-family:Arial,sans-serif;margin:16px;color:#111}.toolbar{display:flex;gap:10px;align-items:center;margin-bottom:16px}.toolbar button,.toolbar a{padding:9px 14px;border:2px solid #111;background:#fff;color:#111;text-decoration:none;font-weight:bold}.labels{display:grid;grid-template-columns:repeat(3,1fr);gap:10mm}.label{border:1px dashed #555;padding:5mm;text-align:center;break-inside:avoid;min-height:64mm;display:flex;flex-direction:column;align-items:center;justify-content:center}.label svg{width:36mm;height:36mm}.title{font-size:11px;font-weight:bold;margin-top:2mm;max-width:55mm}.code{font:700 12px monospace;margin-top:1mm}.barcode{font:10px monospace;color:#444}@media print{body{margin:0}.toolbar{display:none}.labels{gap:3mm}.label{border:1px dashed #aaa}}
    </style>
</head>
<body>
    <div class="toolbar"><button onclick="window.print()">Cetak Label</button><a href="{{ route('admin.databuku.inventory.index', $book) }}">Kembali</a><span>{{ $copies->count() }} label</span></div>
    <main class="labels">
        @foreach($copies as $copy)
            <article class="label">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(180)->margin(1)->generate($copy->qr_payload) !!}
                <div class="title">{{ $book->judul }}</div>
                <div class="code">{{ $copy->inventory_code }}</div>
                @if($copy->barcode)<div class="barcode">Barcode: {{ $copy->barcode }}</div>@endif
            </article>
        @endforeach
    </main>
</body>
</html>
