<?php

namespace App\Http\Controllers;

use App\Models\BorrowingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AiAgentController extends Controller
{
    public function index()
    {
        return view('ai.assistant');
    }

    public function analyze()
    {
        // Ambil data peminjaman 7 hari terakhir hingga ke depan
        $startDate = Carbon::now()->subDays(7)->toDateString();
        $endDate = Carbon::now()->addDays(7)->toDateString();
        
        $requests = BorrowingRequest::with(['user', 'asset'])
            ->where('borrow_date', '>=', $startDate)
            ->where('borrow_date', '<=', $endDate)
            ->get();

        $dataContext = $requests->map(function($req) {
            return [
                'peminjam' => $req->user->name ?? 'Unknown',
                'divisi' => $req->user->team ?? 'Unknown',
                'aset' => $req->asset->name ?? 'Unknown',
                'kategori_aset' => $req->asset->category ?? 'Unknown',
                'tanggal_pinjam' => $req->borrow_date,
                'tanggal_kembali' => $req->return_date,
                'tujuan' => $req->purpose,
                'status' => $req->status,
            ];
        })->toArray();

        $apiKey = env('OPENROUTER_API_KEY');

        if (empty($apiKey)) {
            // Simulasi respon jika API Key belum diatur
            return response()->json([
                'status' => 'success',
                'is_mock' => true,
                'data' => "## 🤖 (Mode Simulasi) Analisis Asisten AI BPS ACT\n\n*Catatan: API Key OpenRouter belum dikonfigurasi. Berikut adalah contoh simulasi hasil.* \n\n### 📊 Ringkasan Kegiatan Mingguan\nBerdasarkan data peminjaman aset minggu ini, mayoritas kegiatan difokuskan pada kegiatan survei lapangan ke desa-desa, menggunakan kendaraan dinas (Avanza, Innova). Terdapat juga beberapa rapat koordinasi internal menggunakan Ruang Rapat Utama.\n\n### 🏷 Klasifikasi Kegiatan\n- **Kegiatan Lapangan / Survei:** 60% peminjaman aset ditujukan untuk perjalanan dinas dan survei BPS.\n- **Rapat / Koordinasi Internal:** 30% peminjaman berpusat pada Ruang Rapat.\n- **Lain-lain:** 10% peminjaman peralatan IT.\n\n### ⚠️ Deteksi Bentrok Jadwal\n- **Perhatian:** Sdr. Budi Santoso tercatat meminjam Kendaraan Dinas (Avanza) sekaligus Ruang Rapat pada tanggal 22 Agustus 2026. Mohon TU memverifikasi apakah ini kesalahan input.\n\n### 💡 Saran Prioritas\n- Pastikan ketersediaan proyektor untuk rapat besar hari Jumat karena jadwal rapat sangat padat.\n- Kendaraan dinas nomor polisi BN 1234 XY perlu segera diservis setelah kembali dari perjalanan dinas panjang besok."
            ]);
        }

        $prompt = "Kamu adalah Asisten AI untuk tim Tata Usaha (TU) BPS. Tugasmu adalah menganalisis data peminjaman fasilitas kantor berikut (dalam format JSON). \n"
                . "Berdasarkan data tersebut, berikan:\n"
                . "1. Ringkasan kegiatan mingguan tim secara umum.\n"
                . "2. Klasifikasi otomatis jenis kegiatan (misal: Rapat, Survei Lapangan, dll).\n"
                . "3. Deteksi bentrok jadwal (misal: orang yang sama meminjam aset berbeda untuk tujuan berbeda di waktu yang sama, atau aset yang sama dipinjam overlapping meski seharusnya ditangani sistem, cek apakah ada anomali).\n"
                . "4. Saran prioritas untuk staf TU dalam mengelola fasilitas minggu ini.\n\n"
                . "Gunakan bahasa Indonesia yang profesional namun luwes, format dengan Markdown (bold, list, header) agar mudah dibaca.\n\n"
                . "Data Peminjaman:\n" . json_encode($dataContext, JSON_PRETTY_PRINT);

        try {
            $url = 'https://openrouter.ai/api/v1/chat/completions';
            
            Log::info('OpenRouter API Request URL (masked key): ' . str_replace($apiKey, '***MASKED***', $url));

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => url('/'), // Penting untuk OpenRouter
                'X-Title' => 'BPS ACT', // Penting untuk OpenRouter
            ])->timeout(30)->post($url, [
                'model' => 'openai/gpt-4o-mini', // Menggunakan model GPT-4o-mini yang dijamin tersedia di OpenRouter
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            Log::info('OpenRouter API Response Status: ' . $response->status());

            if ($response->successful()) {
                $result = $response->json();
                $text = $result['choices'][0]['message']['content'] ?? 'Tidak dapat menghasilkan analisis.';
                return response()->json([
                    'status' => 'success',
                    'is_mock' => false,
                    'data' => $text
                ]);
            }

            Log::error('OpenRouter API Error (HTTP ' . $response->status() . '): ' . $response->body());
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal menghubungi OpenRouter API (HTTP ' . $response->status() . '). Pastikan API Key valid dari OpenRouter.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('OpenRouter API Exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem saat menghubungi AI: ' . $e->getMessage()], 500);
        }
    }
}
