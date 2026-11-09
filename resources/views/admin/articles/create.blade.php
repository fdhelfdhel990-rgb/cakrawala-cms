@extends('layouts.admin')

@section('title', 'Tulis Artikel Baru')
@section('breadcrumb_title', 'Kelola Artikel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Tambah Artikel Baru</h1>
        <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Tulis berita kegiatan literasi atau wawasan bacaan untuk aplikasi mobile.</p>
        
        <div class="mt-3">
            <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1E293B] hover:text-[#1A43BF] transition">
                <span class="material-symbols-rounded text-sm">arrow_back</span> Kembali ke Daftar Artikel
            </a>
        </div>
    </div>

    <!-- Form Sederhana: Judul, Tanggal, Sampul, Isi Berita -->
    <div class="card-system p-7 sm:p-9 max-w-3xl">
        <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- 1. Judul Berita / Artikel -->
            <div>
                <label for="title" class="block text-xs font-bold text-[#1E293B] mb-2">Judul Artikel / Berita <span class="text-rose-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: Kegiatan Membaca Bersama Komunitas Remaja"
                       class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
            </div>

            <!-- 2. Tanggal Publikasi & Penulis -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="published_at" class="block text-xs font-bold text-[#1E293B] mb-2">Tanggal Berita <span class="text-rose-500">*</span></label>
                    <input type="date" id="published_at" name="published_at" value="{{ old('published_at', date('Y-m-d')) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
                </div>

                <div>
                    <label for="author" class="block text-xs font-bold text-[#1E293B] mb-2">Penulis / Kontributor</label>
                    <input type="text" id="author" name="author" value="{{ old('author', Auth::user()->name ?? 'Tim Literasi') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
                </div>
            </div>

            <!-- 3. Sampul Gambar -->
            <div>
                <label class="block text-xs font-bold text-[#1E293B] mb-2">Sampul Gambar Berita</label>
                <div class="bg-[#F0F4FA] border border-[#CBD5E1] rounded-2xl p-6 text-center">
                    <div class="flex flex-col items-center justify-center space-y-1">
                        <span class="material-symbols-rounded text-3xl text-slate-400 mb-1">add_photo_alternate</span>
                        <p class="text-xs font-bold text-[#1E293B]">Pilih Foto Sampul Berita</p>
                        <p class="text-[11px] text-slate-500">Format JPG, PNG, atau WebP • Maksimal 4 MB</p>
                        <input type="file" id="image_file" name="image_file" accept="image/*"
                               class="mt-3 text-xs text-[#64748B] file:mr-3 file:py-1.5 file:px-4 file:rounded-[8px] file:border-0 file:text-xs file:font-bold file:bg-[#1A43BF] file:text-white hover:file:bg-[#15379e] transition cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- 4. Isi Berita -->
            <div>
                <label for="content" class="block text-xs font-bold text-[#1E293B] mb-2">Isi Berita / Artikel <span class="text-rose-500">*</span></label>
                <textarea id="content" name="content" rows="10" required placeholder="Tuliskan isi berita atau artikel selengkapnya di sini..."
                          class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white leading-relaxed font-medium">{{ old('content') }}</textarea>
            </div>

            <!-- Tombol Batal & Simpan -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.articles.index') }}" 
                   class="px-5 py-2.5 rounded-[8px] border border-slate-300 text-[#1E293B] text-xs font-bold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                    <span class="material-symbols-rounded text-base">save</span>
                    <span>Simpan Artikel</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
