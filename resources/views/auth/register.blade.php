<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pegawai — BPS Activity Tracker (BPS ACT)</title>
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
        isLoading: false,
        showPassword: false,
        showPasswordConfirm: false,
        password: '',
        password_confirmation: '',
        get passwordsMatch() {
            if (this.password_confirmation === '') return true;
            return this.password === this.password_confirmation;
        },
        submitForm(e) {
            if (!this.passwordsMatch) {
                e.preventDefault();
                alert('Konfirmasi kata sandi tidak cocok.');
                return;
            }
            if (this.isLoading) { 
                e.preventDefault(); 
                return; 
            }
            this.isLoading = true;
        }
    }">
    
    <div class="w-full min-h-screen grid lg:grid-cols-12">
        
        <!-- SISI KIRI: BANNER MODERN INSTITUSIONAL (Desktop) -->
        <div class="hidden lg:flex lg:col-span-5 xl:col-span-5 bg-gradient-to-br from-[#002B5C] via-[#004A94] to-[#0070D2] text-white p-10 xl:p-14 flex-col justify-between relative overflow-hidden">
            
            <!-- SVG Network / Constellation Background Pattern -->
            <div class="absolute inset-0 opacity-20 pointer-events-none">
                <svg class="w-full h-full object-cover" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800">
                    <defs>
                        <linearGradient id="netGradReg" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff" stop-opacity="0.8"/>
                            <stop offset="100%" stop-color="#60a5fa" stop-opacity="0.2"/>
                        </linearGradient>
                    </defs>
                    <g stroke="url(#netGradReg)" stroke-width="1.2" fill="none">
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
            <div class="relative z-10 my-auto py-10 max-w-lg">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-blue-100 mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Registrasi Pegawai Baru
                </div>

                <h1 class="text-3xl xl:text-4xl font-black tracking-tight text-white leading-[1.2] drop-shadow-sm">
                    Satu portal terpadu untuk kolaborasi seluruh tim kerja BPS.
                </h1>

                <p class="mt-5 text-sm xl:text-base text-blue-100/90 leading-relaxed font-normal">
                    Daftarkan akun pegawai Anda untuk mulai merencanakan, mendokumentasikan, dan memantau kegiatan sensus, survei, rilis statistik, hingga administrasi kegiatan BPS Kabupaten Bangka.
                </p>

                <!-- List Benefits -->
                <div class="mt-8 space-y-3.5 pt-6 border-t border-white/15 text-xs text-blue-100">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-emerald-300 font-bold shrink-0">✓</div>
                        <span>Terintegrasi langsung dengan 9 Tim Kerja BPS Bangka</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-emerald-300 font-bold shrink-0">✓</div>
                        <span>Notifikasi otomatis saat ada kegiatan tim baru</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-emerald-300 font-bold shrink-0">✓</div>
                        <span>Validasi konflik dan bentrok jadwal berbasis AI</span>
                    </div>
                </div>
            </div>

            <!-- Footer Banner -->
            <div class="relative z-10 text-xs text-blue-200/80 font-medium">
                &copy; {{ date('Y') }} Badan Pusat Statistik &bull; Internal use only
            </div>
        </div>

        <!-- SISI KANAN: FORM REGISTER -->
        <div class="col-span-12 lg:col-span-7 xl:col-span-7 bg-white flex flex-col justify-between p-6 sm:p-12 xl:p-14 min-h-screen overflow-y-auto">
            
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
                <span class="text-xs font-bold text-[#005AA9] bg-blue-50 px-2.5 py-1 rounded-full">Daftar Akun</span>
            </div>

            <!-- Main Form Box -->
            <div class="my-auto max-w-xl w-full mx-auto py-2">

                <div class="mb-6 sm:mb-8">
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Daftar Akun Pegawai</h2>
                    <p class="text-sm text-gray-500 mt-1.5">Lengkapi data di bawah ini untuk membuat akun portal BPS ACT.</p>
                </div>

                <!-- Session Alert Messages -->
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

                <form action="{{ route('register') }}" method="POST" class="space-y-4" @submit="submitForm">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap & Gelar</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                            placeholder="contoh: Muhammad Rizky, S.Tr.Stat."
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] hover:border-gray-400 transition outline-none shadow-2xs">
                    </div>

                    <!-- Email Dinas -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Email Pegawai BPS</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                            placeholder="nama@bps.go.id"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] hover:border-gray-400 transition outline-none shadow-2xs">
                    </div>

                    <!-- 2 Kolom: Tim Kerja & Role -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Tim Kerja -->
                        <div>
                            <label for="division_id" class="block text-sm font-bold text-gray-700 mb-1">Unit Kerja / Tim</label>
                            <div class="relative">
                                <select name="division_id" id="division_id" required
                                    class="w-full pl-4 pr-9 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] hover:border-gray-400 transition outline-none appearance-none cursor-pointer shadow-2xs">
                                    <option value="" disabled {{ old('division_id') ? '' : 'selected' }}>Pilih Tim Kerja</option>
                                    @foreach($divisions as $div)
                                    <option value="{{ $div['id'] ?? $div->id }}" {{ old('division_id') == ($div['id'] ?? $div->id) ? 'selected' : '' }}>
                                        {{ $div['name'] ?? $div->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-bold text-gray-700 mb-1">Peran / Hak Akses</label>
                            <div class="relative">
                                <select name="role" id="role" required
                                    class="w-full pl-4 pr-9 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] hover:border-gray-400 transition outline-none appearance-none cursor-pointer shadow-2xs">
                                    <option value="staff" {{ old('role', 'staff') === 'staff' ? 'selected' : '' }}>Staff Pegawai</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator / PJ</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2 Kolom: Password & Konfirmasi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Kata Sandi</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required minlength="6" x-model="password"
                                    placeholder="Minimal 6 karakter"
                                    class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] hover:border-gray-400 transition outline-none shadow-2xs">
                                <button type="button" @click="showPassword = !showPassword" tabindex="-1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition cursor-pointer">
                                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1">Konfirmasi Sandi</label>
                            <div class="relative">
                                <input :type="showPasswordConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required minlength="6" x-model="password_confirmation"
                                    placeholder="Ulangi kata sandi"
                                    class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] hover:border-gray-400 transition outline-none shadow-2xs"
                                    :class="{'border-rose-400 focus:border-rose-500 focus:ring-rose-500/20': !passwordsMatch && password_confirmation !== ''}">
                                <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" tabindex="-1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition cursor-pointer">
                                    <svg x-show="!showPasswordConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <svg x-show="showPasswordConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243"></path>
                                    </svg>
                                </button>
                            </div>
                            <p x-show="!passwordsMatch && password_confirmation !== ''" x-cloak class="text-xs text-rose-600 mt-1 font-semibold">Konfirmasi kata sandi tidak cocok.</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" 
                            :disabled="isLoading || (!passwordsMatch && password_confirmation !== '')"
                            class="w-full py-3.5 px-4 bg-[#005AA9] hover:bg-[#004582] text-white font-bold rounded-xl text-sm transition shadow-md shadow-blue-600/20 active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                            <span x-show="!isLoading" class="flex items-center gap-2">
                                <span>Daftarkan Akun Pegawai</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </span>
                            <span x-show="isLoading" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memproses Pendaftaran...</span>
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Footer Link to Login -->
                <div class="mt-6 pt-5 border-t border-gray-100 text-center">
                    <p class="text-sm text-gray-500 font-medium">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="font-bold text-[#005AA9] hover:underline">Masuk di sini</a>
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
