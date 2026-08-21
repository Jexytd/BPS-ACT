<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class NotifyDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:notify-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa kegiatan yang mendekati batas waktu dan kirim notifikasi ke pembuat/tim.';

    /**
     * Execute the console command.
     */
    public function handle(FirestoreService $firestore)
    {
        $this->info('Memulai pengecekan tenggat waktu kegiatan...');
        
        $activities = $firestore->getCollection('activities');
        $now = Carbon::now();
        $notifiedCount = 0;

        foreach ($activities as $act) {
            $status = strtolower($act['status'] ?? 'planned');
            if (in_array($status, ['done', 'confirmed', 'closed', 'cancelled'])) {
                continue; // Sudah selesai atau dibatalkan, lewati
            }

            $endRaw = $act['end'] ?? ($act['due_date'] ?? null);
            if (!$endRaw) {
                continue; // Tidak ada waktu selesai, lewati
            }

            $startRaw = $act['start'] ?? ($act['start_date'] ?? null);
            $startObj = $startRaw ? Carbon::parse($startRaw) : null;
            
            $endObj = Carbon::parse($endRaw);
            
            // Periksa jika tanggal saja (tidak ada jam spesifik)
            $hasTime = strlen($endRaw) > 10 && !str_ends_with($endRaw, '00:00:00') && !str_ends_with($endRaw, '00:00:00Z');
            if (!$hasTime) {
                $endObj->setTime(16, 30, 0); // Asumsi selesai jam 16:30 sesuai konvensi app
            }

            // Hitung durasi kegiatan
            $durationHours = 24; // Default > 1 hari
            if ($startObj) {
                if (!$hasTime) {
                    $startObj->setTime(7, 30, 0);
                }
                $durationHours = $endObj->diffInHours($startObj);
            }

            $creatorId = $act['created_by'] ?? ($act['createdBy'] ?? null);
            if (!$creatorId) continue; // Tidak diketahui pembuatnya

            $notifiedKey = 'deadline_notified_' . $act['id'];
            
            // Cek apakah sudah pernah dinotifikasi
            // Dalam implementasi nyata, kita bisa menyimpan flag di firestore
            // Di sini kita gunakan query ke notifikasi untuk mengecek
            // Agar efisien, jika notifikasi banyak, kita hanya simpan timestamp/flag di kegiatan itu sendiri
            
            if (isset($act['deadline_notified_at'])) {
                continue; // Sudah pernah dinotifikasi
            }

            $shouldNotify = false;
            $hoursUntilEnd = $now->diffInHours($endObj, false); // false agar bisa negatif jika sudah lewat
            
            if ($hoursUntilEnd < 0) {
                continue; // Sudah lewat
            }

            if ($durationHours > 24) {
                // Kegiatan lebih dari 1 hari -> notifikasi H-1 (24 jam sebelum)
                if ($hoursUntilEnd <= 24) {
                    $shouldNotify = true;
                }
            } else {
                // Kegiatan singkat -> notifikasi 1 jam sebelum
                if ($hoursUntilEnd <= 1) {
                    $shouldNotify = true;
                }
            }

            if ($shouldNotify) {
                // Buat notifikasi
                $notification = [
                    'id' => Str::uuid()->toString(),
                    'user_id' => $creatorId,
                    'title' => 'Peringatan Tenggat Waktu Kegiatan',
                    'message' => 'Kegiatan "' . ($act['title'] ?? 'Tanpa Judul') . '" akan segera berakhir pada ' . $endObj->translatedFormat('d M Y H:i') . '. Jangan lupa untuk mengupdate statusnya!',
                    'type' => 'warning',
                    'link' => '/dashboard',
                    'is_read' => false,
                    'created_at' => $now->toIso8601String(),
                ];

                $firestore->createDocument('notifications', $notification['id'], $notification);

                // Update flag di activity agar tidak dinotif berulang
                $firestore->updateDocument('activities', $act['id'], [
                    'deadline_notified_at' => $now->toIso8601String()
                ]);

                $notifiedCount++;
                $this->info("Notifikasi dikirim untuk kegiatan ID: {$act['id']} (Sisa waktu: {$hoursUntilEnd} jam)");
            }
        }

        $this->info("Pengecekan selesai. {$notifiedCount} notifikasi terkirim.");
    }
}
