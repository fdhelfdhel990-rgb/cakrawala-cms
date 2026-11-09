<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F6FB]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cakrawala</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .material-symbols-rounded {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-[#F8FAFD] text-[#1E293B] antialiased">
    <div class="w-full max-w-[420px]">
        <!-- Login Card Sesuai Desain Acuan -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-[#E9EFF8]">
            <h1 class="text-3xl font-extrabold text-[#0B1E5B] text-center mb-8 tracking-tight">Login</h1>

            @if(session('error') || $errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
                    {{ session('error') ?: $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Form Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-[#1E293B] mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'fdhelfdhel990@gmail.com') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-[#6B8CD5] bg-[#EBF2FA] text-sm text-[#0F172A] font-medium focus:outline-none focus:ring-2 focus:ring-[#0B1E5B] transition placeholder-slate-400">
                </div>

                <!-- Form Password dengan Icon Mata -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-[#1E293B] mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required value="password123"
                               class="w-full px-4 py-3 pr-12 rounded-xl border border-[#CBD5E1] focus:border-[#6B8CD5] bg-[#EBF2FA] text-sm text-[#0F172A] font-medium focus:outline-none focus:ring-2 focus:ring-[#0B1E5B] transition placeholder-slate-400">
                        <button type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()" title="Lihat kata sandi"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#64748B] hover:text-[#0B1E5B] focus:outline-none transition cursor-pointer">
                            <!-- Icon Mata Terbuka -->
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <!-- Icon Mata Tertutup (Disamarkan) -->
                            <svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Checkbox Ingat Saya -->
                <div class="flex items-center gap-2.5 pt-0.5">
                    <input type="checkbox" id="remember" name="remember" checked
                           class="w-4 h-4 rounded text-[#0B1E5B] border-slate-300 focus:ring-[#0B1E5B] cursor-pointer">
                    <label for="remember" class="text-sm font-medium text-[#334155] cursor-pointer select-none">Ingat saya</label>
                </div>

                <!-- Tombol Navy Login -->
                <button type="submit"
                        class="w-full py-3.5 px-6 rounded-xl bg-[#0B1E5B] hover:bg-[#081745] active:bg-[#050f2e] text-white text-base font-bold transition shadow-sm cursor-pointer mt-2">
                    Login
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>