@extends('layouts.admin')
@section('title', 'Monitor Tap RFID')
@section('content')
<div class="space-y-6">
  <section class="bg-white border-2 border-slate-800 rounded-xl p-5">
    <div class="flex flex-wrap justify-between gap-3 items-center">
      <div>
        <h1 class="text-2xl font-extrabold">Monitor Tap RFID / NFC</h1>
        <p class="text-slate-600 text-sm mt-2">Update otomatis setiap 3 detik. Reader hanya mengidentifikasi; transaksi buku tetap dikonfirmasi petugas.</p>
      </div>
      <a href="{{ route('admin.rfid.index') }}" class="px-4 py-2 rounded-lg border font-semibold text-blue-700">Kelola / Pasangkan Kartu</a>
    </div>
  </section>

  <section class="bg-white border rounded-xl p-5 space-y-3">
    <h2 class="text-lg font-bold">Status Perangkat ESP32 / RC522</h2>
    <p class="text-sm text-slate-600">Online jika heartbeat atau scan terautentikasi diterima dalam 120 detik. Status online tidak berarti kartu anti-kloning.</p>
    <div class="overflow-x-auto"><table class="w-full text-sm">
      <thead class="bg-slate-100"><tr><th class="text-left p-2">Reader</th><th class="text-left p-2">Status</th><th class="text-left p-2">Firmware / Wi-Fi</th><th class="text-left p-2">Terakhir terkoneksi</th></tr></thead>
      <tbody id="deviceRows"><tr><td colspan="4" class="p-3 text-slate-500">Memuat status...</td></tr></tbody>
    </table></div>
  </section>

  <section class="bg-white border rounded-xl p-5 space-y-3">
    <h2 class="text-lg font-bold">Diagnostik ESP32 (Debug / Release)</h2>
    <p class="text-sm text-slate-600">Event yang dikirim perangkat lewat HMAC. Tanpa UID, password Wi-Fi, atau secret. Maksimal 15 entri dalam 24 jam.</p>
    <div class="overflow-x-auto"><table class="w-full text-sm">
      <thead class="bg-slate-100"><tr><th class="text-left p-2">Waktu</th><th class="text-left p-2">Reader</th><th class="text-left p-2">Event</th><th class="text-left p-2">HTTP / RSSI</th></tr></thead>
      <tbody id="diagnosticRows"><tr><td colspan="4" class="p-3 text-slate-500">Belum ada event...</td></tr></tbody>
    </table></div>
  </section>

  <section class="bg-white border rounded-xl p-5 space-y-4">
    <div class="flex flex-wrap items-center gap-3 justify-between">
      <label class="flex gap-2 items-center text-sm" for="readerFilter">
        <span class="font-semibold">Reader</span>
        <select id="readerFilter" class="rounded-lg border p-2">
          <option value="">Semua reader</option>
          @foreach($readers as $reader)
          <option value="{{ $reader->id }}">{{ $reader->name }} ({{ $reader->serial }}) {{ $reader->active ? '' : '[Nonaktif]' }}</option>
          @endforeach
        </select>
      </label>
      <div id="readerStatus" role="status" aria-live="polite" class="text-sm text-slate-600">Memuat event...</div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-100"><tr>
          <th scope="col" class="text-left p-3">Waktu</th><th scope="col" class="text-left p-3">Reader</th>
          <th scope="col" class="text-left p-3">Kartu</th><th scope="col" class="text-left p-3">Hasil identifikasi</th><th scope="col" class="text-left p-3">Tindakan</th>
        </tr></thead>
        <tbody id="eventRows"><tr><td colspan="5" class="text-slate-500 p-4 text-center">Belum ada data...</td></tr></tbody>
      </table>
    </div>
    <p class="text-xs text-slate-600">Maksimal 30 event ditampilkan, hasil dari 1 jam terakhir. Tap berulang pada reader yang sama ditahan selama 8 detik. Nama siswa hanya ditampilkan pada halaman admin yang telah login.</p>
  </section>
</div>
@endsection
@push('scripts')
<script>
(() => {
  'use strict';
  const endpoint = @json(route('admin.rfid.events'));
  const indexUrl = @json(route('admin.rfid.index'));
  const tbody = document.getElementById('eventRows');
  const devices = document.getElementById('deviceRows');
  const status = document.getElementById('readerStatus');
  const filter = document.getElementById('readerFilter');
  let cursor = 0;
  let busy = false;
  let consecutiveErrors = 0;
  let eventIds = new Set();

  const cell = (row, value) => {
    const td = document.createElement('td');
    td.className = 'p-3 border-t align-top';
    td.textContent = value;
    row.appendChild(td);
    return td;
  };

  function appendEvent(event) {
    if (eventIds.has(event.id)) return;
    eventIds.add(event.id);
    const tr = document.createElement('tr');
    const label = event.outcome === 'identified' ? 'Teridentifikasi' :
      (event.outcome === 'enrolled' ? 'Kartu didaftarkan' : 'Tidak dikenal / tidak aktif');
    cell(tr, event.occurred_at);
    cell(tr, event.reader_name);
    cell(tr, '****' + (event.uid_last4 || '????'));
    cell(tr, event.member_name ? (label + ' — ' + event.member_name + ' (ID ' + event.member_id + ')') : label);
    const actionTd = cell(tr, event.can_enroll ? '' : 'Verifikasi manual');
    if (event.can_enroll) {
      const link = document.createElement('a');
      link.href = indexUrl;
      link.textContent = 'Pasangkan kartu';
      link.className = 'text-blue-700 underline font-semibold';
      actionTd.appendChild(link);
    }
    if (tbody.firstElementChild && tbody.firstElementChild.children.length === 1) tbody.replaceChildren();
    tbody.prepend(tr);
    while (tbody.children.length > 30) tbody.lastElementChild.remove();
  }

  function renderReaders(items) {
    devices.replaceChildren();
    if (!items || !items.length) {
      const tr = document.createElement('tr');
      const td = cell(tr, 'Belum ada perangkat terdaftar');
      td.colSpan = 4;
      devices.appendChild(tr);
      return;
    }
    const labels = {online:'Online', offline:'Offline / belum terhubung',
      reader_error:'ESP32 online, RC522 bermasalah', disabled:'Dinonaktifkan'};
    for (const item of items) {
      const tr = document.createElement('tr');
      cell(tr, item.name + ' (' + item.serial + ')');
      cell(tr, labels[item.status] || 'Status tidak diketahui');
      cell(tr, (item.firmware || '-') + ' / ' + (item.wifi_rssi == null ? '-' : item.wifi_rssi + ' dBm'));
      cell(tr, item.last_seen_at || 'Belum pernah');
      devices.appendChild(tr);
    }
  }

  function renderDiagnostics(items) {
    const target = document.getElementById('diagnosticRows');
    target.replaceChildren();
    if (!items || !items.length) {
      const tr = document.createElement('tr');
      const td = cell(tr, 'Belum ada diagnostik dalam 24 jam');
      td.colSpan = 4;
      target.appendChild(tr);
      return;
    }
    for (const item of items) {
      const tr = document.createElement('tr');
      cell(tr, item.occurred_at);
      cell(tr, item.reader_name);
      cell(tr, item.event + ' (' + item.firmware + ')');
      cell(tr, (item.http_status || '-') + ' / ' +
        (item.wifi_rssi == null ? '-' : item.wifi_rssi + ' dBm'));
      target.appendChild(tr);
    }
  }

  async function poll() {
    if (busy || document.hidden) return;
    busy = true;
    try {
      const params = new URLSearchParams({after_id:String(cursor)});
      if (filter.value) params.set('reader_id', filter.value);
      const response = await fetch(endpoint + '?' + params.toString(), {
        credentials: 'same-origin',
        headers: {'Accept':'application/json'}, cache:'no-store'
      });
      if (response.status === 401 || response.status === 403) {
        status.textContent = 'Sesi berakhir atau akses ditolak. Silakan login ulang.';
        return;
      }
      if (!response.ok) throw new Error('HTTP ' + response.status);
      const data = await response.json();
      renderReaders(data.readers || []);
      renderDiagnostics(data.diagnostics || []);
      for (const event of data.events || []) {
        appendEvent(event);
        cursor = Math.max(cursor, Number(event.id));
      }
      consecutiveErrors = 0;
      status.textContent = 'Terhubung — ' + new Date().toLocaleTimeString('id-ID');
    } catch (error) {
      consecutiveErrors++;
      status.textContent = 'Koneksi reader monitor terganggu (' + error.message + '). Mencoba lagi...';
    } finally {
      busy = false;
    }
  }

  filter.addEventListener('change', () => {
    cursor = 0;
    eventIds = new Set();
    tbody.replaceChildren();
    poll();
  });
  poll();
  // Polling sederhana, tanpa WebSocket atau paket frontend tambahan.
  const ticker = setInterval(() => { if (consecutiveErrors < 6 || !document.hidden) poll(); }, 3000);
  window.addEventListener('pagehide', () => clearInterval(ticker), {once:true});
})();
</script>
@endpush
