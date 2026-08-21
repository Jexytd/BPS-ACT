@extends('layouts.app')

@section('title', 'Manajemen Pengguna — BPS ACT')
@section('header_title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg flex items-center gap-3">
        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-sm font-bold">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-700">Daftar Akun Pegawai</h3>
            <span class="px-3 py-1 bg-bps-blue text-white text-xs font-bold rounded-full shadow-sm">{{ count($users) }} Pengguna</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-white border-b border-gray-200 text-gray-600 font-bold">
                    <tr>
                        <th class="px-6 py-4">Nama Pegawai</th>
                        <th class="px-6 py-4">Tim/Divisi</th>
                        <th class="px-6 py-4">Role Akses</th>
                        <th class="px-6 py-4">Terdaftar Pada</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $photoUrl = $user->photo;
                                        if (!$photoUrl) {
                                            $photoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0033a0&color=fff&size=64';
                                        }
                                    @endphp
                                    <img src="{{ $photoUrl }}" class="w-10 h-10 rounded-full border border-gray-200 object-cover">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-800">{{ $user->team ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('users.update_role', $user->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" 
                                            class="text-xs font-bold px-2.5 py-1.5 rounded-md border-gray-300 shadow-sm focus:border-bps-blue focus:ring focus:ring-bps-blue/20 transition
                                            {{ $user->role === 'admin' ? 'bg-amber-50 text-amber-800 border-amber-200' : ($user->role === 'lead' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-gray-50 text-gray-700') }}"
                                            {{ $user->id === auth()->id() && auth()->user()->role === 'admin' ? 'disabled' : '' }}>
                                        <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="lead" {{ $user->role === 'lead' ? 'selected' : '' }}>Ketua Tim (Lead)</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin (TU)</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-600">{{ $user->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus pengguna ini secara permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold transition flex items-center gap-1 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 italic">Akun Anda</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
