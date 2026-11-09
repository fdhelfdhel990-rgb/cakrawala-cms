@extends('layouts.admin')

@section('title', 'Kelola Banner')
@section('breadcrumb_title', 'Kelola Banner')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#0B132B] uppercase tracking-wider mb-1">PROMOSI & EVENT</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Kelola Banner Slide HP</h1>
            <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Upload gambar poster dan banner promosi yang tampil pada slide atas layar utama aplikasi HP.</p>
        </div>
        
        <span class="text-xs text-[#64748B] font-medium px-4 py-2 bg-white rounded-xl border border-[#E2E8F0] shadow-2xs self-start sm:self-auto">
            Total: <strong class="text-[#1E293B]">{{ $banners->total() }}</strong> banner
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulir Upload Banner Sederhana -->
        <div class="card-system p-6 sm:p-7 h-fit">
            <h2 class="text-base font-bold text-[#1E293B] mb-1">Upload Banner Baru</h2>
            <p class="text-xs text-[#64748B] mb-5">Tambahkan poster promosi kemitraan atau acara komunitas.</p>

            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Judul Banner -->
                <div>
                    <label for="title" class="block text-xs font-bold text-[#1E293B] mb-1.5">Judul Banner <span class="text-rose-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: Festival Membaca Ramadhan"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
                </div>

                <!-- Keterangan Singkat -->
                <div>
                    <label for="subtitle" class="block text-xs font-bold text-[#1E293B] mb-1.5">Keterangan Singkat</label>
                    <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle') }}" placeholder="Contoh: Diskon pendaftaran & sertifikat"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
                </div>

                <!-- File Gambar Banner -->
                <div>
                    <label for="image_file" class="block text-xs font-bold text-[#1E293B] mb-1.5">File Gambar Banner <span class="text-rose-500">*</span></label>
                    <input type="file" id="image_file" name="image_file" accept="image/*" required
                           class="w-full text-xs text-[#64748B] file:mr-2 file:py-1.5 file:px-3 file:rounded-[8px] file:border-0 file:text-[11px] file:font-bold file:bg-[#1A43BF] file:text-white hover:file:bg-[#15379e] transition cursor-pointer">
                    <p class="text-[10px] text-slate-400 mt-1">Disarankan gambar landscape rasio 16:9</p>
                </div>

                <!-- Tipe Aksi Saat Banner Diklik -->
                <div>
                    <label for="action_type" class="block text-xs font-bold text-[#1E293B] mb-1.5">Aksi Saat Banner Diklik</label>
                    <select id="action_type" name="action_type"
                            class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
                        <option value="none" {{ old('action_type', 'none') === 'none' ? 'selected' : '' }}>Hanya Tampilan Promo (Tanpa Tautan)</option>
                        <option value="article" {{ old('action_type') === 'article' ? 'selected' : '' }}>Buka Artikel</option>
                        <option value="book" {{ old('action_type') === 'book' ? 'selected' : '' }}>Buka Detail Buku</option>
                        <option value="url" {{ old('action_type') === 'url' ? 'selected' : '' }}>Buka Tautan Web Luar</option>
                    </select>
                </div>

                <!-- Target Aksi (Opsional) -->
                <div>
                    <label for="action_target" class="block text-xs font-bold text-[#1E293B] mb-1.5">Target Aksi (Opsional)</label>
                    <input type="text" id="action_target" name="action_target" value="{{ old('action_target') }}" placeholder="Contoh: slug-artikel, ID buku, atau URL https://..."
                           class="w-full px-4 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition bg-white font-medium">
                </div>

                <!-- Status Aktif -->
                <label class="flex items-center gap-2 cursor-pointer pt-1">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-[#1A43BF] focus:ring-[#1A43BF] border-slate-300">
                    <span class="text-xs font-semibold text-[#1E293B]">Aktifkan di Slide HP</span>
                </label>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                    <span class="material-symbols-rounded text-base">cloud_upload</span>
                    <span>Simpan & Tampilkan Banner</span>
                </button>
            </form>
        </div>

        <!-- Daftar Banner Aktif -->
        <div class="lg:col-span-2 space-y-4">
            @forelse($banners as $banner)
                <div class="card-system p-5 flex flex-col sm:flex-row items-center gap-5">
                    <div class="w-full sm:w-48 h-28 rounded-xl border border-slate-200 bg-[#F0F4FA] overflow-hidden shrink-0 relative flex items-center justify-center">
                        <img src="{{ $banner->image_url ?? '' }}" 
                             alt="{{ $banner->title }}" 
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden w-full h-full bg-[#F0F4FA] flex-col items-center justify-center text-[#64748B] text-xs font-semibold gap-1.5 p-2 text-center">
                            <span class="material-symbols-rounded text-2xl text-slate-400">ad_units</span>
                            <span class="text-[10px] text-slate-500">Banner Promo</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 w-full">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-bold text-[#1E293B] truncate">{{ $banner->title }}</span>
                            @if($banner->is_active)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#ECFDF5] text-[#047857]">Aktif di HP</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#F1F5F9] text-[#64748B]">Nonaktif</span>
                            @endif
                        </div>
                        <p class="text-xs text-[#64748B] mt-1 line-clamp-2 font-medium">{{ $banner->subtitle ?? 'Tidak ada keterangan tambahan.' }}</p>
                        
                        <div class="flex items-center justify-end text-[11px] text-[#64748B] mt-3 pt-3 border-t border-slate-100">
                            <button type="button" 
                                    @click="confirmDelete('{{ route('admin.banners.destroy', $banner->id) }}', 'Banner: {{ addslashes($banner->title) }}')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-[8px] text-rose-600 hover:text-rose-700 hover:bg-rose-50 font-bold text-xs transition cursor-pointer">
                                <span class="material-symbols-rounded text-xs">delete</span>
                                <span>Hapus Banner</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card-system p-10 text-center text-[#64748B] text-xs">
                    Belum ada banner promo yang diunggah. Silakan gunakan formulir di samping untuk menambahkan banner pertama.
                </div>
            @endforelse

            <div class="pt-2">
                {{ $banners->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection
