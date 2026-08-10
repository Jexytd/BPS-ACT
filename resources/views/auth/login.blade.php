<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BPS Activity Tracker</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 antialiased font-sans">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
        <!-- Header BPS -->
        <div class="bg-bps-blue p-6 text-white text-center">
            <img src="{{ asset('BPS Logo.svg') }}" alt="BPS Logo" class="h-12 w-auto mx-auto mb-3 object-contain">
            <h1 class="text-xl font-bold tracking-tight">BPS ACT — Login Sesi</h1>
            <p class="text-xs text-blue-100 mt-1">Pencatatan & Perencanaan Kegiatan Tim Badan Pusat Statistik</p>
        </div>

        <div class="p-6">
            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-md">
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Pilih Pegawai / Akun Pengguna</label>
                <div class="space-y-2 mb-6">
                    @foreach($users as $u)
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-bps-blue hover:bg-blue-50/50 cursor-pointer transition">
                        <input type="radio" name="user_id" value="{{ $u['id'] }}" class="text-bps-blue focus:ring-bps-blue h-4 w-4" required {{ $loop->first ? 'checked' : '' }}>
                        <img src="{{ $u['photo'] ?? 'https://ui-avatars.com/api/?name='.urlencode($u['name']) }}" class="w-9 h-9 rounded-full border border-gray-300">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $u['name'] }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $u['email'] }}</p>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] uppercase font-bold rounded {{ $u['role'] === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                            {{ strtoupper($u['role']) }}
                        </span>
                    </label>
                    @endforeach
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-bps-blue hover:bg-bps-teal text-white font-semibold rounded-lg text-sm transition shadow-xs cursor-pointer">
                    Masuk ke Sistem BPS ACT &rarr;
                </button>
            </form>
        </div>
    </div>
</body>
</html>
