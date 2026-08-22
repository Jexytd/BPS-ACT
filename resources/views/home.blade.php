<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPS ACT — Activity & Resource Coordination Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .hero-pattern {
            background-color: #0b2545;
            background-image: 
                radial-gradient(at 0% 0%, hsla(212,100%,35%,0.6) 0px, transparent 50%),
                radial-gradient(at 100% 0%, hsla(199,100%,40%,0.4) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(220,100%,25%,0.7) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(217,91%,60%,0.3) 0px, transparent 50%);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased selection:bg-[#005AA9] selection:text-white" x-data="{ mobileMenu: false }">

    <!-- NAVBAR -->
    <header class="fixed top-0 inset-x-0 z-50 glass-header border-b border-slate-200/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- Logo & Brand -->
            <a href="/" class="flex items-center gap-3.5 group">
                <div class="p-2 bg-[#005AA9] rounded-xl shadow-md shadow-blue-900/10 group-hover:scale-105 transition-transform duration-200">
                    <img src="{{ asset('BPS Logo.svg') }}" alt="Logo BPS" class="h-7 w-auto object-contain brightness-0 invert">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-black tracking-tight text-slate-900 leading-none">BPS ACT</span>
                        <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase bg-blue-100 text-[#005AA9] rounded-md tracking-wider">v1.0</span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Badan Pusat Statistik Kabupaten Bangka</p>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#fitur" class="hover:text-[#005AA9] transition-colors">Fitur Utama</a>
                <a href="#timeline" class="hover:text-[#005AA9] transition-colors">Resource Timeline</a>
                <a href="#peminjaman" class="hover:text-[#005AA9] transition-colors">Peminjaman Aset</a>
                <a href="#tentang" class="hover:text-[#005AA9] transition-colors">Tentang</a>
            </nav>

            <!-- Actions / Auth Buttons -->
            <div class="hidden md:flex items-center gap-3">
                @if(Auth::check() || session()->has('user'))
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#005AA9] hover:bg-[#004582] text-white font-bold text-sm shadow-md shadow-blue-600/20 hover:shadow-lg transition-all active:scale-[0.98]">
                        <span>Buka Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 font-bold text-sm transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#005AA9] hover:bg-[#004582] text-white font-bold text-sm shadow-md shadow-blue-600/20 hover:shadow-lg transition-all active:scale-[0.98]">
                        <span>Daftar Akun</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @endif
            </div>

            <!-- Mobile Hamburger Button -->
            <button @click="mobileMenu = !mobileMenu" type="button" class="md:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition">
                <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenu" x-cloak x-transition class="md:hidden border-b border-slate-200 bg-white/95 backdrop-blur-md px-6 py-5 space-y-4">
            <nav class="flex flex-col space-y-3 text-sm font-semibold text-slate-700">
                <a @click="mobileMenu = false" href="#fitur" class="py-2 hover:text-[#005AA9]">Fitur Utama</a>
                <a @click="mobileMenu = false" href="#timeline" class="py-2 hover:text-[#005AA9]">Resource Timeline</a>
                <a @click="mobileMenu = false" href="#peminjaman" class="py-2 hover:text-[#005AA9]">Peminjaman Aset</a>
                <a @click="mobileMenu = false" href="#tentang" class="py-2 hover:text-[#005AA9]">Tentang</a>
            </nav>
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-2.5">
                @if(Auth::check() || session()->has('user'))
                    <a href="{{ route('dashboard') }}" class="w-full text-center py-3 bg-[#005AA9] text-white font-bold text-sm rounded-xl">Buka Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center py-3 border border-slate-200 text-slate-800 font-bold text-sm rounded-xl">Masuk</a>
                    <a href="{{ route('register') }}" class="w-full text-center py-3 bg-[#005AA9] text-white font-bold text-sm rounded-xl">Daftar Akun</a>
                @endif
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative pt-32 pb-24 md:pt-40 md:pb-36 hero-pattern text-white overflow-hidden">
        <!-- Floating Grid / Mesh Accents -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff0a_1px,transparent_1px),linear-gradient(to_bottom,#ffffff0a_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            <!-- Badge -->
            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white/10 border border-white/20 backdrop-blur-md mb-8">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="text-xs font-bold tracking-wider uppercase text-blue-100">Portal Kolaborasi Terpadu Pegawai BPS</span>
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-white max-w-5xl mx-auto leading-[1.1]">
                Perencanaan Kegiatan & Penjadwalan <span class="bg-gradient-to-r from-blue-200 via-cyan-200 to-sky-400 bg-clip-text text-transparent">Tanpa Tumpang Tindih</span>
            </h1>

            <!-- Subtitle -->
            <p class="mt-6 text-lg sm:text-xl text-blue-100/80 max-w-3xl mx-auto font-medium leading-relaxed">
                Platform koordinasi terpadu untuk monitoring agenda survei, sensus, rilis berita resmi statistik, hingga manajemen peminjaman aset kantor BPS Kabupaten Bangka.
            </p>

            <!-- Call to Actions -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                @if(Auth::check() || session()->has('user'))
                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-8 py-4 bg-white hover:bg-slate-100 text-[#002B5C] font-extrabold text-base rounded-2xl shadow-xl shadow-blue-950/20 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-3">
                        <svg class="w-5 h-5 text-[#005AA9]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Menuju Workspace Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 bg-white hover:bg-slate-100 text-[#002B5C] font-extrabold text-base rounded-2xl shadow-xl shadow-blue-950/20 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-3">
                        <svg class="w-5 h-5 text-[#005AA9]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Masuk ke Portal</span>
                    </a>
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold text-base rounded-2xl backdrop-blur-md transition-all">
                        Daftar Akun Baru
                    </a>
                @endif
            </div>

            <!-- Stats Bar -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto border-t border-white/15 pt-10">
                <div class="p-4 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-xs">
                    <p class="text-2xl sm:text-3xl font-black text-white">9 Tim Kerja</p>
                    <p class="text-xs text-blue-200 font-medium mt-1">IPDS, Nerwilis, PSS & lainnya</p>
                </div>
                <div class="p-4 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-xs">
                    <p class="text-2xl sm:text-3xl font-black text-cyan-300">100% Real-time</p>
                    <p class="text-xs text-blue-200 font-medium mt-1">Sinkronisasi Jadwal & Aset</p>
                </div>
                <div class="p-4 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-xs">
                    <p class="text-2xl sm:text-3xl font-black text-emerald-300">AI Assistant</p>
                    <p class="text-xs text-blue-200 font-medium mt-1">Deteksi Otomatis Jadwal Bentrok</p>
                </div>
                <div class="p-4 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-xs">
                    <p class="text-2xl sm:text-3xl font-black text-amber-300">Supabase & Cloud</p>
                    <p class="text-xs text-blue-200 font-medium mt-1">Enterprise Grade Security</p>
                </div>
            </div>

        </div>
    </section>

    <!-- FITUR UTAMA -->
    <section id="fitur" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-xs font-extrabold uppercase tracking-widest text-[#005AA9]">Ekosistem Terintegrasi</h2>
                <p class="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Solusi Tepat untuk Efisiensi Birokrasi & Manajemen Kerja</p>
                <p class="mt-4 text-base text-slate-600">Dirancang khusus untuk memenuhi alur kerja dan ritme kesibukan petugas sensus, pengolahan, hingga pimpinan BPS.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                
                <!-- Card 1 -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 hover:shadow-xl hover:border-blue-200 transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-[#005AA9] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Resource Timeline & Matrix</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Visualisasikan beban kerja setiap pegawai dan ketersediaan ruangan secara horizontal. Cegah kelebihan penugasan (*overloaded staff*).
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 hover:shadow-xl hover:border-blue-200 transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Peminjaman Fasilitas & Aset TU</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Pengajuan peminjaman proyektor, kendaraan dinas, laptop, dan aula rapat dalam satu klik dengan persetujuan cepat dari Subbagian Umum.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 hover:shadow-xl hover:border-blue-200 transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">AI Conflict Analyzer (BETA)</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Didukung model AI cerdas untuk mendeteksi potensi bentrok tanggal publikasi data, survei lapangan, dan ketersediaan personil.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- SECTION TIMELINE & HIGHLIGHT -->
    <section id="timeline" class="py-20 bg-slate-100 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-6 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#005AA9]/10 text-[#005AA9] text-xs font-bold rounded-lg uppercase">
                        Sinkronisasi Tanpa Batas
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Tampilan Kalender & Timeline Dinamis untuk Seluruh Tim Kerja
                    </h2>
                    <p class="text-slate-600 text-base leading-relaxed">
                        Setiap tim memiliki identitas warna tersendiri (IPDS, Nerwilis, Distribusi, Produksi, dll.). Memudahkan pimpinan memantau progres pelaksanaan survei secara holistik.
                    </p>
                    
                    <ul class="space-y-3 pt-2">
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Filter berdasarkan Tim Kerja, Kategori Sensus, dan Status</span>
                        </li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Notifikasi pengingat tenggat waktu (Deadline Alerts)</span>
                        </li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Integrasi database realtime Supabase PostgreSQL</span>
                        </li>
                    </ul>

                    <div class="pt-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 font-bold text-[#005AA9] hover:text-[#004582] text-sm group">
                            <span>Mulai pantau jadwal sekarang</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Graphic Preview Mockup -->
                <div class="lg:col-span-6">
                    <div class="p-4 bg-white rounded-3xl shadow-2xl border border-slate-200/80">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3 px-2">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            </div>
                            <span class="text-xs font-bold text-slate-400">Resource Timeline Matrix</span>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="p-3.5 rounded-xl bg-blue-50/80 border border-blue-200/80 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full bg-[#005AA9]"></span>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">Rilis Berita Resmi Statistik (BRS)</p>
                                        <p class="text-[11px] text-slate-500">Tim Nerwilis &bull; 09:00 - 12:00 WIB</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-600 text-white rounded-md">Terkonfirmasi</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-emerald-50/80 border border-emerald-200/80 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">Briefing Petugas Survei Pertanian</p>
                                        <p class="text-[11px] text-slate-500">Tim Produksi &bull; Ruang Aula Utama</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-600 text-white rounded-md">Berlangsung</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-purple-50/80 border border-purple-200/80 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full bg-purple-600"></span>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">Evaluasi Portal Satu Data Indonesia</p>
                                        <p class="text-[11px] text-slate-500">Tim IPDS &bull; Ruang Rapat Pimpinan</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-purple-600 text-white rounded-md">Terjadwal</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer id="tentang" class="bg-[#002B5C] text-white pt-16 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-12 border-b border-white/10">
                
                <div class="md:col-span-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-xl">
                            <img src="{{ asset('BPS Logo.svg') }}" alt="Logo BPS" class="h-6 w-auto brightness-0 invert">
                        </div>
                        <span class="text-xl font-black tracking-tight">BPS ACT</span>
                    </div>
                    <p class="text-sm text-blue-100/70 max-w-sm leading-relaxed">
                        Sistem Informasi Manajemen Kegiatan dan Pengelolaan Peminjaman Aset Badan Pusat Statistik Kabupaten Bangka.
                    </p>
                </div>

                <div class="md:col-span-3 space-y-3 text-sm">
                    <p class="font-bold text-white uppercase tracking-wider text-xs">Navigasi</p>
                    <ul class="space-y-2 text-blue-100/70 font-medium">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">Dashboard</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Masuk Pegawai</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar Akun</a></li>
                    </ul>
                </div>

                <div class="md:col-span-3 space-y-3 text-sm">
                    <p class="font-bold text-white uppercase tracking-wider text-xs">Kontak Kantor</p>
                    <p class="text-blue-100/70 text-xs leading-relaxed">
                        Jl. Ahmad Yani No. 1, Sungailiat, Kab. Bangka, Kepulauan Bangka Belitung.<br>
                        Email: bps1901@bps.go.id
                    </p>
                </div>

            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-blue-200/60 gap-4">
                <p>&copy; 2026 Badan Pusat Statistik Kabupaten Bangka. All rights reserved.</p>
                <p class="font-medium">BPS ACT Platform &bull; Made with pride for official statistics</p>
            </div>
        </div>
    </footer>

</body>
</html>
