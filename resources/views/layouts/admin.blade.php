<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F6FB]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - EPerpus Cakrawala Admin</title>
    
    <!-- Google Fonts: Poppins & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- Tailwind CSS & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navyDark: '#0B132B',
                        primaryBlue: '#1A43BF',
                        activeBlueBg: '#EBF3FF',
                        canvasBg: '#F4F6FB',
                        cardBorder: '#E2E8F0',
                        textPrimary: '#1E293B',
                        textMuted: '#64748B',
                    },
                    borderRadius: {
                        'card': '16px',
                        '2xl': '16px',
                    },
                    boxShadow: {
                        'card': '0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -2px rgba(0, 0, 0, 0.02)',
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --font-sans: 'Poppins', sans-serif;
            --color-navy: #0B132B;
            --color-blue: #1A43BF;
            --color-primary: #1A43BF;
            --card-radius: 16px;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
            --content-max-width: 1160px;
            --page-gutter: 32px;
            --page-scale: .92;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #1E293B;
            background-color: #F4F6FB;
            zoom: var(--page-scale);
        }

        @supports not (zoom: 1) {
            body {
                transform: scale(var(--page-scale));
                transform-origin: top center;
            }
        }

        .container-custom {
            max-width: var(--content-max-width);
            margin-left: auto;
            margin-right: auto;
            padding-left: var(--page-gutter);
            padding-right: var(--page-gutter);
            width: 100%;
        }

        .card-system {
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            transition: all 0.2s ease;
        }

        @media (max-width: 768px) {
            :root {
                --page-gutter: 16px;
            }
        }

        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }

        .material-symbols-rounded {
            font-family: 'Material Symbols Rounded', sans-serif;
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            vertical-align: middle;
            line-height: 1;
            user-select: none;
        }
    </style>
</head>
<body class="h-full antialiased text-[#1E293B] bg-[#F4F6FB]" x-data="{ 
    deleteModalOpen: false, 
    deleteActionUrl: '', 
    deleteItemName: '',
    confirmDelete(url, name) {
        this.deleteActionUrl = url;
        this.deleteItemName = name;
        this.deleteModalOpen = true;
    }
}">
    <div class="min-h-full flex flex-col lg:flex-row">
        <!-- Sidebar Navigation: Bersih & Penomoran Halaman -->
        <aside class="w-full lg:w-64 bg-white text-[#1E293B] flex flex-col justify-between shrink-0 border-r border-[#E2E8F0] z-20">
            <div>
                <!-- Header Sidebar: Logo Teks Bersih Tanpa Background Sesuai Permintaan -->
                <div class="px-6 py-5 border-b border-[#E2E8F0]/80 flex items-center justify-start">
                    <a href="{{ route('admin.dashboard') }}" class="block">
                        <img src="{{ asset('images/logo_teks.png') }}" alt="Cakrawala" 
                             class="h-10 sm:h-11 w-auto object-contain"
                             onerror="this.onerror=null; this.src='{{ asset('umages/logo_teks.png') }}';">
                    </a>
                </div>

                <!-- 5 Menu Navigasi Utama dengan Penomoran (01, 02, 03, 04, 05) -->
                <nav class="px-4 py-5 space-y-1 text-xs font-semibold">
                    <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-[#64748B]">MENU UTAMA</p>

                    <!-- 01 Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#EBF3FF] text-[#0B132B] font-bold shadow-xs' : 'text-[#1E293B] hover:text-[#0B132B] hover:bg-slate-50' }}">
                        <span class="w-5 text-xs font-bold {{ request()->routeIs('admin.dashboard') ? 'text-[#1A43BF]' : 'text-[#64748B]' }}">01</span>
                        <span class="text-xs">Dashboard</span>
                    </a>

                    <!-- 02 Kelola Buku -->
                    <a href="{{ route('admin.books.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.books.*') ? 'bg-[#EBF3FF] text-[#0B132B] font-bold shadow-xs' : 'text-[#1E293B] hover:text-[#0B132B] hover:bg-slate-50' }}">
                        <span class="w-5 text-xs font-bold {{ request()->routeIs('admin.books.*') ? 'text-[#1A43BF]' : 'text-[#64748B]' }}">02</span>
                        <span class="text-xs">Kelola Buku</span>
                    </a>

                    <!-- 03 Kelola Artikel -->
                    <a href="{{ route('admin.articles.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.articles.*') ? 'bg-[#EBF3FF] text-[#0B132B] font-bold shadow-xs' : 'text-[#1E293B] hover:text-[#0B132B] hover:bg-slate-50' }}">
                        <span class="w-5 text-xs font-bold {{ request()->routeIs('admin.articles.*') ? 'text-[#1A43BF]' : 'text-[#64748B]' }}">03</span>
                        <span class="text-xs">Kelola Artikel</span>
                    </a>

                    <!-- 04 Kelola Banner -->
                    <a href="{{ route('admin.banners.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.banners.*') ? 'bg-[#EBF3FF] text-[#0B132B] font-bold shadow-xs' : 'text-[#1E293B] hover:text-[#0B132B] hover:bg-slate-50' }}">
                        <span class="w-5 text-xs font-bold {{ request()->routeIs('admin.banners.*') ? 'text-[#1A43BF]' : 'text-[#64748B]' }}">04</span>
                        <span class="text-xs">Kelola Banner</span>
                    </a>

                    <!-- 05 Kelola Ulasan -->
                    <a href="{{ route('admin.reviews.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.reviews.*') ? 'bg-[#EBF3FF] text-[#0B132B] font-bold shadow-xs' : 'text-[#1E293B] hover:text-[#0B132B] hover:bg-slate-50' }}">
                        <span class="w-5 text-xs font-bold {{ request()->routeIs('admin.reviews.*') ? 'text-[#1A43BF]' : 'text-[#64748B]' }}">05</span>
                        <span class="text-xs">Kelola Ulasan</span>
                    </a>
                </nav>
            </div>

            <!-- Bagian Bawah: Profil Pengelola & Keluar -->
            <div class="p-4 border-t border-[#E2E8F0] space-y-3">
                <div class="flex items-center gap-3 px-1">
                    <div class="w-8 h-8 rounded-full bg-[#EBF3FF] text-[#1A43BF] font-bold flex items-center justify-center text-xs shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-[#1E293B] truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-[11px] text-[#64748B] truncate">Pengelola Perpustakaan</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-[8px] border border-slate-300 text-[#1E293B] hover:bg-slate-50 hover:border-slate-400 text-xs font-bold transition cursor-pointer">
                        <span class="material-symbols-rounded text-base text-slate-500">logout</span>
                        <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto custom-scrollbar">
            <!-- Top Navbar Bersih & Ringkas -->
            <header class="h-16 bg-white border-b border-[#E2E8F0] px-6 sm:px-8 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2 text-xs font-medium text-[#64748B]">
                    <span>Panel Pengelola</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-[#1E293B] font-semibold">@yield('breadcrumb_title', 'Dashboard')</span>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.books.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#1A43BF] hover:underline transition">
                        <span>Katalog Digital Library</span>
                        <span class="material-symbols-rounded text-sm">arrow_forward</span>
                    </a>
                </div>
            </header>

            <!-- Main Canvas -->
            <main class="flex-1 py-7">
                <div class="container-custom">
                    <!-- Session Flash Message: Sukses -->
                    @if(session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-rounded text-lg text-emerald-600">check_circle</span>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-base font-bold cursor-pointer">
                                <span class="material-symbols-rounded text-base">close</span>
                            </button>
                        </div>
                    @endif

                    <!-- Session Flash Message: Error -->
                    @if(session('error'))
                        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-rounded text-lg text-rose-600">error</span>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-base font-bold cursor-pointer">
                                <span class="material-symbols-rounded text-base">close</span>
                            </button>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
                            <p class="font-bold mb-1">Periksa kembali formulir yang diisi:</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <!-- Footer Ringkas -->
            <footer class="bg-white border-t border-[#E2E8F0] py-4 mt-auto">
                <div class="container-custom flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-[#64748B]">
                    <p>
                        &copy; {{ date('Y') }} <span class="font-bold text-[#1E293B]">EPerpus Cakrawala</span> • Komunitas Literasi Remaja Tambun Selatan.
                    </p>
                    <p class="text-[11px]">
                        Panel Pengelola Ramah Komunitas • Poppins
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Data (Ramah Non-Tech) -->
    <div x-show="deleteModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div x-show="deleteModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="deleteModalOpen = false"
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"></div>

            <div x-show="deleteModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="inline-block bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:max-w-md w-full border border-[#E2E8F0] p-6 relative z-10">
                
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-rounded text-2xl text-rose-600">delete_forever</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-[#1E293B]">Konfirmasi Hapus Data</h3>
                        <p class="text-xs text-[#64748B] mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus <span class="font-bold text-[#1E293B]" x-text="deleteItemName"></span>? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" 
                            @click="deleteModalOpen = false" 
                            class="px-4 py-2 rounded-[8px] border border-slate-300 text-xs font-semibold text-[#1E293B] hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <form :action="deleteActionUrl" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-5 py-2 rounded-[8px] bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
