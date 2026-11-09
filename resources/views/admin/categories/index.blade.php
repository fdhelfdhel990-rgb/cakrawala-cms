@extends('layouts.admin')

@section('title', 'Kategori & Minat Baca')
@section('breadcrumb_title', 'Join Us')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#0B132B] uppercase tracking-wider mb-1">MANAJEMEN KONTEN</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Kategori & Label Minat Baca</h1>
            <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Kelola kategori koleksi, genre buku, dan label filter minat baca komunitas (Anak, Fiksi, Ilmiah, Sains, dll).</p>
        </div>
        
        <span class="text-xs text-[#64748B] font-medium px-4 py-2 bg-white rounded-xl border border-[#E2E8F0] shadow-2xs self-start sm:self-auto">
            Total: <span class="font-bold text-[#1E293B]">{{ $categories->total() }}</span> Kategori
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Card 1: Formulir Tambah Kategori -->
        <div class="card-system p-6 sm:p-7 h-fit">
            <h2 class="text-base font-bold text-[#1E293B] mb-1">Tambah Kategori Baru</h2>
            <p class="text-xs text-[#64748B] mb-5">Label kategori baru akan langsung muncul pada filter pencarian aplikasi mobile.</p>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-bold text-[#1E293B] mb-1.5">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Fiksi, Sains, Teknologi, Sejarah"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
                </div>

                <div>
                    <label for="icon" class="block text-xs font-bold text-[#1E293B] mb-1.5">Tag Ikon</label>
                    <input type="text" id="icon" name="icon" value="{{ old('icon', 'book-open') }}" placeholder="book-open, sparkles, globe..."
                           class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-[#1E293B] mb-1.5">Deskripsi Singkat</label>
                    <textarea id="description" name="description" rows="3" placeholder="Deskripsi singkat kategori ini..."
                              class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white leading-relaxed font-medium">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                    Simpan Kategori
                </button>
            </form>
        </div>

        <!-- Card 2: Tabel Daftar Kategori -->
        <div class="lg:col-span-2 card-system p-6 sm:p-7 space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-[#1E293B]">Daftar Kategori Aktif</h2>
                    <p class="text-xs text-[#64748B] mt-0.5">Klasifikasi buku dalam digital library.</p>
                </div>
                <span class="text-xs text-[#64748B] font-medium">
                    {{ $categories->total() }} genre
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#F8FAFC] text-[#64748B] font-bold uppercase tracking-wider text-[11px] rounded-xl">
                            <th class="py-3 px-4 rounded-l-xl">KATEGORI</th>
                            <th class="py-3 px-4">KODE LABEL</th>
                            <th class="py-3 px-4 text-center">JUMLAH BUKU</th>
                            <th class="py-3 px-4 text-center rounded-r-xl">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 font-bold text-[#1E293B]">
                                    {{ $category->name }}
                                </td>
                                <td class="py-3.5 px-4 text-[#64748B]">
                                    <code class="px-2 py-0.5 rounded-md bg-[#F1F5F9] text-[#1E293B] text-[11px] font-semibold">{{ $category->slug }}</code>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('admin.books.index', ['category_id' => $category->id]) }}" 
                                       class="inline-block px-3 py-1 rounded-md bg-[#EFF6FF] text-[#1D4ED8] font-bold text-[11px] hover:bg-blue-100 transition">
                                        {{ $category->books_count }} Koleksi
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <button type="button" 
                                            @click="confirmDelete('{{ route('admin.categories.destroy', $category->id) }}', 'Kategori: {{ addslashes($category->name) }}')"
                                            class="text-[#DC2626] hover:text-[#991B1B] font-bold text-xs hover:underline cursor-pointer">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-[#64748B]">Belum ada kategori yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Sesuai Permintaan Sistem Desain -->
            <div class="pt-4 border-t border-slate-100">
                {{ $categories->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection
