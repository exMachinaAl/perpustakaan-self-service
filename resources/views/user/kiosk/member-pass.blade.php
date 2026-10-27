<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Kartu QR Anggota • Perpustakaan</title>
    <style>
        body{font-family:system-ui;background:#0d1930;color:white;text-align:center;padding:24px}
        article{max-width:430px;margin:4vh auto;background:#1a3150;border-radius:22px;padding:26px}
        .qr{background:white;padding:20px;border-radius:14px;display:inline-block}
        .notice{background:#124d40;padding:12px;border-radius:10px}.warn{background:#5a3921;padding:12px;border-radius:10px}
        input,button{box-sizing:border-box;width:100%;font:inherit;padding:12px;border-radius:9px;margin-top:8px}
        button{background:#2f82ca;color:white;border:0;font-weight:700}a{color:#83d5ff}
    </style>
</head>
<body><article>
    <h2>Kartu QR Anggota</h2>
    <p>QR ini dibuat otomatis untuk akun Anda dan dapat dicetak. Scan di kiosk lalu masukkan PIN.</p>
    <div class="qr">{!! $qr !!}</div>
    @if(session('notice'))<p class="notice">{{ session('notice') }}</p>@endif
    <p class="{{ $pinReady ? 'notice' : 'warn' }}">{{ $pinReady ? 'PIN kiosk sudah aktif.' : 'Buat PIN sebelum memakai kartu ini di kiosk.' }}</p>
    <form method="post" action="{{ route('user.kiosk.pin') }}">
        @csrf
        <label>PIN kiosk baru (6 angka)</label>
        <input type="password" name="pin" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autocomplete="new-password">
        <label>Ulangi PIN</label>
        <input type="password" name="pin_confirmation" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autocomplete="new-password">
        <button type="submit">Simpan PIN</button>
    </form>
    @if($errors->any())<p class="warn">{{ $errors->first() }}</p>@endif
    <p><small>Jangan bagikan kartu dan PIN. Jika kartu hilang, hubungi petugas untuk menerbitkan ulang.</small></p>
</article></body></html>
