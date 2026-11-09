@extends('layouts.admin')

@section('title', 'Tambah Buku')
@section('breadcrumb_title', 'Kelola Buku')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Tambah Buku Baru</h1>
        <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Lengkapi formulir sederhana berikut untuk menambahkan buku ke aplikasi.</p>
        
        <div class="mt-3">
            <a href="{{ route('admin.books.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1E293B] hover:text-[#1A43BF] transition">
                <span class="material-symbols-rounded text-sm">arrow_back</span>
                <span>Kembali ke Daftar Buku</span>
            </a>
        </div>
    </div>

    <!-- Form Sederhana: Judul, Penulis, Kategori, Sinopsis, Sampul, File PDF -->
    <div class="card-system p-7 sm:p-9 max-w-3xl">
        @if ($errors->any())
            <div class="p-4 mb-5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                <div class="flex items-center gap-2 font-bold mb-1.5">
                    <span class="material-symbols-rounded text-base text-rose-600">error</span>
                    <span>Gagal menambahkan buku baru. Periksa catatan berikut:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- 1. Judul Buku -->
            <div>
                <label for="title" class="block text-xs font-bold text-[#1E293B] mb-2">Judul Buku <span class="text-rose-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: Laskar Pelangi"
                       class="w-full px-4 py-3 rounded-xl border @error('title') border-rose-300 bg-rose-50/30 @else border-[#E2E8F0] bg-white @enderror text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                @error('title')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Penulis & 3. Kategori (Grid 2 Kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="author" class="block text-xs font-bold text-[#1E293B] mb-2">Penulis Buku <span class="text-rose-500">*</span></label>
                    <input type="text" id="author" name="author" value="{{ old('author') }}" required placeholder="Contoh: Andrea Hirata"
                           class="w-full px-4 py-3 rounded-xl border @error('author') border-rose-300 bg-rose-50/30 @else border-[#E2E8F0] bg-white @enderror text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                    @error('author')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-xs font-bold text-[#1E293B] mb-2">Kategori Buku <span class="text-rose-500">*</span></label>
                    <select id="category_id" name="category_id" required 
                            class="w-full px-4 py-3 rounded-xl border @error('category_id') border-rose-300 bg-rose-50/30 @else border-[#E2E8F0] bg-white @enderror text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 4. Sinopsis Buku -->
            <div>
                <label for="synopsis" class="block text-xs font-bold text-[#1E293B] mb-2">Sinopsis / Ringkasan Cerita</label>
                <textarea id="synopsis" name="synopsis" rows="4" placeholder="Tuliskan ringkasan singkat isi buku agar menarik bagi pembaca..."
                          class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white leading-relaxed font-medium">{{ old('synopsis') }}</textarea>
            </div>

            <!-- 5. Sampul Buku (Cover Image) -->
            <div>
                <label class="block text-xs font-bold text-[#1E293B] mb-2">Sampul Buku (Gambar Cover)</label>
                <div class="bg-[#F0F4FA] border border-[#CBD5E1] rounded-2xl p-6 text-center">
                    <div class="flex flex-col items-center justify-center space-y-1">
                        <span class="material-symbols-rounded text-3xl text-slate-400 mb-1">add_photo_alternate</span>
                        <p class="text-xs font-bold text-[#1E293B]">Pilih File Gambar Sampul</p>
                        <p class="text-[11px] text-slate-500">Format JPG, PNG, atau WEBP • Maksimal 10 MB</p>
                        <input type="file" id="cover_file" name="cover_file" accept="image/png,image/jpeg,image/webp,image/*"
                               class="mt-3 text-xs text-[#64748B] file:mr-3 file:py-1.5 file:px-4 file:rounded-[8px] file:border-0 file:text-xs file:font-bold file:bg-[#1A43BF] file:text-white hover:file:bg-[#15379e] transition cursor-pointer">
                    </div>
                </div>
                @error('cover_file')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 6. File Dokumen PDF (Bacaan) -->
            <div>
                <label class="block text-xs font-bold text-[#1E293B] mb-2">Dokumen Bacaan (File PDF)</label>
                <div class="bg-white border @error('pdf_file') border-rose-300 bg-rose-50/20 @else border-[#E2E8F0] @enderror rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-rounded text-2xl text-emerald-600">upload_file</span>
                        <div>
                            <p class="text-xs font-bold text-[#1E293B]">Unggah File Dokumen PDF</p>
                            <p class="text-[11px] text-[#64748B]">Format .PDF • Maksimal 100 MB • Dapat dibaca langsung di HP.</p>
                        </div>
                    </div>
                    <input type="file" id="pdf_file" name="pdf_file" accept=".pdf,application/pdf"
                           class="text-xs text-[#64748B] file:mr-3 file:py-1.5 file:px-4 file:rounded-[8px] file:border-0 file:text-xs file:font-bold file:bg-[#047857] file:text-white hover:file:bg-[#065F46] transition cursor-pointer">
                </div>
                @error('pdf_file')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Batal & Simpan -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.books.index') }}" 
                   class="px-5 py-2.5 rounded-[8px] border border-slate-300 text-[#1E293B] text-xs font-bold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer inline-flex items-center gap-1.5">
                    <span class="material-symbols-rounded text-base">save</span>
                    <span>Simpan Buku</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
