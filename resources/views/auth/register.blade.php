<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pegawai — BPS ACT</title>
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
    
    <div class="w-full max-w-xl bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden my-8">
        
        <!-- Header Branding BPS Institusional -->
        <div class="bg-gradient-to-r from-[#005AA9] to-[#0070D2] p-6 sm:p-7 text-white text-center relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-white/10 pointer-events-none"></div>
            <div class="absolute -left-6 -top-6 w-24 h-24 rounded-full bg-white/10 pointer-events-none"></div>

            <div class="inline-flex items-center justify-center p-2.5 bg-white rounded-xl shadow-md mb-3">
                <img src="{{ asset('BPS Logo.svg') }}" alt="BPS Logo" class="h-9 w-auto object-contain">
            </div>
            <h1 class="text-2xl font-black tracking-tight text-white">Registrasi Pegawai BPS</h1>
            <p class="text-xs sm:text-sm text-blue-100 font-medium mt-0.5">Daftarkan akun pegawai baru ke sistem BPS ACT</p>
        </div>

        <div class="p-6 sm:p-8">
            
            @if($errors->any())
            <div class="mb-6 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl flex items-start gap-2.5">
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

            <form action="{{ route('register') }}" method="POST" class="space-y-4 sm:space-y-5" @submit="submitForm">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5">Nama Lengkap & Gelar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                            placeholder="contoh: Muhammad Rizky, S.Tr.Stat."
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] transition outline-none">
                    </div>
                </div>

                <!-- Email Dinas -->
                <div>
                    <label for="email" class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5">Email Pegawai BPS</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                            placeholder="contoh: rizky@bps.go.id"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] transition outline-none">
                    </div>
                </div>

                <!-- Divisi & Role (2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Divisi -->
                    <div>
                        <label for="division_id" class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5">Unit Kerja / Tim</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <select name="division_id" id="division_id" required
                                class="w-full pl-10 pr-9 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] transition outline-none appearance-none cursor-pointer">
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
                        <label for="role" class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5">Peran / Hak Akses</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <select name="role" id="role" required
                                class="w-full pl-10 pr-9 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] transition outline-none appearance-none cursor-pointer">
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

                <!-- Password & Konfirmasi (2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required minlength="6" x-model="password"
                                placeholder="Minimal 6 karakter"
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

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5">Konfirmasi Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400"
                                :class="{'text-rose-500': !passwordsMatch && password_confirmation !== ''}">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <input :type="showPasswordConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required minlength="6" x-model="password_confirmation"
                                placeholder="Ulangi kata sandi"
                                class="w-full pl-10 pr-11 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#005AA9]/20 focus:border-[#005AA9] transition outline-none"
                                :class="{'border-rose-400 focus:border-rose-500 focus:ring-rose-500/20': !passwordsMatch && password_confirmation !== ''}">
                            <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" tabindex="-1" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition cursor-pointer">
                                <svg x-show="!showPasswordConfirm" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <svg x-show="showPasswordConfirm" x-cloak class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <p x-show="!passwordsMatch && password_confirmation !== ''" x-cloak class="text-xs text-rose-600 mt-1 font-semibold">Konfirmasi kata sandi tidak cocok.</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                        :disabled="isLoading || (!passwordsMatch && password_confirmation !== '')"
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-[#005AA9] to-[#0070D2] hover:from-[#004582] hover:to-[#005AA9] text-white font-bold rounded-xl text-sm transition shadow-md shadow-blue-500/20 active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
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

            <!-- Footer Login Link -->
            <div class="mt-6 pt-5 border-t border-gray-200 text-center">
                <p class="text-xs sm:text-sm text-gray-600">
                    Sudah memiliki akun terdaftar? 
                    <a href="{{ route('login') }}" class="font-bold text-[#005AA9] hover:underline">Masuk ke Portal</a>
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
