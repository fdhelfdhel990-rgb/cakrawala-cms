@extends('layouts.admin')

@section('title', 'Pemantauan Anggota & Aktivitas')
@section('breadcrumb_title', 'Join Us')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#0B132B] uppercase tracking-wider mb-1">MANAJEMEN KONTEN</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Pemantauan Anggota & Aktivitas Baca</h1>
            <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Pantau keanggotaan pembaca, aktivitas rak bacaan, dan progres membaca anggota komunitas.</p>
        </div>
        
        <span class="text-xs text-[#64748B] font-medium px-4 py-2 bg-white rounded-xl border border-[#E2E8F0] shadow-2xs self-start sm:self-auto">
            Total: <span class="font-bold text-[#1E293B]">{{ $users->total() }}</span> Akun
        </span>
    </div>

    <!-- Card Container -->
    <div class="card-system p-6 sm:p-7 space-y-5">
        <h2 class="text-base font-bold text-[#1E293B]">Daftar akun anggota komunitas</h2>

        <!-- Search Bar & Filter -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto flex-1">
                <div class="relative w-full sm:w-80">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <select name="role" class="w-full sm:w-48 py-2.5 px-4 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] bg-white font-medium">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Anggota Komunitas</option>
                </select>

                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-[#1A43BF] hover:bg-[#15379e] text-white rounded-[8px] text-xs font-bold transition shadow-xs cursor-pointer">
                    Filter
                </button>
            </form>

            <span class="text-xs text-[#64748B]">
                {{ $users->total() }} akun terdaftar
            </span>
        </div>

        <!-- Tabel Akun -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#F8FAFC] text-[#64748B] font-bold uppercase tracking-wider text-[11px] rounded-xl">
                        <th class="py-3 px-4 rounded-l-xl">PENGGUNA</th>
                        <th class="py-3 px-4">PERAN</th>
                        <th class="py-3 px-4">SEDANG DIBACA</th>
                        <th class="py-3 px-4">WISHLIST</th>
                        <th class="py-3 px-4">ULASAN</th>
                        <th class="py-3 px-4">TERDAFTAR</th>
                        <th class="py-3 px-4 text-center rounded-r-xl">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#EBF3FF] text-[#1A43BF] font-bold flex items-center justify-center shrink-0 text-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#1E293B] text-xs">{{ $user->name }}</p>
                                        <p class="text-[11px] text-[#64748B]">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                @if($user->role === 'admin')
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-[#0B132B] text-white">Administrator</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-[#ECFDF5] text-[#047857]">Anggota</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 font-bold text-[#1E293B]">
                                {{ $user->reading_count ?? 0 }} Buku
                            </td>

                            <td class="py-3.5 px-4 font-bold text-[#1E293B]">
                                {{ $user->wishlist_count ?? 0 }} Buku
                            </td>

                            <td class="py-3.5 px-4 font-bold text-[#1E293B]">
                                {{ $user->reviews_count ?? 0 }} Ulasan
                            </td>

                            <td class="py-3.5 px-4 text-[#64748B]">
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.users.show', $user->id) }}" 
                                   class="px-4 py-1.5 rounded-xl border border-[#0B132B] text-[#0B132B] font-bold text-xs hover:bg-slate-50 transition">
                                    Detail Rak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-[#64748B]">
                                Tidak ada akun yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span class="text-xs text-[#64748B]">
                {{ $users->total() }} akun terdaftar
            </span>
            <div>
                {{ $users->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection
