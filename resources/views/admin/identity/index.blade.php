@extends('layouts.admin')

@section('title', 'Identitas & Konten Informasi')
@section('breadcrumb_title', 'Identitas & Konten')

@section('content')
<div class="space-y-6" x-data="{ activeSection: 'profile' }">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#0B132B] uppercase tracking-wider mb-1">MANAJEMEN KONTEN</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Identitas Komunitas & Aplikasi</h1>
            <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Atur profil komunitas, visi misi, syarat ketentuan, dan kontak layanan literasi.</p>
        </div>
    </div>

    <!-- Section Navigation Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-[#E2E8F0] pb-2">
        <button type="button" 
                @click="activeSection = 'profile'"
                :class="activeSection === 'profile' ? 'bg-[#0B132B] text-white' : 'bg-white text-[#64748B] hover:text-[#1E293B] border border-[#E2E8F0]'"
                class="px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer">
            1. Profil Komunitas & Kontak
        </button>
        <button type="button" 
                @click="activeSection = 'vision'"
                :class="activeSection === 'vision' ? 'bg-[#0B132B] text-white' : 'bg-white text-[#64748B] hover:text-[#1E293B] border border-[#E2E8F0]'"
                class="px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer">
            2. Visi & Misi
        </button>
        <button type="button" 
                @click="activeSection = 'about'"
                :class="activeSection === 'about' ? 'bg-[#0B132B] text-white' : 'bg-white text-[#64748B] hover:text-[#1E293B] border border-[#E2E8F0]'"
                class="px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer">
            3. Tentang Aplikasi & Legalitas
        </button>
    </div>

    <!-- Form Container -->
    <form action="{{ route('admin.identity.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Bagian 1: Profil Komunitas & Kontak -->
        <div x-show="activeSection === 'profile'" class="card-system p-7 sm:p-9 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-[#1E293B]">Profil Komunitas & Kontak Pengelola</h2>
                <p class="text-xs text-[#64748B] mt-0.5">Informasi utama identitas komunitas yang tampil di header aplikasi dan dokumen resmi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                <!-- Nama Komunitas -->
                <div>
                    <label for="community_name" class="block text-xs font-bold text-[#1E293B] mb-2">Nama Komunitas <span class="text-rose-500">*</span></label>
                    <input type="text" id="community_name" name="community_name" value="{{ old('community_name', $settings['community_name']) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                </div>

                <!-- Nama Aplikasi -->
                <div>
                    <label for="app_name" class="block text-xs font-bold text-[#1E293B] mb-2">Nama Platform Digital <span class="text-rose-500">*</span></label>
                    <input type="text" id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                </div>

                <!-- Tagline Aplikasi -->
                <div class="md:col-span-2">
                    <label for="tagline" class="block text-xs font-bold text-[#1E293B] mb-2">Tagline / Slogan Aplikasi</label>
                    <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $settings['tagline']) }}"
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                    <p class="text-[11px] text-[#64748B] mt-1.5">Muncul pada layar pembuka (splash) aplikasi mobile.</p>
                </div>

                <!-- Email Resmi -->
                <div>
                    <label for="contact_email" class="block text-xs font-bold text-[#1E293B] mb-2">Alamat Email Pengelola</label>
                    <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}"
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                </div>

                <!-- Nomor Telepon / WA -->
                <div>
                    <label for="contact_phone" class="block text-xs font-bold text-[#1E293B] mb-2">Nomor Kontak WhatsApp / Telepon</label>
                    <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}"
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                </div>

                <!-- Alamat Sekretariat -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-xs font-bold text-[#1E293B] mb-2">Alamat Komunitas / Sekretariat</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $settings['address']) }}"
                           class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition font-medium">
                </div>
            </div>
        </div>

        <!-- Bagian 2: Visi & Misi Komunitas -->
        <div x-show="activeSection === 'vision'" class="card-system p-7 sm:p-9 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-[#1E293B]">Visi & Misi Literasi Remaja</h2>
                <p class="text-xs text-[#64748B] mt-0.5">Arah tujuan dan komitmen komunitas literasi untuk generasi muda pembaca.</p>
            </div>

            <div class="space-y-5">
                <div>
                    <label for="vision" class="block text-xs font-bold text-[#1E293B] mb-2">Visi Komunitas</label>
                    <textarea id="vision" name="vision" rows="3"
                              class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition leading-relaxed font-medium">{{ old('vision', $settings['vision']) }}</textarea>
                </div>

                <div>
                    <label for="mission" class="block text-xs font-bold text-[#1E293B] mb-2">Misi Komunitas</label>
                    <textarea id="mission" name="mission" rows="5"
                              class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition leading-relaxed font-medium">{{ old('mission', $settings['mission']) }}</textarea>
                    <p class="text-[11px] text-[#64748B] mt-1.5">Tuliskan butir-butir misi yang jelas dan terarah.</p>
                </div>
            </div>
        </div>

        <!-- Bagian 3: Tentang Aplikasi & Kebijakan -->
        <div x-show="activeSection === 'about'" class="card-system p-7 sm:p-9 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-[#1E293B]">Tentang Aplikasi & Ketentuan Layanan</h2>
                <p class="text-xs text-[#64748B] mt-0.5">Penjelasan profil aplikasi serta transparansi syarat dan perlindungan privasi pembaca.</p>
            </div>

            <div class="space-y-5">
                <div>
                    <label for="about" class="block text-xs font-bold text-[#1E293B] mb-2">Tentang Aplikasi EPerpus Cakrawala</label>
                    <textarea id="about" name="about" rows="4"
                              class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition leading-relaxed font-medium">{{ old('about', $settings['about']) }}</textarea>
                </div>

                <div>
                    <label for="terms" class="block text-xs font-bold text-[#1E293B] mb-2">Syarat & Ketentuan Penggunaan</label>
                    <textarea id="terms" name="terms" rows="4"
                              class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition leading-relaxed font-medium">{{ old('terms', $settings['terms']) }}</textarea>
                </div>

                <div>
                    <label for="privacy_policy" class="block text-xs font-bold text-[#1E293B] mb-2">Kebijakan Privasi Pengguna</label>
                    <textarea id="privacy_policy" name="privacy_policy" rows="4"
                              class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] transition leading-relaxed font-medium">{{ old('privacy_policy', $settings['privacy_policy']) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Bottom Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <button type="submit" 
                    class="px-7 py-3 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                Simpan Pembaruan Identitas
            </button>
        </div>
    </form>
</div>
@endsection
