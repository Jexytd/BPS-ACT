@extends('layouts.app')

@section('title', 'Executive Dashboard — BPS ACT')
@section('header_title', 'Executive Dashboard & Beban Kerja Tim')

@section('content')
<div class="space-y-6">
    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Metric 1: Activities This Week -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-gray-500 tracking-wider">Kegiatan Minggu Ini</span>
                <span class="p-2 bg-blue-50 text-bps-blue rounded-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900">{{ $activitiesThisWeek->count() }}</span>
                <span class="text-xs text-gray-500">kegiatan</span>
            </div>
            <p class="text-xs text-gray-400 mt-1">Rentang 7 hari berjalan</p>
        </div>

        <!-- Metric 2: Total Activities -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-gray-500 tracking-wider">Total Kegiatan Terdaftar</span>
                <span class="p-2 bg-teal-50 text-bps-teal rounded-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900">{{ $totalActivities }}</span>
                <span class="text-xs text-gray-500">paket kerja</span>
            </div>
            <p class="text-xs text-gray-400 mt-1">Firestore Database</p>
        </div>

        <!-- Metric 3: Completion Rate -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-gray-500 tracking-wider">Tingkat Penyelesaian</span>
                <span class="p-2 bg-emerald-50 text-bps-green rounded-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900">{{ $completionRate }}%</span>
                <span class="text-xs text-emerald-600 font-medium">Completed</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                <div class="bg-bps-green h-1.5 rounded-full" style="width: {{ $completionRate }}%"></div>
            </div>
        </div>

        <!-- Metric 4: Overdue & Unassigned -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-gray-500 tracking-wider">Perlu Perhatian</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-amber-600">{{ $overdueList->count() }}</span>
                <span class="text-xs text-gray-500">kegiatan</span>
            </div>
            <p class="text-xs text-amber-700 mt-1">Terlewat / Belum di-assign</p>
        </div>
    </div>

    <!-- Main Content Split: Per-person Load & Overdue Table -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Per-Person Workload Bar -->
        <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="font-bold text-sm text-gray-900">Beban Kerja Tim per Anggota</h3>
                <span class="text-xs text-gray-400">Total Kegiatan</span>
            </div>

            <div class="space-y-3">
                @forelse($userWorkload as $item)
                @php $u = $item['user']; @endphp
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <img src="{{ $u['photo'] ?? 'https://ui-avatars.com/api/?name='.urlencode($u['name']) }}" class="w-6 h-6 rounded-full border border-gray-200">
                            <span class="font-semibold text-gray-800">{{ $u['name'] }}</span>
                            <span class="text-[10px] text-gray-400">({{ $item['division']['code'] ?? 'DIV' }})</span>
                        </div>
                        <span class="font-bold text-bps-blue">{{ $item['count'] }} kegiatan</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        @php $percent = $totalActivities > 0 ? min(100, round(($item['count'] / max(1, $totalActivities)) * 100)) : 0; @endphp
                        <div class="bg-bps-blue h-2 rounded-full transition-all duration-300" style="width: {{ max(10, $percent) }}%"></div>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-xs text-gray-400">Belum ada data anggota tim.</div>
                @endforelse
            </div>
        </div>

        <!-- Right: Overdue / Action Required List -->
        <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 p-5 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-gray-900">Daftar Kegiatan Terlewat / Belum Di-assign</h3>
                    <p class="text-xs text-gray-500">Memerlukan penyesuaian jadwal atau alokasi tim</p>
                </div>
                <a href="{{ route('activities.index') }}" class="text-xs text-bps-blue font-semibold hover:underline flex items-center gap-1">
                    Buka Kalender &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 border-b border-gray-200">
                            <th class="py-2.5 px-3 font-semibold">ID</th>
                            <th class="py-2.5 px-3 font-semibold">Nama Kegiatan</th>
                            <th class="py-2.5 px-3 font-semibold">Assignee</th>
                            <th class="py-2.5 px-3 font-semibold">Status</th>
                            <th class="py-2.5 px-3 font-semibold">Batas Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($overdueList as $act)
                        @php 
                            $assigneeId = $act['assignees'][0] ?? ($act['assignee_id'] ?? null);
                            $assigneeUser = $users->get($assigneeId);
                        @endphp
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="py-2.5 px-3 font-mono text-gray-500">#{{ $act['id'] }}</td>
                            <td class="py-2.5 px-3 font-semibold text-gray-900">{{ $act['title'] ?? $act['subject'] }}</td>
                            <td class="py-2.5 px-3">
                                @if($assigneeUser)
                                <div class="flex items-center gap-1.5">
                                    <img src="{{ $assigneeUser['photo'] ?? '' }}" class="w-5 h-5 rounded-full">
                                    <span class="text-gray-700">{{ $assigneeUser['name'] }}</span>
                                </div>
                                @else
                                <span class="px-2 py-0.5 rounded text-[10px] bg-red-100 text-red-700 font-bold">Unassigned</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                    {{ $act['status'] ?? 'planned' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-red-600 font-medium">
                                {{ \Carbon\Carbon::parse($act['end'] ?? $act['due_date'])->format('d M Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">
                                <svg class="w-8 h-8 text-emerald-500 mx-auto mb-2 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Semua kegiatan tim dalam status aman & terjadwal!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
