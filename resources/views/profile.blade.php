@extends('layouts.app')

@section('title', 'Profil Saya — BPS ACT')
@section('header_title', 'Pengaturan Profil')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg">
        <div class="flex items-center gap-3 mb-2">
            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-bold">Terjadi Kesalahan</span>
        </div>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-8 pb-8 border-b border-gray-100">
                <div class="relative group">
                    @php
                        $photoUrl = auth()->user()->photo;
                        if (!$photoUrl) {
                            $photoUrl = 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0033a0&color=fff&size=128';
                        }
                    @endphp
                    <img id="photo-preview" src="{{ $photoUrl }}" class="w-24 h-24 rounded-full object-cover border-4 border-gray-50 shadow-sm">
                    
                    <label class="absolute inset-0 flex items-center justify-center bg-black/50 text-white rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <input type="file" name="photo" accept="image/*" class="hidden" onchange="previewImage(event)">
                    </label>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1 capitalize">Role: <span class="font-bold text-bps-blue">{{ auth()->user()->role }}</span></p>
                    <p class="text-xs text-gray-400 mt-2">Klik foto untuk mengubah foto profil (Max 2MB).</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-1">Email Dinas BPS</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm">
                </div>

                <div class="pt-4 mt-4 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-900 mb-4">Ubah Kata Sandi (Opsional)</h4>
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Kata Sandi Baru</label>
                        <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm">
                    </div>
                </div>

                <div class="pt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-bps-blue hover:bg-bps-teal text-white text-sm font-bold rounded-lg shadow-md transition">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photo-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
