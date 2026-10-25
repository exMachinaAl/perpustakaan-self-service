@extends('layouts.admin')

@section('title', 'Scan QR Bukti Peminjaman')

@section('content')
<div class="space-y-6">
<a href="{{ route('admin.rfid.index') }}" class="inline-block text-blue-700 underline font-bold">Kelola Kartu RFID Anggota</a>
<a href="{{ route('admin.rfid.desk') }}" class="ml-4 inline-block text-blue-700 underline font-bold">Monitor Tap RFID Real-time</a>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Scan QR Bukti Peminjaman
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Arahkan kamera ke QR Code pada bukti peminjaman user.
                </p>
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="max-w-xl mx-auto">

            <div id="reader" class="w-full overflow-hidden rounded-xl border border-slate-200"></div>

            <div id="scanResult" class="hidden mt-5 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold">
                QR berhasil dibaca. Mengalihkan ke halaman validasi...
            </div>

            <div id="scanError" class="hidden mt-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold">
                QR tidak valid.
            </div>

            <div class="mt-5 rounded-xl bg-blue-50 border border-blue-200 p-4">
                <p class="text-sm text-blue-800">
                    Pastikan QR berasal dari bukti peminjaman yang sudah disetujui admin.
                </p>
            </div>

        </div>
    </div>

</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
const resultBox=document.getElementById('scanResult');
const errorBox=document.getElementById('scanError');
let busy=false;
async function onScanSuccess(decodedText){
 if(busy)return;
 busy=true;
 try {
  const response=await fetch(@json(route('admin.scan.verify')),{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':@json(csrf_token()),'Accept':'application/json'},body:JSON.stringify({payload:decodedText})});
  const result=await response.json();
  if(!response.ok){throw new Error(result.message||'QR tidak valid');}
  resultBox.textContent='QR valid. Menampilkan detail peminjaman.';
  resultBox.classList.remove('hidden');
  errorBox.classList.add('hidden');
  await html5QrcodeScanner.clear();
  // URL dikembalikan dari API aplikasi (bukan dari QR atau input tidak tepercaya).
  window.location.assign(result.url);
 }catch(error){
  errorBox.textContent=error.message||'Gagal memeriksa QR';
  errorBox.classList.remove('hidden');resultBox.classList.add('hidden');
  busy=false;
 }
}
const html5QrcodeScanner=new Html5QrcodeScanner('reader',{fps:10,qrbox:{width:250,height:250}},false);
html5QrcodeScanner.render(onScanSuccess,()=>{});
</script>
@endsection