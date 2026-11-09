@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header Dashboard Sederhana -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#0B132B] uppercase tracking-wider mb-1">
                RINGKASAN KOMUNITAS
            </p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Dashboard Perpustakaan</h1>
            <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Ringkasan 4 data utama perpustakaan digital EPerpus Cakrawala.</p>
        </div>
        
        <a href="{{ route('admin.books.create') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer self-start sm:self-auto">
            <span class="material-symbols-rounded text-lg">add</span>
            <span>Tambah Buku Baru</span>
        </a>
    </div>

    <!-- 4 Angka Ringkasan Sesuai Instruksi (Total Buku, Total Artikel, Banner Aktif, Total Ulasan) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Buku -->
        <div class="card-system p-6 flex flex-col justify-between hover:border-[#1A43BF]/30 transition">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="w-8 h-8 rounded-lg bg-[#EFF6FF] text-[#1A43BF] font-bold text-xs flex items-center justify-center">01</span>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-[#EFF6FF] text-[#1A43BF]">Koleksi</span>
                </div>
                <p class="text-xs font-medium text-[#64748B]">Total Buku</p>
                <h3 class="text-3xl font-extrabold text-[#1E293B] my-1">{{ $totalBooks }}</h3>
            </div>
            <a href="{{ route('admin.books.index') }}" class="text-xs font-semibold text-[#1A43BF] hover:underline mt-4 inline-flex items-center gap-1">
                <span>Kelola Buku</span>
                <span class="material-symbols-rounded text-sm">arrow_forward</span>
            </a>
        </div>

        <!-- 2. Total Artikel -->
        <div class="card-system p-6 flex flex-col justify-between hover:border-orange-200 transition">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="w-8 h-8 rounded-lg bg-[#FFF7ED] text-[#C2410C] font-bold text-xs flex items-center justify-center">02</span>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-[#FFF7ED] text-[#C2410C]">Publikasi</span>
                </div>
                <p class="text-xs font-medium text-[#64748B]">Total Artikel</p>
                <h3 class="text-3xl font-extrabold text-[#1E293B] my-1">{{ $totalArticles }}</h3>
            </div>
            <a href="{{ route('admin.articles.index') }}" class="text-xs font-semibold text-[#1A43BF] hover:underline mt-4 inline-flex items-center gap-1">
                <span>Kelola Artikel</span>
                <span class="material-symbols-rounded text-sm">arrow_forward</span>
            </a>
        </div>

        <!-- 3. Banner Aktif -->
        <div class="card-system p-6 flex flex-col justify-between hover:border-emerald-200 transition">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="w-8 h-8 rounded-lg bg-[#ECFDF5] text-[#047857] font-bold text-xs flex items-center justify-center">03</span>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-[#ECFDF5] text-[#047857]">Slide HP</span>
                </div>
                <p class="text-xs font-medium text-[#64748B]">Banner Aktif</p>
                <h3 class="text-3xl font-extrabold text-[#1E293B] my-1">{{ $totalBanners }}</h3>
            </div>
            <a href="{{ route('admin.banners.index') }}" class="text-xs font-semibold text-[#1A43BF] hover:underline mt-4 inline-flex items-center gap-1">
                <span>Kelola Banner</span>
                <span class="material-symbols-rounded text-sm">arrow_forward</span>
            </a>
        </div>

        <!-- 4. Total Ulasan -->
        <div class="card-system p-6 flex flex-col justify-between hover:border-purple-200 transition">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="w-8 h-8 rounded-lg bg-[#F3E8FF] text-[#6D28D9] font-bold text-xs flex items-center justify-center">04</span>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-[#F3E8FF] text-[#6D28D9]">Rating</span>
                </div>
                <p class="text-xs font-medium text-[#64748B]">Total Ulasan</p>
                <h3 class="text-3xl font-extrabold text-[#1E293B] my-1">{{ $totalReviews }}</h3>
            </div>
            <a href="{{ route('admin.reviews.index') }}" class="text-xs font-semibold text-[#1A43BF] hover:underline mt-4 inline-flex items-center gap-1">
                <span>Kelola Ulasan</span>
                <span class="material-symbols-rounded text-sm">arrow_forward</span>
            </a>
        </div>
    </div>

    <!-- 2 Widget Ringkas Bawah: Buku Populer & Ulasan Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Widget 1: Buku Populer -->
        <div class="card-system p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-[#1E293B]">Buku Populer Dibaca</h2>
                    <p class="text-xs text-[#64748B] mt-0.5">Koleksi buku favorit anggota komunitas literasi.</p>
                </div>
                <a href="{{ route('admin.books.index') }}" class="text-xs font-semibold text-[#1A43BF] hover:underline inline-flex items-center gap-1">
                    <span>Lihat semua buku</span>
                    <span class="material-symbols-rounded text-sm">arrow_forward</span>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($popularBooks as $book)
                    <div class="flex items-center justify-between p-3 rounded-xl border border-[#E2E8F0] hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($book->cover_image && !Str::contains($book->cover_image, 'placeholder'))
                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" 
                                     class="w-10 h-13 object-cover rounded-lg border border-slate-200 shrink-0"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="hidden w-10 h-12 rounded-lg bg-[#EFF6FF] text-[#1A43BF] font-bold text-xs items-center justify-center shrink-0">
                                    {{ strtoupper(substr($book->title, 0, 1)) }}
                                </div>
                            @else
                                <div class="w-10 h-12 rounded-lg bg-[#EFF6FF] text-[#1A43BF] font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($book->title, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-[#1E293B] truncate">{{ $book->title }}</h4>
                                <p class="text-[11px] text-[#64748B] mt-0.5">{{ $book->author }} • {{ $book->category->name ?? 'Umum' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.books.edit', $book->id) }}" class="px-3.5 py-1.5 rounded-[8px] border border-[#1A43BF] text-[#1A43BF] hover:bg-[#1A43BF] hover:text-white text-xs font-bold transition shrink-0 cursor-pointer inline-flex items-center gap-1">
                            <span class="material-symbols-rounded text-xs">edit</span>
                            <span>Edit</span>
                        </a>
                    </div>
                @empty
                    <p class="text-center py-6 text-xs text-[#64748B]">Belum ada data buku.</p>
                @endforelse
            </div>
        </div>

        <!-- Widget 2: Ulasan Pembaca Terbaru -->
        <div class="card-system p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-[#1E293B]">Ulasan Pembaca Terbaru</h2>
                    <p class="text-xs text-[#64748B] mt-0.5">Tanggapan dan rating bintang dari pembaca buku.</p>
                </div>
                <a href="{{ route('admin.reviews.index') }}" class="text-xs font-semibold text-[#1A43BF] hover:underline inline-flex items-center gap-1">
                    <span>Kelola semua ulasan</span>
                    <span class="material-symbols-rounded text-sm">arrow_forward</span>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentReviews as $rev)
                    <div class="p-3.5 rounded-xl border border-[#E2E8F0] hover:bg-slate-50 transition space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#1E293B]">{{ $rev->user->name ?? 'Pembaca' }}</span>
                            <span class="inline-flex items-center gap-1 font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded text-[11px]">
                                ★ {{ $rev->rating }}
                            </span>
                        </div>
                        <p class="text-xs text-[#1E293B] italic line-clamp-1">"{{ $rev->comment ?? 'Tidak ada komentar.' }}"</p>
                        <div class="flex items-center justify-between text-[11px] text-[#64748B] pt-1 border-t border-slate-100">
                            <span>Buku: {{ $rev->book ? $rev->book->title : 'Aplikasi' }}</span>
                            <a href="{{ route('admin.reviews.index') }}" class="font-bold text-[#1A43BF] hover:underline inline-flex items-center gap-0.5">
                                <span>Balas Ulasan</span>
                                <span class="material-symbols-rounded text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-6 text-xs text-[#64748B]">Belum ada ulasan yang masuk.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
