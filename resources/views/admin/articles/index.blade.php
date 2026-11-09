@extends('layouts.admin')

@section('title', 'Kelola Artikel')
@section('breadcrumb_title', 'Kelola Artikel')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#0B132B] uppercase tracking-wider mb-1">PUBLIKASI & KABAR</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Kelola Artikel</h1>
            <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Kelola penulisan artikel, berita kegiatan, dan agenda komunitas literasi.</p>
        </div>
        
        <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer self-start sm:self-auto">
            <span class="material-symbols-rounded text-lg">add</span>
            <span>Tambah Artikel</span>
        </a>
    </div>

    <!-- Card Container -->
    <div class="card-system p-6 sm:p-7 space-y-5">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <h2 class="text-base font-bold text-[#1E293B]">Daftar Artikel & Berita</h2>

            <span class="text-xs text-[#64748B] font-medium self-end sm:self-center">
                Total: <strong class="text-[#1E293B]">{{ $articles->total() }}</strong> artikel
            </span>
        </div>

        <!-- Search Bar -->
        <div class="pt-1">
            <form method="GET" action="{{ route('admin.articles.index') }}" class="relative w-full sm:w-96">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul artikel atau berita..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] focus:border-transparent transition bg-white font-medium">
                <span class="material-symbols-rounded text-slate-400 absolute left-3.5 top-2.5 text-lg">search</span>
                @if(request()->filled('q'))
                    <a href="{{ route('admin.articles.index') }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-rounded text-base">close</span>
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Artikel Sederhana: SAMPUL, JUDUL ARTIKEL, PENULIS, TANGGAL, AKSI -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#F8FAFC] text-[#64748B] font-bold uppercase tracking-wider text-[11px] rounded-xl">
                        <th class="py-3 px-4 rounded-l-xl">SAMPUL</th>
                        <th class="py-3 px-4">JUDUL ARTIKEL</th>
                        <th class="py-3 px-4">PENULIS</th>
                        <th class="py-3 px-4">TANGGAL</th>
                        <th class="py-3 px-4 text-center rounded-r-xl">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($articles as $art)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Sampul -->
                            <td class="py-3.5 px-4">
                                @if($art->image_url)
                                    <div class="w-12 h-9 relative shrink-0">
                                        <img src="{{ $art->image_url }}" alt="{{ $art->title }}" 
                                             class="w-12 h-9 object-cover rounded-lg border border-slate-200 shadow-2xs"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="hidden w-12 h-9 rounded-lg bg-[#FFF7ED] text-[#C2410C] font-bold text-xs items-center justify-center border border-slate-200">
                                            <span class="material-symbols-rounded text-lg text-[#C2410C]">article</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="w-12 h-9 rounded-lg bg-[#FFF7ED] text-[#C2410C] font-bold text-xs flex items-center justify-center border border-slate-200">
                                        <span class="material-symbols-rounded text-lg text-[#C2410C]">article</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Judul -->
                            <td class="py-3.5 px-4 font-bold text-[#1E293B] max-w-sm">
                                <a href="{{ route('admin.articles.edit', $art->id) }}" class="hover:text-[#1A43BF] transition line-clamp-2">
                                    {{ $art->title }}
                                </a>
                                @if($art->excerpt)
                                    <p class="text-[11px] text-[#64748B] font-normal line-clamp-1 mt-0.5">{{ $art->excerpt }}</p>
                                @endif
                            </td>

                            <!-- Penulis -->
                            <td class="py-3.5 px-4 text-[#1E293B]">
                                {{ $art->author }}
                            </td>

                            <!-- Tanggal -->
                            <td class="py-3.5 px-4 text-[#64748B]">
                                {{ $art->published_at ? $art->published_at->format('d M Y') : '-' }}
                            </td>

                            <!-- Aksi: [ Edit ] [ Hapus ] -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.articles.edit', $art->id) }}" 
                                       class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-[8px] border border-[#1A43BF] text-[#1A43BF] hover:bg-[#1A43BF] hover:text-white font-bold text-xs transition cursor-pointer">
                                        <span class="material-symbols-rounded text-xs">edit</span>
                                        <span>Edit</span>
                                    </a>

                                    <button type="button" 
                                            @click="confirmDelete('{{ route('admin.articles.destroy', $art->id) }}', 'Artikel: {{ addslashes($art->title) }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-[8px] text-rose-600 hover:text-rose-700 hover:bg-rose-50 font-bold text-xs transition cursor-pointer">
                                        <span class="material-symbols-rounded text-xs">delete</span>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-[#64748B]">
                                Belum ada artikel yang dibuat. Klik tombol <strong>+ Tambah Artikel</strong> untuk menulis berita baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span class="text-xs text-[#64748B]">
                Menampilkan {{ $articles->count() }} dari {{ $articles->total() }} artikel
            </span>
            <div>
                {{ $articles->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection
