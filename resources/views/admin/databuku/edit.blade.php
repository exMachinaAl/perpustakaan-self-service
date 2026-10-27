@extends('layouts.admin')

@section('title', 'Edit Buku - UMG Digilib')

@section('content')

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
    }

    .neo-panel {
        border: 2px solid #0f172a;
        box-shadow: 5px 5px 0 #0f172a;
    }

    .neo-btn {
        border: 2px solid #0f172a;
        box-shadow: 3px 3px 0 #0f172a;
        transition: all .18s ease;
    }

    .neo-btn:hover {
        transform: translate(-1px, -1px);
        box-shadow: 5px 5px 0 #0f172a;
    }

    .neo-badge {
        border: 2px solid #0f172a;
        box-shadow: 2px 2px 0 #0f172a;
    }

    .neo-input,
    .neo-select,
    .neo-textarea {
        border: 2px solid #0f172a;
        background: #ffffff;
        color: #0f172a;
        font-weight: 600;
        outline: none;
    }

    .neo-input:focus,
    .neo-select:focus,
    .neo-textarea:focus {
        box-shadow: 0 0 0 4px rgba(125, 211, 252, 0.45);
    }

    .cover-preview-box {
        border: 2px dashed #0f172a;
        background: linear-gradient(to bottom right, #f8fafc, #e0f2fe);
    }

    .cover-fixed {
        aspect-ratio: 3 / 4;
        min-height: 320px;
    }
</style>

<div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-5 py-4 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-950 transition">
            Dashboard
        </a>
        <span>/</span>
        <a href="{{ route('admin.databuku.index') }}" class="hover:text-slate-950 transition">
            Data Buku
        </a>
        <span>/</span>
        <span class="text-slate-950">Edit Buku</span>
    </div>

    {{-- Header --}}
    <div class="neo-panel rounded-2xl px-5 py-5 bg-gradient-to-br from-slate-50 via-white to-amber-50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-950 text-white text-xs font-black tracking-wide uppercase neo-badge mb-3">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    Edit Katalog
                </div>

                <h2 class="text-2xl md:text-3xl font-black text-slate-950 leading-tight">
                    Edit Buku
                </h2>

                <p class="text-sm md:text-base text-slate-600 mt-2 max-w-2xl">
                    Perbarui informasi buku, cover, kategori, stok, lokasi rak, dan sinopsis buku.
                </p>
            </div>

            <div class="flex justify-start lg:justify-end lg:ml-auto lg:pl-10">
                <a href="{{ route('admin.databuku.index') }}"
                   class="neo-btn inline-flex items-center gap-2 bg-white text-slate-950 px-4 py-2.5 rounded-xl text-sm font-black">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Kembali ke Data Buku
                </a>
            </div>
        </div>
    </div>

    {{-- Form Panel --}}
    <div class="neo-panel bg-white rounded-2xl overflow-hidden">

        <div class="px-5 py-4 border-b-2 border-slate-950 bg-amber-100">
            <h3 class="text-lg font-black text-slate-950">
                Informasi Bibliografi
            </h3>
            <p class="text-xs text-slate-600 font-bold mt-1">
                Data lama otomatis terisi. Ubah bagian yang diperlukan lalu simpan perubahan.
            </p>
        </div>

        <form action="{{ route('admin.databuku.update', $buku->id_buku) }}"
              method="POST"
              enctype="multipart/form-data"
              class="p-5 md:p-6 space-y-6">

            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                {{-- Cover --}}
                <div class="md:col-span-4 space-y-3">
                    <label class="block text-sm font-black text-slate-950">
                        Cover Buku
                    </label>

                    <label for="cover-upload"
                           class="cover-preview-box cover-fixed relative rounded-2xl flex flex-col items-center justify-center gap-4 cursor-pointer overflow-hidden hover:bg-amber-50 transition">

                        @if ($buku->cover)
                            <img id="cover-preview"
                                 src="{{ asset('storage/' . $buku->cover) }}"
                                 alt="Cover {{ $buku->judul }}"
                                 class="absolute inset-0 w-full h-full object-cover opacity-45">
                        @else
                            <img id="cover-preview"
                                 src="https://lh3.googleusercontent.com/aida-public/AB6AXuD7W_okpqu6RUSfhyt9O7Lyr4c3p0qHn71Mf5z9zpFK6eKn25cY7r4ZIU8fXirVf_jfNyCp-Ngl3ZfpqsXDS1vkzTupg2C1KinFYRhpBdaPvTTMvE4f12zFfvBOouA_OW4qz-Lm8M2zMs1end83FxQCCfTCA-cMaplA-vQaY3k53Rp5-uPjlD8c2tHnVR5cgi1BHHI_Feo1j7f4baAwuJUiKWEKguISUDnQ8kNsQgdtjv_o7kmvEx2D7kMRjFMT3CJ9FpV7KJRTTc8P"
                                 alt="Cover Placeholder"
                                 class="absolute inset-0 w-full h-full object-cover opacity-15">
                        @endif

                        <div class="relative z-10 w-14 h-14 rounded-2xl bg-white border-2 border-slate-950 flex items-center justify-center">
                            <span class="material-symbols-outlined text-slate-950 text-[28px]">
                                add_a_photo
                            </span>
                        </div>

                        <div class="text-center px-4 relative z-10">
                            <p class="text-sm font-black text-slate-950">
                                Ganti Cover
                            </p>
                            <p class="text-xs text-slate-600 font-semibold mt-1">
                                JPG, PNG, WEBP • maks. 2MB
                            </p>
                        </div>

                        <input class="hidden"
                               id="cover-upload"
                               name="cover"
                               type="file"
                               accept="image/*">
                    </label>

                    @error('cover')
                        <p class="text-rose-600 text-sm font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Form Fields --}}
                <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Judul Buku
                        </label>

                        <input name="judul"
                               value="{{ old('judul', $buku->judul) }}"
                               class="neo-input w-full px-4 py-3 rounded-xl text-sm"
                               placeholder="Masukkan judul lengkap buku..."
                               type="text">

                        @error('judul')
                            <p class="text-rose-600 text-sm font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Pengarang
                        </label>

                        <input name="pengarang"
                               value="{{ old('pengarang', $buku->pengarang) }}"
                               class="neo-input w-full px-4 py-3 rounded-xl text-sm"
                               placeholder="Nama penulis..."
                               type="text">

                        @error('pengarang')
                            <p class="text-rose-600 text-sm font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Kategori
                        </label>

                        @php
                            $kategoriList = [
                                'Sains & Teknologi',
                                'Ekonomi & Bisnis',
                                'Kedokteran',
                                'Sosial & Politik',
                                'Sastra',
                                'Teknologi Informasi',
                                'Ilmu Sosial',
                                'Psikologi',
                                'Pendidikan',
                                'Umum'
                            ];
                        @endphp

                        <select name="kategori"
                                class="neo-select w-full px-4 py-3 rounded-xl text-sm appearance-none">
                            <option disabled value="">Pilih Kategori</option>

                            @foreach ($kategoriList as $kategori)
                                <option value="{{ $kategori }}"
                                    {{ old('kategori', $buku->kategori) == $kategori ? 'selected' : '' }}>
                                    {{ $kategori }}
                                </option>
                            @endforeach
                        </select>

                        @error('kategori')
                            <p class="text-rose-600 text-sm font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Kode Katalog
                        </label>

                        <input name="kode"
                               value="{{ old('kode', $buku->kode) }}"
                               class="neo-input w-full px-4 py-3 rounded-xl text-sm"
                               placeholder="Contoh: BK-08221"
                               type="text">

                        @error('kode')
                            <p class="text-rose-600 text-sm font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">ISBN / Barcode Penerbit</label>
                        <input name="isbn" value="{{ old('isbn', $buku->isbn) }}" class="neo-input w-full px-4 py-3 rounded-xl text-sm" placeholder="Contoh: 9786020000000" type="text">
                        @error('isbn')<p class="text-rose-600 text-sm font-bold mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Tahun Terbit
                        </label>

                        <input name="tahun_terbit"
                               value="{{ old('tahun_terbit', $buku->tahun_terbit) }}"
                               class="neo-input w-full px-4 py-3 rounded-xl text-sm"
                               placeholder="2024"
                               type="number">

                        @error('tahun_terbit')
                            <p class="text-rose-600 text-sm font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Eksemplar Tersedia
                        </label>

                        <input name="stock"
                               value="{{ old('stock', $buku->stock) }}"
                               class="neo-input w-full px-4 py-3 rounded-xl text-sm"
                               type="number"
                               min="0">

                        @error('stock')
                            <p class="text-rose-600 text-sm font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Lokasi Rak
                        </label>

                        <input name="lokasi"
                               value="{{ old('lokasi', $buku->lokasi) }}"
                               class="neo-input w-full px-4 py-3 rounded-xl text-sm"
                               placeholder="Contoh: A-12-04"
                               type="text">

                        @error('lokasi')
                            <p class="text-rose-600 text-sm font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Penerbit
                        </label>

                        <input name="penerbit"
                               value="{{ old('penerbit', $buku->penerbit) }}"
                               class="neo-input w-full px-4 py-3 rounded-xl text-sm"
                               placeholder="Nama penerbit..."
                               type="text">

                        @error('penerbit')
                            <p class="text-rose-600 text-sm font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Deskripsi / Sinopsis
                        </label>

                        <textarea name="deskripsi"
                                  class="neo-textarea w-full px-4 py-3 rounded-xl text-sm resize-none"
                                  placeholder="Tuliskan ringkasan singkat buku di sini..."
                                  rows="5">{{ old('deskripsi', $buku->deskripsi ?? '') }}</textarea>

                        @error('deskripsi')
                            <p class="text-rose-600 text-sm font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-5 border-t-2 border-slate-950">
                <a href="{{ route('admin.databuku.index') }}"
                   class="neo-btn px-6 py-3 rounded-xl bg-white text-slate-950 text-sm font-black inline-flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                    Batal
                </a>

                <button class="neo-btn px-6 py-3 rounded-xl bg-slate-950 text-white text-sm font-black inline-flex items-center justify-center gap-2"
                        type="submit">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const coverInput = document.getElementById('cover-upload');
    const coverPreview = document.getElementById('cover-preview');

    if (coverInput && coverPreview) {
        coverInput.addEventListener('change', function () {
            const file = this.files[0];

            if (file) {
                coverPreview.src = URL.createObjectURL(file);
                coverPreview.classList.remove('opacity-15');
                coverPreview.classList.remove('opacity-45');
                coverPreview.classList.add('opacity-45');
            }
        });
    }
</script>
@endpush
