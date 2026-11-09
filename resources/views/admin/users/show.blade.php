@extends('layouts.admin')

@section('title', 'Aktivitas Pengguna - ' . $user->name)
@section('breadcrumb_title', 'Join Us')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div>
        <p class="text-[11px] font-bold text-[#0B132B] uppercase tracking-wider mb-1">MANAJEMEN KONTEN</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Detail Aktivitas: {{ $user->name }}</h1>
        <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Rincian status keanggotaan, koleksi rak bacaan, dan catatan bookmark pengguna.</p>
        
        <div class="mt-3">
            <a href="{{ route('admin.join-us.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1E293B] hover:text-[#1A43BF] transition">
                <span class="material-symbols-rounded text-sm">arrow_back</span>
                <span>Kembali ke Daftar Anggota</span>
            </a>
        </div>
    </div>

    <!-- User Overview Card -->
    <div class="card-system p-6 sm:p-7 flex flex-col sm:flex-row items-center gap-6">
        <div class="w-14 h-14 rounded-2xl bg-[#EBF3FF] text-[#1A43BF] font-bold text-xl flex items-center justify-center shrink-0">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="flex-1 text-center sm:text-left">
            <h2 class="text-lg font-bold text-[#1E293B]">{{ $user->name }}</h2>
            <p class="text-xs text-[#64748B] mt-0.5">{{ $user->email }} • Terdaftar sejak {{ $user->created_at->format('d M Y') }}</p>
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-3">
                <span class="px-3 py-1 rounded-md text-[11px] font-bold bg-[#0B132B] text-white">
                    {{ ucfirst($user->role) }}
                </span>
                @if($user->membership_status === 'approved')
                    <span class="px-3 py-1 rounded-md text-[11px] font-bold bg-[#ECFDF5] text-[#047857]">
                        ✓ Anggota Terverifikasi
                    </span>
                @elseif($user->membership_status === 'rejected')
                    <span class="px-3 py-1 rounded-md text-[11px] font-bold bg-[#FFF1F2] text-[#E11D48]">
                        ✕ Ditolak
                    </span>
                @else
                    <span class="px-3 py-1 rounded-md text-[11px] font-bold bg-[#FEF3C7] text-[#D97706]">
                        ⌛ Menunggu Persetujuan
                    </span>
                @endif
                <span class="px-3 py-1 rounded-md text-[11px] font-semibold bg-[#F1F5F9] text-[#1E293B]">
                    {{ $user->bookshelves->where('type', 'reading')->count() }} Sedang Dibaca
                </span>
                <span class="px-3 py-1 rounded-md text-[11px] font-semibold bg-[#F1F5F9] text-[#1E293B]">
                    {{ $user->bookshelves->where('type', 'wishlist')->count() }} Wishlist
                </span>
                <span class="px-3 py-1 rounded-md text-[11px] font-semibold bg-[#F1F5F9] text-[#1E293B]">
                    {{ $user->bookmarks->count() }} Bookmark PDF
                </span>
            </div>
        </div>
    </div>

    <!-- Rak Saya Simulator Card -->
    <div class="card-system p-6 sm:p-7 space-y-5" x-data="{ activeTab: 'baca' }">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-[#1E293B]">Rak Saya - Aktivitas Membaca</h3>
            
            <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-semibold">
                <button type="button" @click="activeTab = 'baca'"
                        :class="activeTab === 'baca' ? 'bg-[#0B132B] text-white font-bold shadow-xs' : 'text-[#64748B] hover:text-[#1E293B]'"
                        class="px-4 py-1.5 rounded-lg transition cursor-pointer">
                    Sedang Dibaca ({{ $user->bookshelves->where('type', 'reading')->count() }})
                </button>
                <button type="button" @click="activeTab = 'wishlist'"
                        :class="activeTab === 'wishlist' ? 'bg-[#0B132B] text-white font-bold shadow-xs' : 'text-[#64748B] hover:text-[#1E293B]'"
                        class="px-4 py-1.5 rounded-lg transition cursor-pointer">
                    Wishlist ({{ $user->bookshelves->where('type', 'wishlist')->count() }})
                </button>
            </div>
        </div>

        <!-- Tab: Sedang Dibaca -->
        <div x-show="activeTab === 'baca'" class="space-y-3">
            @php
                $readingBooks = $user->bookshelves->where('type', 'reading');
            @endphp

            @forelse($readingBooks as $shelf)
                <div class="p-4 rounded-xl border border-[#E2E8F0] flex items-center gap-4 bg-white hover:bg-slate-50 transition">
                    <img src="{{ $shelf->book->cover_url ?? 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=150&q=80' }}" 
                         alt="{{ $shelf->book->title }}" 
                         class="w-12 h-16 object-cover rounded-lg border border-slate-200 shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-[#1E293B] truncate">{{ $shelf->book->title }}</h4>
                        <p class="text-[11px] text-[#64748B] mt-0.5">{{ $shelf->book->author }} • {{ $shelf->book->category->name ?? 'Umum' }}</p>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2">
                            <div class="bg-[#1A43BF] h-1.5 rounded-full" style="width: {{ max(4, $shelf->progress_percentage) }}%"></div>
                        </div>
                        <span class="text-[10px] text-[#64748B] mt-1 block">Halaman {{ $shelf->last_page_read }} / {{ $shelf->book->page_count }} ({{ $shelf->progress_percentage }}%)</span>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-[#64748B] text-xs">
                    Pengguna belum memiliki buku yang sedang dibaca.
                </div>
            @endforelse
        </div>

        <!-- Tab: Wishlist -->
        <div x-show="activeTab === 'wishlist'" style="display: none;" class="space-y-3">
            @php
                $wishlistBooks = $user->bookshelves->where('type', 'wishlist');
            @endphp

            @forelse($wishlistBooks as $shelf)
                <div class="p-4 rounded-xl border border-[#E2E8F0] flex items-center gap-4 bg-white hover:bg-slate-50 transition">
                    <img src="{{ $shelf->book->cover_url ?? 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=150&q=80' }}" 
                         alt="{{ $shelf->book->title }}" 
                         class="w-12 h-16 object-cover rounded-lg border border-slate-200 shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-[#1E293B] truncate">{{ $shelf->book->title }}</h4>
                        <p class="text-[11px] text-[#64748B] mt-0.5">{{ $shelf->book->author }}</p>
                        <span class="text-[10px] text-slate-400 mt-1 block">Disimpan pada {{ $shelf->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-[#64748B] text-xs">
                    Belum ada buku di wishlist pengguna ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Bookmark & Ulasan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Bookmark Halaman PDF -->
        <div class="card-system p-6 space-y-4">
            <h3 class="text-sm font-bold text-[#1E293B]">Penanda Halaman (Bookmarks PDF)</h3>
            <div class="space-y-3">
                @forelse($user->bookmarks as $bm)
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-slate-100 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#1E293B]">{{ $bm->book->title }}</span>
                            <span class="text-[10px] text-[#64748B]">{{ $bm->created_at->format('d M') }}</span>
                        </div>
                        <p class="text-[#1A43BF] font-semibold text-[11px] mt-1">Halaman ke-{{ $bm->page_number }}</p>
                        @if($bm->note)
                            <p class="text-[#64748B] italic mt-1 text-[11px]">"{{ $bm->note }}"</p>
                        @endif
                    </div>
                @empty
                    <div class="py-6 text-center text-[#64748B] text-xs">Belum ada penanda halaman.</div>
                @endforelse
            </div>
        </div>

        <!-- Ulasan Pengguna -->
        <div class="card-system p-6 space-y-4">
            <h3 class="text-sm font-bold text-[#1E293B]">Ulasan & Rating Dikirim</h3>
            <div class="space-y-3">
                @forelse($user->reviews as $rev)
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-slate-100 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#1E293B]">{{ $rev->book ? $rev->book->title : 'Ulasan Aplikasi' }}</span>
                            <span class="text-amber-500 font-bold">★ {{ $rev->rating }}/5</span>
                        </div>
                        <p class="text-[#1E293B] italic text-[11px]">"{{ $rev->comment }}"</p>
                        <span class="text-[10px] text-[#64748B] block">{{ $rev->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="py-6 text-center text-[#64748B] text-xs">Belum pernah mengirimkan ulasan.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
