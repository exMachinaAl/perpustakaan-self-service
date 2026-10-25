@extends('layouts.admin')
@section('title','Kartu RFID / NFC')
@section('content')
<div class="space-y-6">
  <section class="bg-white border-2 border-slate-800 rounded-xl p-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-extrabold">Kartu RFID / NFC Anggota</h1>
        <p class="text-sm text-slate-600 mt-2">Mode UID adalah identifikasi, <strong>bukan autentikasi anti-kloning</strong>. Konfirmasi kartu fisik dan identitas anggota sebelum memasangkan kartu.</p>
      </div>
      <a class="inline-flex px-4 py-2 bg-blue-700 text-white font-semibold rounded-lg" href="{{ route('admin.rfid.desk') }}">Monitor Tap RFID</a>
    </div>
    @if(session('success'))<div role="status" class="p-3 mt-4 rounded bg-green-100 text-green-900">{{ session('success') }}</div>@endif
    @if($errors->any())<div role="alert" class="p-3 mt-4 rounded bg-red-100 text-red-900"><strong>Gagal:</strong> {{ $errors->first() }}</div>@endif
  </section>

  <section class="bg-white border rounded-xl p-5 space-y-4">
    <div class="flex flex-wrap justify-between items-center gap-3">
      <div><h2 class="text-lg font-bold">Daftarkan kartu dari tap reader (direkomendasikan)</h2>
        <p class="text-sm text-slate-600">Tap kartu yang belum terdaftar, lalu refresh halaman ini. Scan hanya berlaku selama 2 menit.</p></div>
      <a href="{{ route('admin.rfid.index') }}" class="px-3 py-2 border rounded-lg text-blue-700">Refresh daftar</a>
    </div>
    @forelse($pendingScans as $scan)
      <form method="POST" action="{{ route('admin.rfid.enroll') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 border rounded-xl p-4 items-end">
        @csrf
        <input type="hidden" name="scan_event_id" value="{{ $scan->id }}" />
        <div class="md:col-span-4">
          <p class="text-sm"><strong>Reader:</strong> {{ $scan->reader_name }}</p>
          <p class="text-sm text-slate-600">UID akhir: ****{{ $scan->uid_last4 }}</p>
          <p class="text-xs text-slate-500">{{ $scan->created_at }}</p>
        </div>
        <div class="md:col-span-3">
          <label for="member-{{ $scan->id }}" class="block text-sm font-medium">ID anggota tujuan</label>
          <input id="member-{{ $scan->id }}" name="user_id" type="number" required min="1" class="w-full border rounded p-2" placeholder="Lihat menu Anggota" />
        </div>
        <label class="md:col-span-3 text-xs flex items-start gap-2">
          <input type="checkbox" name="confirmed" value="1" required class="mt-1" />
          <span>Saya telah memeriksa kartu fisik dan identitas pemilik.</span>
        </label>
        <button class="md:col-span-2 bg-blue-700 text-white rounded p-2 font-semibold" type="submit">Pasangkan</button>
      </form>
    @empty
      <div class="bg-slate-50 border border-dashed rounded p-4 text-sm text-slate-600">Belum ada hasil tap kartu tidak dikenal dalam 2 menit terakhir. Buka monitor, tap kartu, lalu refresh.</div>
    @endforelse
  </section>

  <section class="bg-white border rounded-xl p-5 space-y-3">
    <h2 class="text-lg font-bold">Pendaftaran manual (cadangan)</h2>
    <p class="text-sm text-slate-600">Hanya jika reader sedang tidak tersedia; jangan gunakan UID sebagai password atau bukti kepemilikan yang aman.</p>
    <form method="POST" action="{{ route('admin.rfid.store') }}" class="grid md:grid-cols-3 gap-3">@csrf
      <div><label for="manual-member" class="block text-sm">ID anggota</label><input id="manual-member" required type="number" min="1" name="user_id" class="w-full border rounded p-2" value="{{ old('user_id') }}"></div>
      <div><label for="manual-uid" class="block text-sm">UID Hex kartu</label><input id="manual-uid" required type="text" name="uid" autocomplete="off" class="w-full border rounded p-2" placeholder="04AABBCC"></div>
      <button type="submit" class="bg-slate-800 text-white rounded p-2 self-end">Daftarkan manual</button>
    </form>
  </section>

  <section class="bg-white border rounded-xl p-5">
    <h2 class="text-lg font-bold mb-3">Kartu yang sudah pernah terdaftar</h2>
    <div class="overflow-x-auto"><table class="min-w-full text-sm">
      <thead><tr class="bg-slate-50"><th scope="col" class="text-left p-2">Anggota</th><th scope="col" class="text-left p-2">UID akhir</th><th scope="col" class="text-left p-2">Status</th><th scope="col" class="text-left p-2">Aksi</th></tr></thead>
      <tbody>
      @forelse($cards as $card)
        <tr class="border-t"><td class="p-2">{{ $card->user->nama ?? 'Akun tidak tersedia' }} (ID {{ $card->user_id }})</td><td class="p-2">****{{ $card->uid_last4 }}</td>
          <td class="p-2">{{ $card->status }}</td><td class="p-2">
          @if($card->status === 'aktif')
            <form method="POST" action="{{ route('admin.rfid.revoke',$card) }}" onsubmit="return confirm('Cabut akses kartu ini?');">@csrf @method('PATCH')
              <button type="submit" class="text-red-700 underline">Cabut kartu</button>
            </form>
          @else <span class="text-slate-400">Tidak aktif</span> @endif
          </td></tr>
      @empty <tr><td colspan="4" class="text-center text-slate-500 p-4">Belum ada kartu.</td></tr>
      @endforelse
      </tbody>
    </table></div>
    <div class="mt-3">{{ $cards->links() }}</div>
  </section>
</div>
@endsection
