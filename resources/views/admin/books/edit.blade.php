@extends('layouts.admin')

@section('title', 'Edit Buku - ' . $book->title)
@section('breadcrumb_title', 'Kelola Buku')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Edit Informasi Buku</h1>
        <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Perbarui data buku bacaan atau ganti sampul/file PDF.</p>
        
        <div class="mt-3">
            <a href="{{ route('admin.books.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1E293B] hover:text-[#1A43BF] transition">
                <span class="material-symbols-rounded text-sm">arrow_back</span> Kembali ke Daftar Buku
            </a>
        </div>
    </div>

    <!-- Form Sederhana -->
    <div class="card-system p-7 sm:p-9 max-w-3xl">
        @if ($errors->any())
            <div class="p-4 mb-5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                <div class="flex items-center gap-2 font-bold mb-1.5">
                    <span class="material-symbols-rounded text-base text-rose-600">error</span>
                    <span>Gagal memperbarui data buku. Periksa catatan berikut:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- 1. Judul Buku -->
            <div>
                <label for="title" class="block text-xs font-bold text-[#1E293B] mb-2">Judul Buku <span class="text-rose-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $book->title) }}" required
                       class="w-full px-4 py-3 rounded-xl border @error('title') border-rose-300 bg-rose-50/30 @else border-[#E2E8F0] bg-white @enderror text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                @error('title')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Penulis & 3. Kategori (Grid 2 Kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="author" class="block text-xs font-bold text-[#1E293B] mb-2">Penulis Buku <span class="text-rose-500">*</span></label>
                    <input type="text" id="author" name="author" value="{{ old('author', $book->author) }}" required
                           class="w-full px-4 py-3 rounded-xl border @error('author') border-rose-300 bg-rose-50/30 @else border-[#E2E8F0] bg-white @enderror text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                    @error('author')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-xs font-bold text-[#1E293B] mb-2">Kategori Buku <span class="text-rose-500">*</span></label>
                    <select id="category_id" name="category_id" required 
                            class="w-full px-4 py-3 rounded-xl border @error('category_id') border-rose-300 bg-rose-50/30 @else border-[#E2E8F0] bg-white @enderror text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>
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
                <textarea id="synopsis" name="synopsis" rows="4"
                          class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white leading-relaxed font-medium">{{ old('synopsis', $book->synopsis) }}</textarea>
            </div>

            <!-- 5. Sampul Buku -->
            <div>
                <label class="block text-xs font-bold text-[#1E293B] mb-2">Sampul Buku</label>
                <div class="bg-[#F0F4FA] border border-[#CBD5E1] rounded-2xl p-6">
                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        @if($book->cover_url)
                            <div class="relative shrink-0">
                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" 
                                     class="w-16 h-24 object-cover rounded-xl border border-slate-200 shadow-sm shrink-0"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="hidden w-16 h-24 rounded-xl bg-[#EFF6FF] text-[#1A43BF] font-bold text-xl items-center justify-center border border-blue-200 shrink-0">
                                    {{ strtoupper(substr($book->title, 0, 1)) }}
                                </div>
                            </div>
                        @endif
                        <div class="flex-1 text-center sm:text-left space-y-1">
                            <p class="text-xs font-bold text-[#1E293B]">Ganti Sampul Buku</p>
                            <p class="text-[11px] text-[#64748B]">Pilih gambar baru dari perangkat jika ingin mengganti sampul (Maks. 10 MB).</p>
                            <input type="file" name="cover_file" accept="image/png,image/jpeg,image/webp,image/*"
                                   class="mt-2 text-xs text-[#64748B] file:mr-3 file:py-1.5 file:px-4 file:rounded-[8px] file:border-0 file:text-xs file:font-bold file:bg-[#1A43BF] file:text-white hover:file:bg-[#15379e] transition cursor-pointer">
                        </div>
                    </div>
                </div>
                @error('cover_file')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 6. File Dokumen PDF -->
            <div>
                <label class="block text-xs font-bold text-[#1E293B] mb-2">Dokumen Bacaan (File PDF)</label>
                <div class="bg-white border @error('pdf_file') border-rose-300 bg-rose-50/20 @else border-[#E2E8F0] @enderror rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[#1E293B]">Status Dokumen:</span>
                            @if($book->file_path)
                                <span class="px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">PDF Tersedia</span>
                                <a href="{{ url('/api/books/'.$book->id.'/pdf') }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-[#1A43BF] hover:underline ml-2">
                                    <span>Lihat File</span>
                                    <span class="material-symbols-rounded text-sm">arrow_forward</span>
                                </a>
                            @else
                                <span class="px-2.5 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[11px] font-bold border border-rose-200">Belum Ada PDF</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-[#64748B] mt-1">Pilih file PDF baru untuk mengganti file bacaan aplikasi (Maks. 100 MB).</p>
                    </div>
                    <input type="file" name="pdf_file" accept=".pdf,application/pdf"
                           class="text-xs text-[#64748B] file:mr-3 file:py-1.5 file:px-4 file:rounded-[8px] file:border-0 file:text-xs file:font-bold file:bg-[#047857] file:text-white hover:file:bg-[#065F46] transition cursor-pointer">
                </div>
                @error('pdf_file')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Batal & Simpan Perubahan -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.books.index') }}" 
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
