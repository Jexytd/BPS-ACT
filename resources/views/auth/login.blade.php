<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — BPS Activity Tracker (BPS ACT)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased font-sans flex"
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
    
    <div class="w-full min-h-screen grid lg:grid-cols-12">
        
        <!-- SISI KIRI: BANNER MODERN INSTITUSIONAL (Desktop) -->
        <div class="hidden lg:flex lg:col-span-6 xl:col-span-7 bg-gradient-to-br from-[#002B5C] via-[#004A94] to-[#0070D2] text-white p-12 xl:p-16 flex-col justify-between relative overflow-hidden">
            
            <!-- SVG Network / Constellation Background Pattern -->
            <div class="absolute inset-0 opacity-20 pointer-events-none">
                <svg class="w-full h-full object-cover" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800">
                    <defs>
                        <linearGradient id="netGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff" stop-opacity="0.8"/>
                            <stop offset="100%" stop-color="#60a5fa" stop-opacity="0.2"/>
                        </linearGradient>
                    </defs>
                    <g stroke="url(#netGrad)" stroke-width="1.2" fill="none">
                        <!-- Lines connecting nodes -->
                        <line x1="120" y1="180" x2="280" y2="120" />
                        <line x1="280" y1="120" x2="420" y2="220" />
                        <line x1="120" y1="180" x2="200" y2="340" />
                        <line x1="200" y1="340" x2="380" y2="320" />
                        <line x1="420" y1="220" x2="380" y2="320" />
                        <line x1="420" y1="220" x2="600" y2="160" />
                        <line x1="380" y1="320" x2="520" y2="440" />
                        <line x1="600" y1="160" x2="680" y2="300" />
                        <line x1="680" y1="300" x2="520" y2="440" />
                        <line x1="200" y1="340" x2="160" y2="520" />
                        <line x1="160" y1="520" x2="320" y2="580" />
                        <line x1="320" y1="580" x2="520" y2="440" />
                        <line x1="320" y1="580" x2="480" y2="680" />
                        <line x1="520" y1="440" x2="660" y2="580" />
                        <line x1="660" y1="580" x2="480" y2="680" />
                    </g>
                    <!-- Glowing Nodes -->
                    <g fill="#ffffff">
                        <circle cx="120" cy="180" r="4" opacity="0.9"/>
                        <circle cx="280" cy="120" r="5" opacity="0.8"/>
                        <circle cx="420" cy="220" r="6" opacity="0.9"/>
                        <circle cx="200" cy="340" r="4" opacity="0.7"/>
                        <circle cx="380" cy="320" r="5" opacity="0.95"/>
                        <circle cx="600" cy="160" r="4" opacity="0.8"/>
                        <circle cx="680" cy="300" r="5" opacity="0.7"/>
                        <circle cx="520" cy="440" r="6" opacity="0.9"/>
                        <circle cx="160" cy="520" r="4" opacity="0.8"/>
                        <circle cx="320" cy="580" r="5" opacity="0.85"/>
                        <circle cx="480" cy="680" r="4" opacity="0.7"/>
                        <circle cx="660" cy="580" r="5" opacity="0.8"/>
                    </g>
                </svg>
            </div>

            <!-- Header Branding -->
            <div class="relative z-10">
                <div class="flex items-center gap-3.5">
                    <div class="p-2.5 bg-white rounded-xl shadow-lg inline-flex items-center justify-center">
                        <img src="{{ asset('BPS Logo.svg') }}" alt="Logo BPS" class="h-8 w-auto object-contain">
                    </div>
                    <div>
                        <h2 class="text-xl font-black tracking-tight text-white leading-none">BPS ACT</h2>
                        <p class="text-xs text-blue-200 font-medium mt-1">BPS Kabupaten Bangka</p>
                    </div>
                </div>
            </div>

            <!-- Central Hero Pitch -->
            <div class="relative z-10 my-auto py-12 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-blue-100 mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Single Source of Truth Kegiatan Pegawai
                </div>

                <h1 class="text-4xl xl:text-5xl font-black tracking-tight text-white leading-[1.15] drop-shadow-sm">
                    Pantau kegiatan tim, kelola jadwal kerja, dukung dengan AI.
                </h1>

                <p class="mt-6 text-base xl:text-lg text-blue-100/90 leading-relaxed font-normal">
                    Platform internal Badan Pusat Statistik untuk pencatatan dan monitoring aktivitas pegawai antar tim kerja, pencegahan jadwal bentrok otomatis, serta transparansi progres output statistik.
                </p>

                <!-- 3 Key Features -->
                <div class="grid grid-cols-3 gap-4 mt-8 pt-8 border-t border-white/15">
                    <div>
                        <div class="text-2xl font-black text-white">9 Tim</div>
                        <div class="text-xs text-blue-200 mt-0.5">BPS Kab. Bangka</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">Real-Time</div>
                        <div class="text-xs text-blue-200 mt-0.5">Kalender Tim</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">AI Detection</div>
                        <div class="text-xs text-blue-200 mt-0.5">Cek Bentrok Jadwal</div>
                    </div>
                </div>
            </div>

            <!-- Footer Banner -->
            <div class="relative z-10 text-xs text-blue-200/80 font-medium">
                &copy; {{ date('Y') }} Badan Pusat Statistik &bull; Internal use only
            </div>
        </div>

        <!-- SISI KANAN: FORM LOGIN -->
        <div class="col-span-12 lg:col-span-6 xl:col-span-5 bg-white flex flex-col justify-between p-6 sm:p-12 xl:p-16 min-h-screen">
            
            <!-- Mobile Header Logo (< lg) -->
            <div class="lg:hidden flex items-center justify-between pb-6 border-b border-gray-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-[#005AA9] rounded-lg shadow-sm">
                        <img src="{{ asset('BPS Logo.svg') }}" alt="Logo BPS" class="h-6 w-auto object-contain brightness-0 invert">
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold text-gray-900 leading-none">BPS ACT</h2>
                        <p class="text-[11px] text-gray-500 mt-0.5">BPS Kabupaten Bangka</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-[#005AA9] bg-blue-50 px-2.5 py-1 rounded-full">Portal Pegawai</span>
            </div>

            <!-- Main Form Box -->
            <div class="my-auto max-w-md w-full mx-auto py-4">

                <div class="mb-8">
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Masuk</h2>
                    <p class="text-sm text-gray-500 mt-1.5">Gunakan akun email BPS Anda untuk mengakses sistem.</p>
                </div>

                <!-- Session Alert Messages -->
                @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl flex items-start gap-3">
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

                <form action="{{ route('login') }}" method="POST" class="space-y-5" @submit="submitForm">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-1.5">Email Pegawai</label>
                        <div class="relative">
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                                placeholder="nama@bps.go.id"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] hover:border-gray-400 transition outline-none shadow-2xs">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-bold text-gray-700">Kata Sandi</label>
                            <a href="javascript:void(0)" onclick="alert('Silakan hubungi Administrator IT / Tim Pengolahan & TI BPS untuk reset kata sandi Anda.')" class="text-xs font-semibold text-[#005AA9] hover:underline transition">Lupa password?</a>
                        </div>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required autocomplete="current-password"
                                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                                class="w-full pl-4 pr-11 py-3 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] hover:border-gray-400 transition outline-none shadow-2xs">
                            <button type="button" @click="showPassword = !showPassword" tabindex="-1" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition cursor-pointer">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center pt-1">
                        <label class="flex items-center gap-2.5 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#005AA9] focus:ring-[#005AA9] cursor-pointer">
                            <span class="font-medium">Ingat sesi saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" 
                            :disabled="isLoading"
                            class="w-full py-3.5 px-4 bg-[#005AA9] hover:bg-[#004582] text-white font-bold rounded-xl text-sm transition shadow-md shadow-blue-600/20 active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed">
                            <span x-show="!isLoading" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Masuk</span>
                            </span>
                            <span x-show="isLoading" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memverifikasi...</span>
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Footer Link to Register -->
                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-sm text-gray-500 font-medium">
                        Belum punya akun? 
                        <a href="{{ route('register') }}" class="font-bold text-[#005AA9] hover:underline">Daftar di sini</a>
                    </p>
                </div>
            </div>

            <!-- Footer Small -->
            <div class="text-center text-xs text-gray-400 pt-4">
                &copy; {{ date('Y') }} Badan Pusat Statistik &bull; BPS ACT v1.0
            </div>

        </div>

    </div>

</body>
</html>
