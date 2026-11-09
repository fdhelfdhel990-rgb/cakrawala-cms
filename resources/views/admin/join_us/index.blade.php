@extends('layouts.admin')

@section('title', 'Kelola Keanggotaan & Join Us')
@section('breadcrumb_title', 'Join Us')

@section('content')
<div class="space-y-6" x-data="{ currentTab: 'members' }">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#0B132B] uppercase tracking-wider mb-1">MANAJEMEN KONTEN</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Kelola Join Us & Keanggotaan</h1>
            <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Verifikasi pendaftaran anggota komunitas literasi remaja dan kelola minat kategori baca.</p>
        </div>
        
        <!-- Tab Navigation Buttons -->
        <div class="inline-flex p-1 rounded-xl bg-slate-100 border border-[#E2E8F0] text-xs font-semibold self-start sm:self-auto">
            <button type="button" 
                    @click="currentTab = 'members'"
                    :class="currentTab === 'members' ? 'bg-[#0B132B] text-white shadow-xs' : 'text-[#64748B] hover:text-[#1E293B]'"
                    class="px-4 py-2 rounded-lg transition cursor-pointer">
                Daftar Calon & Anggota
            </button>
            <button type="button" 
                    @click="currentTab = 'categories'"
                    :class="currentTab === 'categories' ? 'bg-[#0B132B] text-white shadow-xs' : 'text-[#64748B] hover:text-[#1E293B]'"
                    class="px-4 py-2 rounded-lg transition cursor-pointer">
                Kategori Minat Baca
            </button>
        </div>
    </div>

    <!-- 3 Stat Cards Ringkasan Keanggotaan -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <a href="{{ route('admin.join-us.index', ['status' => 'pending']) }}" 
           class="card-system p-5 flex items-center justify-between hover:border-amber-300 transition">
            <div>
                <p class="text-xs font-medium text-[#64748B]">Menunggu Persetujuan</p>
                <h3 class="text-2xl font-extrabold text-amber-600 my-0.5">{{ $totalPending }}</h3>
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-700">
                    <span>Perlu ditinjau</span>
                    <span class="material-symbols-rounded text-sm">arrow_forward</span>
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                <span class="material-symbols-rounded text-lg">hourglass_top</span>
            </div>
        </a>

        <a href="{{ route('admin.join-us.index', ['status' => 'approved']) }}" 
           class="card-system p-5 flex items-center justify-between hover:border-emerald-300 transition">
            <div>
                <p class="text-xs font-medium text-[#64748B]">Anggota Resmi Aktif</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 my-0.5">{{ $totalApproved }}</h3>
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700">
                    <span>Telah diverifikasi</span>
                    <span class="material-symbols-rounded text-sm">arrow_forward</span>
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                <span class="material-symbols-rounded text-lg">check</span>
            </div>
        </a>

        <a href="{{ route('admin.join-us.index', ['status' => 'rejected']) }}" 
           class="card-system p-5 flex items-center justify-between hover:border-rose-300 transition">
            <div>
                <p class="text-xs font-medium text-[#64748B]">Pendaftaran Ditolak</p>
                <h3 class="text-2xl font-extrabold text-rose-600 my-0.5">{{ $totalRejected }}</h3>
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-700">
                    <span>Tidak memenuhi syarat</span>
                    <span class="material-symbols-rounded text-sm">arrow_forward</span>
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm">
                <span class="material-symbols-rounded text-lg">close</span>
            </div>
        </a>
    </div>

    <!-- TAB 1: DAFTAR CALON & ANGGOTA -->
    <div x-show="currentTab === 'members'" class="card-system p-6 sm:p-7 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-[#1E293B]">Permohonan Masuk Komunitas</h2>
                <p class="text-xs text-[#64748B] mt-0.5">Kelola verifikasi pendaftar baru yang ingin bergabung ke Komunitas Literasi Remaja.</p>
            </div>

            <!-- Filter Status Badges -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.join-us.index') }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('status') ? 'bg-[#0B132B] text-white' : 'bg-slate-100 text-[#64748B] hover:bg-slate-200' }}">
                    Semua
                </a>
                <a href="{{ route('admin.join-us.index', ['status' => 'pending']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'pending' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                    Menunggu Konfirmasi ({{ $totalPending }})
                </a>
                <a href="{{ route('admin.join-us.index', ['status' => 'approved']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'approved' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    Disetujui ({{ $totalApproved }})
                </a>
                <a href="{{ route('admin.join-us.index', ['status' => 'rejected']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'rejected' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                    Ditolak ({{ $totalRejected }})
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
            <form method="GET" action="{{ route('admin.join-us.index') }}" class="relative w-full sm:w-96">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email pendaftar..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] focus:border-transparent transition bg-white font-medium">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                @if(request()->filled('q'))
                    <a href="{{ route('admin.join-us.index', request('status') ? ['status' => request('status')] : []) }}" class="absolute right-3 top-2.5 text-xs text-slate-400 hover:text-slate-600 font-bold">&times;</a>
                @endif
            </form>

            <span class="text-xs text-[#64748B]">
                {{ $members->total() }} calon/anggota terdaftar
            </span>
        </div>

        <!-- Tabel Pendaftar & Anggota -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#F8FAFC] text-[#64748B] font-bold uppercase tracking-wider text-[11px] rounded-xl">
                        <th class="py-3 px-4 rounded-l-xl">PENDAFTAR</th>
                        <th class="py-3 px-4">EMAIL</th>
                        <th class="py-3 px-4">TANGGAL DAFTAR</th>
                        <th class="py-3 px-4">STATUS KEANGGOTAAN</th>
                        <th class="py-3 px-4 text-center rounded-r-xl">AKSI PERSETUJUAN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($members as $member)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#EBF3FF] text-[#1A43BF] font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#1E293B]">{{ $member->name }}</p>
                                        <a href="{{ route('admin.users.show', $member->id) }}" class="inline-flex items-center gap-1 text-[11px] text-[#1A43BF] hover:underline">
                                            <span>Lihat detail rak baca</span>
                                            <span class="material-symbols-rounded text-sm">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-[#64748B]">
                                {{ $member->email }}
                            </td>
                            <td class="py-3.5 px-4 text-[#64748B]">
                                {{ $member->created_at ? $member->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($member->membership_status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#ECFDF5] text-[#047857]">
                                        ✓ Anggota Aktif
                                    </span>
                                @elseif($member->membership_status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#FFF1F2] text-[#E11D48]">
                                        ✕ Ditolak
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#FEF3C7] text-[#D97706]">
                                        ⌛ Menunggu Konfirmasi
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    @if($member->membership_status !== 'approved')
                                        <form action="{{ route('admin.join-us.approve', $member->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs cursor-pointer flex items-center gap-1">
                                                <span>✓</span> Setujui
                                            </button>
                                        </form>
                                    @endif

                                    @if($member->membership_status !== 'rejected')
                                        <form action="{{ route('admin.join-us.reject', $member->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1.5 rounded-lg border border-rose-300 text-rose-600 hover:bg-rose-50 font-bold text-xs transition cursor-pointer flex items-center gap-1">
                                                <span>✕</span> Tolak
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-[#64748B]">
                                Tidak ada pendaftar atau anggota pada kriteria ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span class="text-xs text-[#64748B]">
                Menampilkan {{ $members->count() }} dari {{ $members->total() }} anggota
            </span>
            <div>
                {{ $members->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>

    <!-- TAB 2: KATEGORI & LABEL MINAT BACA (Sesuai Layar Pencarian Mobile) -->
    <div x-show="currentTab === 'categories'" class="card-system p-6 sm:p-7 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-[#1E293B]">Kategori & Label Minat Komunitas</h2>
                <p class="text-xs text-[#64748B] mt-0.5">Label kategori ini tampil di menu pencarian dan filter aplikasi mobile pengguna (Anak, Fiksi, Ilmiah, Sains, dll).</p>
            </div>

            <!-- Tombol Tambah Kategori -->
            <button type="button" 
                    @click="$dispatch('open-add-category-modal')"
                    class="px-4 py-2 rounded-xl bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold transition shadow-xs cursor-pointer self-start sm:self-auto">
                + Tambah Kategori Baru
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($categories as $cat)
                <div class="p-4 rounded-xl border border-[#E2E8F0] bg-white flex items-center justify-between hover:shadow-xs transition">
                    <div>
                        <h4 class="text-sm font-bold text-[#1E293B]">{{ $cat->name }}</h4>
                        <p class="text-[11px] text-[#64748B] mt-0.5">{{ $cat->books_count }} koleksi buku</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-[#F1F5F9] text-[#1E293B]">
                        {{ $cat->slug }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
