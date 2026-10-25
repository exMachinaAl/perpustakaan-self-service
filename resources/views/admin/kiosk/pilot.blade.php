<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pilot Peminjaman Kiosk</title>
<style>body{margin:0;padding:24px;background:#0d1b30;color:#f0f5ff;font:16px system-ui}main{max-width:1050px;margin:auto}.card{background:#172b47;border:1px solid #335578;border-radius:14px;padding:18px;margin:16px 0}table{width:100%;border-collapse:collapse}td,th{padding:10px;border-bottom:1px solid #385476;text-align:left}button{font:inherit;background:#2d72bb;border:0;color:white;padding:10px;border-radius:8px;cursor:pointer}button:disabled{background:#516274;cursor:not-allowed}a{color:#a7dfff}.warn{background:#5a3921;padding:12px;border-radius:8px}.success{background:#124d40;padding:12px;border-radius:8px}</style></head><body><main>
<h1>Circulation Engine — Pilot Petugas</h1><p><a href="{{ route('admin.kiosk.devices') }}">Perangkat Kiosk</a> · <a href="{{ route('admin.kiosk.hub') }}">Scan Hub</a></p>
<div class="card"><p><strong>Status:</strong> {{ $pilotEnabled ? 'PILOT AKTIF (khusus non-production)' : 'VALIDASI SAJA — tidak ada transaksi' }}</p><p class="warn">Peminjaman nyata hanya boleh dikonfirmasi petugas setelah memeriksa siswa dan buku fisik. QR stiker bisa difoto. Jangan gunakan mode pilot untuk peminjaman tanpa pengawasan.</p><p>Aturan: maksimal 3 buku aktif, 7 hari kalender, tidak menerima siswa tidak aktif, eksemplar sedang dipinjam, atau siswa dengan pinjaman terlambat.</p></div>
@if(session('notice'))<div class="success">{{ session('notice') }}</div>@endif
@if($errors->any())<div class="warn">{{ $errors->first() }}</div>@endif
<div class="card"><h2>Sesi scan per perangkat</h2><div style="overflow:auto"><table><thead><tr><th>Perangkat</th><th>Status</th><th>Anggota</th><th>Buku</th><th>Waktu habis</th><th>Tindakan</th></tr></thead><tbody>
@forelse($devices as $d)<tr><td>#{{ $d->id }} — {{ $d->name }}</td><td>{{ $d->state ?: 'ready' }}</td><td>{{ $d->member_name ?: '—' }}</td><td>{{ $d->book_title ? $d->book_title.' / '.$d->copy_label : '—' }}</td><td>{{ $d->expires_at ?: '—' }}</td><td>
@if($d->state === 'borrowed') Pinjaman #{{ $d->checkout_loan_id }}
@elseif($pilotEnabled && $d->active && $d->state === 'validated' && $d->validated_event_id && $d->expires_at && \Carbon\Carbon::parse($d->expires_at)->isFuture())
<form method="post" action="{{ route('admin.kiosk.pilot.approve', $d->id) }}">@csrf <button onclick="return confirm('Pastikan siswa dan buku fisik sudah benar. Catat peminjaman nyata?')">Konfirmasi Pinjam</button></form>
@else — @endif</td></tr>
@empty<tr><td colspan="6">Belum ada perangkat terdaftar.</td></tr>@endforelse
</tbody></table></div></div>
<div class="card"><h2>Riwayat transaksi pilot</h2><table><thead><tr><th>Perangkat</th><th>ID Pinjaman</th><th>Jatuh tempo</th><th>Waktu</th></tr></thead><tbody>@forelse($checkouts as $c)<tr><td>{{ $c->name }}</td><td>#{{ $c->loan_id }}</td><td>{{ $c->due_date }}</td><td>{{ $c->created_at }}</td></tr>@empty<tr><td colspan="4">Belum ada transaksi pilot.</td></tr>@endforelse</tbody></table></div>
<p>Untuk pengujian identifikasi normal, biarkan <code>KIOSK_PILOT_CHECKOUT=false</code>. Status QR valid tidak pernah menulis data pinjaman.</p>
</main></body></html>
