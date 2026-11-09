@extends('layouts.admin')

@section('title', 'Kelola Buku')
@section('breadcrumb_title', 'Kelola Buku')

@section('content')
<div class="space-y-6">
    <!-- Header Section Sederhana -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#0B132B] uppercase tracking-wider mb-1">KATALOG BACAAN</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Kelola Buku</h1>
            <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Kelola koleksi e-book, unggah dokumen PDF, serta ketersediaan buku bagi pembaca.</p>
        </div>
        
        <!-- Tombol + Tambah Buku -->
        <a href="{{ route('admin.books.create') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer self-start sm:self-auto">
            <span class="material-symbols-rounded text-lg">add</span>
            <span>Tambah Buku</span>
        </a>
    </div>

    <!-- Card Container Katalog Buku -->
    <div class="card-system p-6 sm:p-7 space-y-5">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <h2 class="text-base font-bold text-[#1E293B]">Daftar Koleksi Buku Digital</h2>

            <span class="text-xs text-[#64748B] font-medium self-end sm:self-center">
                Total: <strong class="text-[#1E293B]">{{ $books->total() }}</strong> koleksi buku
            </span>
        </div>

        <!-- Search Bar -->
        <div class="pt-1">
            <form method="GET" action="{{ route('admin.books.index') }}" class="relative w-full sm:w-96">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul atau penulis buku..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] focus:border-transparent transition bg-white font-medium">
                <span class="material-symbols-rounded text-slate-400 absolute left-3 top-2.5 text-lg">search</span>
                @if(request()->filled('q'))
                    <a href="{{ route('admin.books.index') }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 inline-flex items-center">
                        <span class="material-symbols-rounded text-sm">close</span>
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Daftar Buku -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#F8FAFC] text-[#64748B] font-bold uppercase tracking-wider text-[11px] rounded-xl">
                        <th class="py-3 px-4 rounded-l-xl">SAMPUL</th>
                        <th class="py-3 px-4">JUDUL BUKU</th>
                        <th class="py-3 px-4">PENULIS</th>
                        <th class="py-3 px-4">KATEGORI</th>
                        <th class="py-3 px-4">DOKUMEN PDF</th>
                        <th class="py-3 px-4 text-center rounded-r-xl">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($books as $index => $book)
                        @php
                            $pastelBgs = ['bg-[#EFF6FF] text-[#1D4ED8]', 'bg-[#FFF7ED] text-[#C2410C]', 'bg-[#ECFDF5] text-[#047857]', 'bg-[#F3E8FF] text-[#6D28D9]'];
                            $pastel = $pastelBgs[$index % count($pastelBgs)];
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Sampul Cover -->
                            <td class="py-3.5 px-4">
                                @if($book->cover_image && !Str::contains($book->cover_image, 'placeholder'))
                                    <div class="w-10 h-14 relative shrink-0">
                                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" 
                                             class="w-10 h-14 object-cover rounded-lg border border-slate-200 shadow-2xs"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="hidden w-10 h-12 rounded-lg {{ $pastel }} font-bold text-sm items-center justify-center border border-slate-200/60 shadow-2xs">
                                            {{ strtoupper(substr($book->title, 0, 1)) }}
                                        </div>
                                    </div>
                                @else
                                    <div class="w-10 h-12 rounded-lg {{ $pastel }} font-bold text-sm flex items-center justify-center border border-slate-200/60 shadow-2xs">
                                        {{ strtoupper(substr($book->title, 0, 1)) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Judul Buku -->
                            <td class="py-3.5 px-4 font-bold text-[#1E293B] max-w-xs">
                                <a href="{{ route('admin.books.edit', $book->id) }}" class="hover:text-[#1A43BF] transition line-clamp-2">
                                    {{ $book->title }}
                                </a>
                                @if($book->synopsis)
                                    <p class="text-[11px] text-[#64748B] font-normal line-clamp-1 mt-0.5">{{ $book->synopsis }}</p>
                                @endif
                            </td>

                            <!-- Penulis -->
                            <td class="py-3.5 px-4 text-[#1E293B]">
                                {{ $book->author }}
                            </td>

                            <!-- Kategori -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#F1F5F9] text-[#1E293B]">
                                    {{ $book->category->name ?? 'Umum' }}
                                </span>
                            </td>

                            <!-- Status Dokumen PDF -->
                            <td class="py-3.5 px-4">
                                @if($book->file_path)
                                    <a href="{{ url('/api/books/'.$book->id.'/pdf') }}" target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[8px] bg-[#ECFDF5] text-[#047857] text-[11px] font-bold hover:bg-emerald-100 transition">
                                        <span class="material-symbols-rounded text-base text-emerald-600">picture_as_pdf</span>
                                        <span>Buka PDF</span>
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum ada file</span>
                                @endif
                            </td>

                            <!-- Aksi: [ Lihat ] [ Edit ] [ Hapus ] -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.books.edit', $book->id) }}" 
                                       class="px-3.5 py-1.5 rounded-[8px] border border-[#1A43BF] text-[#1A43BF] hover:bg-[#1A43BF] hover:text-white font-bold text-xs transition cursor-pointer inline-flex items-center gap-1">
                                        <span class="material-symbols-rounded text-xs">edit</span>
                                        <span>Edit</span>
                                    </a>

                                    <button type="button" 
                                            @click="confirmDelete('{{ route('admin.books.destroy', $book->id) }}', 'Buku: {{ addslashes($book->title) }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-[8px] text-rose-600 hover:text-rose-700 hover:bg-rose-50 font-bold text-xs transition cursor-pointer">
                                        <span class="material-symbols-rounded text-xs">delete</span>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-[#64748B]">
                                Belum ada buku yang ditambahkan. Silakan klik tombol <strong>+ Tambah Buku</strong> di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Card: Paginasi Sederhana -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span class="text-xs text-[#64748B]">
                Menampilkan {{ $books->count() }} dari {{ $books->total() }} koleksi buku
            </span>
            <div>
                {{ $books->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection
