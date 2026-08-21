@extends('layouts.app')

@section('title', 'Asisten AI — BPS ACT')
@section('header_title', 'Asisten Cerdas (AI) BPS ACT')

@section('content')
<div x-data="aiApp()" class="space-y-6 max-w-4xl mx-auto">
    
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-bps-blue to-bps-teal p-8 rounded-2xl shadow-lg text-white text-center relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-10 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-bps-teal opacity-20 blur-xl"></div>
        
        <div class="relative z-10 flex flex-col items-center">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-4 shadow-inner border border-white/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Asisten Cerdas Tata Usaha</h2>
            <p class="text-blue-100 max-w-lg mx-auto text-sm md:text-base leading-relaxed">
                Analisis data peminjaman fasilitas secara otomatis. Dapatkan ringkasan kegiatan mingguan tim, klasifikasi, dan deteksi bentrok jadwal.
            </p>
            
            <button @click="analyze()" :disabled="loading" 
                    class="mt-6 px-6 py-3 bg-white text-bps-blue font-bold rounded-full shadow-lg hover:bg-gray-50 transition transform hover:-translate-y-0.5 disabled:opacity-75 disabled:cursor-not-allowed flex items-center gap-2">
                <span x-show="!loading">
                    <svg class="w-5 h-5 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Mulai Analisis Data Minggu Ini
                </span>
                <span x-show="loading" class="flex items-center gap-2">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Memproses Data...
                </span>
            </button>
        </div>
    </div>

    <!-- Alert Mock Mode -->
    <div x-show="isMock" x-cloak class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-amber-800">Mode Simulasi Aktif</h3>
                <p class="text-sm text-amber-700 mt-1">API Key OpenRouter belum diatur di file `.env`. Hasil di bawah ini adalah simulasi (mockup).</p>
            </div>
        </div>
    </div>

    <!-- Results Area -->
    <div x-show="result" x-transition.opacity.duration.500ms class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" x-cloak>
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-2 text-bps-blue font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan Hasil Analisis AI
            </div>
            <span class="text-xs text-gray-400 font-medium" x-text="new Date().toLocaleString('id-ID')"></span>
        </div>
        
        <!-- Markdown Output -->
        <div class="p-6 md:p-8 prose prose-blue max-w-none prose-headings:font-bold prose-h2:text-2xl prose-h3:text-lg prose-h3:text-bps-blue prose-li:text-gray-700 prose-p:text-gray-700 leading-relaxed" x-html="renderedMarkdown">
        </div>
    </div>

</div>

<!-- Include Marked.js for Markdown parsing -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
    function aiApp() {
        return {
            loading: false,
            result: '',
            isMock: false,

            get renderedMarkdown() {
                if (!this.result) return '';
                return marked.parse(this.result);
            },

            async analyze() {
                this.loading = true;
                this.result = '';
                this.isMock = false;

                try {
                    const res = await fetch('/api/ai/analyze', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (res.ok) {
                        const json = await res.json();
                        this.result = json.data;
                        this.isMock = json.is_mock;
                    } else {
                        const err = await res.json();
                        alert(err.message || 'Gagal menganalisis data.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan.');
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
