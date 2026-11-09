@extends('layouts.admin')

@section('title', 'Edit Artikel - ' . $article->title)
@section('breadcrumb_title', 'Kelola Artikel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Edit Artikel</h1>
        <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Perbarui naskah artikel atau ganti foto sampul berita.</p>
        
        <div class="mt-3">
            <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1E293B] hover:text-[#1A43BF] transition">
                <span class="material-symbols-rounded text-sm">arrow_back</span> Kembali ke Daftar Artikel
            </a>
        </div>
    </div>

    <!-- Form Sederhana -->
    <div class="card-system p-7 sm:p-9 max-w-3xl">
        <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- 1. Judul Berita / Artikel -->
            <div>
                <label for="title" class="block text-xs font-bold text-[#1E293B] mb-2">Judul Artikel / Berita <span class="text-rose-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $article->title) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
            </div>

            <!-- 2. Tanggal Publikasi & Penulis -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="published_at" class="block text-xs font-bold text-[#1E293B] mb-2">Tanggal Berita <span class="text-rose-500">*</span></label>
                    <input type="date" id="published_at" name="published_at" value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d') : '') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
                </div>

                <div>
                    <label for="author" class="block text-xs font-bold text-[#1E293B] mb-2">Penulis / Kontributor</label>
                    <input type="text" id="author" name="author" value="{{ old('author', $article->author) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
                </div>
            </div>

            <!-- 3. Sampul Gambar -->
            <div>
                <label class="block text-xs font-bold text-[#1E293B] mb-2">Sampul Gambar Berita</label>
                <div class="bg-[#F0F4FA] border border-[#CBD5E1] rounded-2xl p-6">
                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        @if($article->image_url)
                            <div class="relative shrink-0">
                                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" 
                                     class="w-24 h-16 object-cover rounded-xl border border-slate-200 shadow-sm shrink-0"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="hidden w-24 h-16 rounded-xl bg-[#FFF7ED] text-[#C2410C] font-bold text-xs items-center justify-center border border-slate-200">
                                    <span class="material-symbols-rounded text-lg text-[#C2410C]">article</span>
                                </div>
                            </div>
                        @endif
                        <div class="flex-1 text-center sm:text-left space-y-1">
                            <p class="text-xs font-bold text-[#1E293B]">Ganti Foto Sampul</p>
                            <p class="text-[11px] text-[#64748B]">Pilih foto baru dari perangkat jika ingin mengganti gambar sampul.</p>
                            <input type="file" name="image_file" accept="image/*"
                                   class="mt-2 text-xs text-[#64748B] file:mr-3 file:py-1.5 file:px-4 file:rounded-[8px] file:border-0 file:text-xs file:font-bold file:bg-[#1A43BF] file:text-white hover:file:bg-[#15379e] transition cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Isi Berita -->
            <div>
                <label for="content" class="block text-xs font-bold text-[#1E293B] mb-2">Isi Berita / Artikel <span class="text-rose-500">*</span></label>
                <textarea id="content" name="content" rows="10" required
                          class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white leading-relaxed font-medium">{{ old('content', $article->content) }}</textarea>
            </div>

            <!-- Tombol Batal & Simpan Perubahan -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.articles.index') }}" 
                   class="px-5 py-2.5 rounded-[8px] border border-slate-300 text-[#1E293B] text-xs font-bold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                    <span class="material-symbols-rounded text-base">save</span>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
