@extends('layouts.app')

@section('title', 'Semua Notifikasi Tim — BPS ACT')
@section('header_title', 'Daftar Semua Notifikasi Kegiatan Tim')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="notificationsPage()">

    <div class="bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden">
        <div class="bg-bps-blue px-6 py-4 flex items-center justify-between text-white">
            <h2 class="font-bold text-lg">Riwayat Kegiatan Tim BPS Anda</h2>
            <span class="text-sm bg-white/20 px-3 py-1 rounded-full font-medium">{{ $notifications->count() }} Kegiatan Ditemukan</span>
        </div>

        @if($notifications->isNotEmpty())
            <ul class="divide-y divide-gray-200">
                @foreach($notifications as $notif)
                @php
                    $creator = collect($users)->firstWhere('id', $notif['createdBy']);
                    $isRead = in_array($user['id'], $notif['readBy'] ?? []);
                @endphp
                <li id="page-notif-{{ $notif['id'] }}" class="p-5 flex items-start justify-between gap-4 transition hover:bg-gray-50 {{ $isRead ? 'bg-white opacity-80' : 'bg-blue-50/50' }}">
                    <div class="flex-1 cursor-pointer" @click="readNotification('{{ $notif['id'] }}')">
                        <div class="flex items-center gap-2 mb-1">
                            @if(!$isRead)
                            <span class="w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse shadow-sm"></span>
                            @endif
                            <p class="text-base text-gray-800 leading-snug">
                                <span class="font-bold text-bps-blue">{{ $creator['name'] ?? 'Sistem' }}</span>
                                mendaftarkan kegiatan 
                                <span class="font-bold text-gray-900">"{{ $notif['title'] ?? $notif['subject'] }}"</span>
                            </p>
                        </div>
                        <div class="ml-4 space-y-1">
                            <p class="text-sm text-gray-600 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="font-medium text-gray-800">Tanggal:</span> {{ \Carbon\Carbon::parse($notif['start'] ?? $notif['start_date'] ?? null)->format('d M Y, H:i') }}
                                @if(isset($notif['end']) || isset($notif['due_date']))
                                 - {{ \Carbon\Carbon::parse($notif['end'] ?? $notif['due_date'] ?? null)->format('d M Y, H:i') }}
                                @endif
                            </p>
                            @if(!empty($notif['location']))
                            <p class="text-sm text-gray-600 flex items-center gap-1.5 mt-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="font-medium text-gray-800">Lokasi:</span> {{ $notif['location'] }}
                            </p>
                            @endif
                            @if(!empty($notif['description']))
                            <p class="text-sm text-gray-500 italic line-clamp-2 mt-2">"{{ $notif['description'] }}"</p>
                            @endif
                            
                            <p class="text-xs text-gray-400 mt-2">Dicatat pada: {{ \Carbon\Carbon::parse($notif['created_at'])->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-2 flex-shrink-0">
                        @if(!$isRead)
                        <button @click="readNotification('{{ $notif['id'] }}')" class="px-3 py-1.5 text-xs font-bold text-bps-blue bg-blue-50 border border-blue-200 hover:bg-blue-100 rounded shadow-sm transition">
                            Tandai Terbaca
                        </button>
                        @endif
                        <button @click="deleteNotificationItem('{{ $notif['id'] }}')" class="px-3 py-1.5 text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 hover:bg-rose-100 rounded shadow-sm transition">
                            Hapus Notifikasi
                        </button>
                    </div>
                </li>
                @endforeach
            </ul>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Peti Kosong</h3>
                <p class="text-gray-500">Belum ada notifikasi kegiatan dari tim Anda saat ini.</p>
                <a href="{{ route('dashboard') }}" class="inline-block mt-4 px-4 py-2 bg-bps-blue text-white rounded-lg font-bold text-sm shadow-md hover:bg-bps-teal transition">Kembali ke Kalender</a>
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    function notificationsPage() {
        return {
            async readNotification(id) {
                try {
                    await fetch(`/api/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    let el = document.getElementById('page-notif-' + id);
                    if(el) {
                        el.classList.remove('bg-blue-50/50');
                        el.classList.add('bg-white', 'opacity-80');
                        const indicator = el.querySelector('.animate-pulse');
                        if (indicator) indicator.remove();
                        const btn = el.querySelector('button.text-bps-blue');
                        if (btn) btn.remove();
                    }
                } catch(e) {}
            },

            async deleteNotificationItem(id) {
                if(!confirm('Anda yakin ingin menghapus notifikasi ini dari riwayat Anda? Kegiatan aslinya tidak akan terhapus.')) return;
                try {
                    const res = await fetch(`/api/notifications/${id}/delete`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    if(res.ok) {
                        let el = document.getElementById('page-notif-' + id);
                        if(el) el.remove();
                    }
                } catch(e) {}
            }
        }
    }
</script>
@endsection
