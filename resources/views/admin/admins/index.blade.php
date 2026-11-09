@extends('layouts.admin')

@section('title', 'Kelola Admin & Hak Akses')
@section('breadcrumb_title', 'Kelola Admin & Izin')

@section('content')
<div class="space-y-6" x-data="{ 
    addAdminModalOpen: false,
    editAdminModalOpen: false,
    editAdminData: { id: null, name: '', email: '', admin_level: 'library_admin' },
    openEdit(admin) {
        this.editAdminData = { ...admin };
        this.editAdminModalOpen = true;
    }
}">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#0B132B] uppercase tracking-wider mb-1">PENGATURAN AKSES</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Kelola Admin & Hak Akses</h1>
            <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Atur wewenang pengelola platform: Super Admin, Admin Perpustakaan, dan Editor Artikel.</p>
        </div>
        
        <!-- Tombol Tambah Admin -->
        <button type="button" 
                @click="addAdminModalOpen = true"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer self-start sm:self-auto">
            <span class="text-sm font-bold leading-none">+</span>
            <span>Tambah Pengelola Baru</span>
        </button>
    </div>

    <!-- 3 Stat Cards Hak Akses -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="card-system p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-[#64748B]">Super Admin</p>
                <h3 class="text-2xl font-extrabold text-[#0B132B] my-0.5">{{ $stats['super_admin'] }}</h3>
                <span class="text-[11px] font-semibold text-[#1A43BF]">Akses penuh sistem</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#EBF3FF] text-[#1A43BF] flex items-center justify-center font-bold text-xs">
                SA
            </div>
        </div>

        <div class="card-system p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-[#64748B]">Admin Perpustakaan</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 my-0.5">{{ $stats['library_admin'] }}</h3>
                <span class="text-[11px] font-semibold text-emerald-700">Katalog buku & anggota</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                AP
            </div>
        </div>

        <div class="card-system p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-[#64748B]">Editor Artikel</p>
                <h3 class="text-2xl font-extrabold text-amber-600 my-0.5">{{ $stats['article_editor'] }}</h3>
                <span class="text-[11px] font-semibold text-amber-700">Publikasi & berita literasi</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                EA
            </div>
        </div>
    </div>

    <!-- Card Tabel Admin -->
    <div class="card-system p-6 sm:p-7 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-[#1E293B]">Daftar Pengelola Terdaftar</h2>
                <p class="text-xs text-[#64748B] mt-0.5">Kelola kredensial dan tingkat izin masing-masing staf komunitas.</p>
            </div>

            <!-- Filter Level -->
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.admins.index') }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('level') ? 'bg-[#0B132B] text-white' : 'bg-slate-100 text-[#64748B] hover:bg-slate-200' }}">
                    Semua
                </a>
                <a href="{{ route('admin.admins.index', ['level' => 'super_admin']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('level') === 'super_admin' ? 'bg-[#1A43BF] text-white' : 'bg-slate-100 text-[#64748B] hover:bg-slate-200' }}">
                    Super Admin
                </a>
                <a href="{{ route('admin.admins.index', ['level' => 'library_admin']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('level') === 'library_admin' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-[#64748B] hover:bg-slate-200' }}">
                    Admin Perpus
                </a>
                <a href="{{ route('admin.admins.index', ['level' => 'article_editor']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('level') === 'article_editor' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-[#64748B] hover:bg-slate-200' }}">
                    Editor Artikel
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
            <form method="GET" action="{{ route('admin.admins.index') }}" class="relative w-full sm:w-96">
                @if(request('level'))
                    <input type="hidden" name="level" value="{{ request('level') }}">
                @endif
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email admin..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] focus:border-transparent transition bg-white font-medium">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                @if(request()->filled('q'))
                    <a href="{{ route('admin.admins.index', request('level') ? ['level' => request('level')] : []) }}" class="absolute right-3 top-2.5 text-xs text-slate-400 hover:text-slate-600 font-bold">&times;</a>
                @endif
            </form>

            <span class="text-xs text-[#64748B]">
                {{ $admins->total() }} admin terdaftar
            </span>
        </div>

        <!-- Tabel Admin -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#F8FAFC] text-[#64748B] font-bold uppercase tracking-wider text-[11px] rounded-xl">
                        <th class="py-3 px-4 rounded-l-xl">NAMA PENGELOLA</th>
                        <th class="py-3 px-4">EMAIL</th>
                        <th class="py-3 px-4">TINGKAT AKSES</th>
                        <th class="py-3 px-4">TANGGAL DITAMBAHKAN</th>
                        <th class="py-3 px-4 text-center rounded-r-xl">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($admins as $admin)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#EBF3FF] text-[#1A43BF] font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-[#1E293B]">{{ $admin->name }}</span>
                                    @if($admin->id === Auth::id())
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#EFF6FF] text-[#1D4ED8]">Anda</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-[#64748B]">
                                {{ $admin->email }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($admin->admin_level === 'super_admin')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#EFF6FF] text-[#1D4ED8]">
                                        Super Admin
                                    </span>
                                @elseif($admin->admin_level === 'library_admin')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#ECFDF5] text-[#047857]">
                                        Admin Perpustakaan
                                    </span>
                                @elseif($admin->admin_level === 'article_editor')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#FFF7ED] text-[#C2410C]">
                                        Editor Artikel
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#F1F5F9] text-[#64748B]">
                                        Pengelola
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-[#64748B]">
                                {{ $admin->created_at ? $admin->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <!-- Edit Button -->
                                    <button type="button" 
                                            @click="openEdit({ id: {{ $admin->id }}, name: '{{ addslashes($admin->name) }}', email: '{{ addslashes($admin->email) }}', admin_level: '{{ $admin->admin_level ?? 'library_admin' }}' })"
                                            class="px-3.5 py-1.5 rounded-xl border border-[#0B132B] text-[#0B132B] font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
                                        Ubah
                                    </button>

                                    <!-- Delete Button (Except Self and Root) -->
                                    @if($admin->id !== Auth::id() && $admin->email !== 'admin@cakrawala.id')
                                        <button type="button" 
                                                @click="confirmDelete('{{ route('admin.admins.destroy', $admin->id) }}', 'Akun Pengelola: {{ addslashes($admin->name) }}')"
                                                class="text-rose-600 hover:text-rose-700 font-bold text-xs hover:underline cursor-pointer ml-1">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-[#64748B]">
                                Tidak ada akun admin yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span class="text-xs text-[#64748B]">
                Menampilkan {{ $admins->count() }} dari {{ $admins->total() }} akun
            </span>
            <div>
                {{ $admins->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah Admin Baru -->
    <div x-show="addAdminModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="addAdminModalOpen = false"></div>
            
            <div class="inline-block bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:max-w-md w-full border border-[#E2E8F0] p-6 relative z-10">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-[#1E293B]">Tambah Pengelola Komunitas</h3>
                    <button type="button" @click="addAdminModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('admin.admins.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-[#1E293B] mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Sarah Faradila"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1E293B] mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="nama@cakrawala.id"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1E293B] mb-1">Kata Sandi Awal <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1E293B] mb-1">Tingkat Hak Akses <span class="text-rose-500">*</span></label>
                        <select name="admin_level" required 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] font-medium">
                            <option value="super_admin">Super Admin (Akses Penuh)</option>
                            <option value="library_admin" selected>Admin Perpustakaan (Katalog Buku & Anggota)</option>
                            <option value="article_editor">Editor Artikel (Publikasi Berita)</option>
                        </select>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" @click="addAdminModalOpen = false" class="px-4 py-2 rounded-[8px] border border-slate-300 text-xs font-semibold text-[#1E293B] hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                            Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ubah Admin -->
    <div x-show="editAdminModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="editAdminModalOpen = false"></div>
            
            <div class="inline-block bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:max-w-md w-full border border-[#E2E8F0] p-6 relative z-10">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-[#1E293B]">Ubah Data Pengelola</h3>
                    <button type="button" @click="editAdminModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form :action="'{{ url('admin/admins') }}/' + editAdminData.id" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-[#1E293B] mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="editAdminData.name" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1E293B] mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" x-model="editAdminData.email" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1E293B] mb-1">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" minlength="8" placeholder="Biarkan kosong jika tidak diubah"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1E293B] mb-1">Tingkat Hak Akses <span class="text-rose-500">*</span></label>
                        <select name="admin_level" x-model="editAdminData.admin_level" required 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] font-medium">
                            <option value="super_admin">Super Admin (Akses Penuh)</option>
                            <option value="library_admin">Admin Perpustakaan (Katalog Buku & Anggota)</option>
                            <option value="article_editor">Editor Artikel (Publikasi Berita)</option>
                        </select>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" @click="editAdminModalOpen = false" class="px-4 py-2 rounded-[8px] border border-slate-300 text-xs font-semibold text-[#1E293B] hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
