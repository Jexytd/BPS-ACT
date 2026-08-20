<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Pegawai — BPS ACT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 antialiased text-gray-800"
    x-data="{
        showPassword: false,
        isLoading: false,
        submitForm(e) {
            if (this.isLoading) { 
                e.preventDefault(); 
                return; 
            }
            this.isLoading = true;
        }
    }">
    
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden my-6">
        
        <!-- Header Branding BPS Institusional -->
        <div class="bg-gradient-to-r from-[#005AA9] to-[#0070D2] p-6 text-white text-center relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-white/10 pointer-events-none"></div>
            <div class="absolute -left-6 -top-6 w-24 h-24 rounded-full bg-white/10 pointer-events-none"></div>
            
            <div class="inline-flex items-center justify-center p-2.5 bg-white rounded-xl shadow-md mb-3">
                <img src="{{ asset('BPS Logo.svg') }}" alt="BPS Logo" class="h-10 w-auto object-contain">
            </div>
            <h1 class="text-2xl font-black tracking-tight text-white">BPS ACT</h1>
            <p class="text-xs sm:text-sm text-blue-100 font-medium mt-0.5">Portal Login Pegawai</p>
        </div>

        <div class="p-6 sm:p-8">

            <!-- Session Messages -->
            @if(session('success'))
            <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl flex items-start gap-2.5">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="space-y-1 font-medium text-xs sm:text-sm">
                    @foreach($errors->all() as $err)
                    <p>{{ $err }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Form Login -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4" @submit="submitForm">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5">Email Pegawai BPS</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path>
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                            placeholder="contoh: budi@bps.go.id"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] transition outline-none">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs sm:text-sm font-bold text-gray-700">Kata Sandi</label>
                        <a href="javascript:void(0)" onclick="alert('Silakan hubungi Administrator IT / Tim IPD BPS untuk reset kata sandi Anda.')" class="text-xs font-semibold text-[#005AA9] hover:underline transition">Lupa password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required autocomplete="current-password"
                            placeholder="Masukkan kata sandi"
                            class="w-full pl-10 pr-11 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] transition outline-none">
                        <button type="button" @click="showPassword = !showPassword" tabindex="-1" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition cursor-pointer">
                            <svg x-show="!showPassword" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center pt-1">
                    <label class="flex items-center gap-2 text-xs sm:text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#005AA9] focus:ring-[#005AA9] cursor-pointer">
                        <span class="font-medium">Ingat sesi saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                        :disabled="isLoading"
                        class="w-full py-3 px-4 bg-gradient-to-r from-[#005AA9] to-[#0070D2] hover:from-[#004582] hover:to-[#005AA9] text-white font-bold rounded-xl text-sm transition shadow-md shadow-blue-500/20 active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed">
                        <span x-show="!isLoading" class="flex items-center gap-2">
                            <span>Masuk ke Sistem</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </span>
                        <span x-show="isLoading" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Footer Register Link -->
            <div class="mt-6 pt-5 border-t border-gray-200 text-center">
                <p class="text-xs sm:text-sm text-gray-600">
                    Belum memiliki akun pegawai? 
                    <a href="{{ route('register') }}" class="font-bold text-[#005AA9] hover:underline">Daftar sekarang</a>
                </p>
            </div>

        </div>

        <!-- System Version Footer -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 text-center">
            <p class="text-[11px] text-gray-400 font-medium">&copy; {{ date('Y') }} Badan Pusat Statistik &bull; BPS ACT Single Source of Truth</p>
        </div>

    </div>

</body>
</html>
