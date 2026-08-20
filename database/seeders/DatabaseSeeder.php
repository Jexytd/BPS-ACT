<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Divisions (Tim Kerja di BPS Kabupaten Bangka)
        $divisions = [
            [
                'id' => 'div_ipds',
                'name' => 'Tim Kerja Pengolahan dan Teknologi Informasi',
                'code' => 'IPDS',
                'color' => '#00A6B4',
            ],
            [
                'id' => 'div_nerwilis',
                'name' => 'Tim Kerja Nerwilis dan Tim Kerja UKP',
                'code' => 'NERWILIS',
                'color' => '#005AA9',
            ],
            [
                'id' => 'div_pss',
                'name' => 'Tim Pembinaan Statistik Sektoral',
                'code' => 'PSS',
                'color' => '#7C3AED',
            ],
            [
                'id' => 'div_prod',
                'name' => 'Tim Kerja Statistik Produksi',
                'code' => 'PROD',
                'color' => '#16A34A',
            ],
            [
                'id' => 'div_mitra',
                'name' => 'Tim Kerja Manajemen Lapangan dan Mitra',
                'code' => 'MITRA',
                'color' => '#EA580C',
            ],
            [
                'id' => 'div_dist',
                'name' => 'Tim Kerja Statistik Distribusi',
                'code' => 'DIST',
                'color' => '#0284C7',
            ],
            [
                'id' => 'div_sensus',
                'name' => 'Tim Kerja Sensus dan Pengembangan Survei',
                'code' => 'SENSUS',
                'color' => '#D97706',
            ],
            [
                'id' => 'div_diseminasi',
                'name' => 'Tim Kerja Diseminasi Statistik dan Hubungan Masyarakat',
                'code' => 'DISEMINASI',
                'color' => '#DB2777',
            ],
            [
                'id' => 'div_kualitas',
                'name' => 'Tim Kerja Penjaminan Kualitas dan Manajemen Resiko',
                'code' => 'PKMR',
                'color' => '#4B5563',
            ],
        ];

        foreach ($divisions as $div) {
            Division::updateOrCreate(['id' => $div['id']], $div);
        }

        // 2. Seed Sample Users with default password: 'password'
        $users = [
            [
                'id' => 'usr_catherine',
                'name' => 'Catherine Johnson',
                'email' => 'catherine@bps.go.id',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'division_id' => 'div_nerwilis',
                'photo' => 'https://ui-avatars.com/api/?name=Catherine+Johnson&background=005AA9&color=fff',
            ],
            [
                'id' => 'usr_daphne',
                'name' => 'Daphne Turner',
                'email' => 'daphne@bps.go.id',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'division_id' => 'div_ipds',
                'photo' => 'https://ui-avatars.com/api/?name=Daphne+Turner&background=00A6B4&color=fff',
            ],
            [
                'id' => 'usr_leonard',
                'name' => 'Leonard Douglas',
                'email' => 'leonard@bps.go.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'division_id' => 'div_ipds',
                'photo' => 'https://ui-avatars.com/api/?name=Leonard+Douglas&background=6DBE45&color=fff',
            ],
            [
                'id' => 'usr_maya',
                'name' => 'Maya Berdygylyjova',
                'email' => 'maya@bps.go.id',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'division_id' => 'div_prod',
                'photo' => 'https://ui-avatars.com/api/?name=Maya+Berdygylyjova&background=ED8B00&color=fff',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['id' => $u['id']], $u);
        }

        // 3. Seed Sample Activities
        $activities = [
            [
                'id' => '742',
                'title' => 'Rilis Berita Resmi Statistik (BRS)',
                'subject' => 'Rilis Berita Resmi Statistik (BRS)',
                'project_name' => 'Portal Publik BPS Bangka',
                'description' => 'Persiapan materi dan siaran pers indikator strategis BPS Kabupaten Bangka.',
                'start' => '2026-06-08T09:00:00Z',
                'end' => '2026-06-09T17:00:00Z',
                'start_date' => '2026-06-08',
                'due_date' => '2026-06-09',
                'all_day' => true,
                'status' => 'Confirmed',
                'category' => 'Diseminasi',
                'location' => 'Ruang Pelayanan Statistik Terpadu (PST)',
                'created_by' => 'usr_leonard',
                'assignee_id' => 'usr_catherine',
                'division_id' => 'div_diseminasi',
                'assignees' => ['usr_catherine', 'usr_daphne'],
                'result' => 'Publikasi dan BRS berhasil dirilis tepat waktu.',
            ],
            [
                'id' => '743',
                'title' => 'Survei Sosial Ekonomi Nasional (Susenas)',
                'subject' => 'Susenas 2026 Tahap 1',
                'project_name' => 'Susenas',
                'description' => 'Pengumpulan dan validasi kuesioner rumah tangga sampel di wilayah Kabupaten Bangka.',
                'start' => '2026-06-10T08:00:00Z',
                'end' => '2026-06-15T16:00:00Z',
                'start_date' => '2026-06-10',
                'due_date' => '2026-06-15',
                'all_day' => true,
                'status' => 'In progress',
                'category' => 'Survei Lapangan',
                'location' => 'Kecamatan Sungailiat & Sekitarnya',
                'created_by' => 'usr_catherine',
                'assignee_id' => 'usr_catherine',
                'division_id' => 'div_mitra',
                'assignees' => ['usr_catherine'],
                'result' => 'Progres pencacahan mencapai 60%.',
            ],
            [
                'id' => '744',
                'title' => 'Rekonsiliasi Data Statistik Produksi Pertanian',
                'subject' => 'Rekonsiliasi Data Statistik Produksi',
                'project_name' => 'Statistik Produksi',
                'description' => 'Sinkronisasi data hasil panen padi dan perkebunan lada / kelapa sawit.',
                'start' => '2026-06-12T09:00:00Z',
                'end' => '2026-06-14T15:00:00Z',
                'start_date' => '2026-06-12',
                'due_date' => '2026-06-14',
                'all_day' => true,
                'status' => 'In specification',
                'category' => 'Pengolahan Data',
                'location' => 'Ruang Rapat BPS Kabupaten Bangka',
                'created_by' => 'usr_maya',
                'assignee_id' => 'usr_maya',
                'division_id' => 'div_prod',
                'assignees' => ['usr_maya'],
            ],
        ];

        foreach ($activities as $act) {
            Activity::updateOrCreate(['id' => $act['id']], $act);
        }
    }
}
