<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = [
            [
                'id' => 'ast_' . Str::random(10),
                'name' => 'Toyota Avanza BA 1234 XX',
                'category' => 'kendaraan',
                'description' => 'Mobil dinas operasional BPS',
                'status' => 'tersedia',
            ],
            [
                'id' => 'ast_' . Str::random(10),
                'name' => 'Toyota Innova BA 5678 YY',
                'category' => 'kendaraan',
                'description' => 'Mobil dinas pimpinan BPS',
                'status' => 'tersedia',
            ],
            [
                'id' => 'ast_' . Str::random(10),
                'name' => 'Ruang Rapat Utama',
                'category' => 'ruang_rapat',
                'description' => 'Kapasitas 30 orang, dilengkapi proyektor dan AC',
                'status' => 'tersedia',
            ],
            [
                'id' => 'ast_' . Str::random(10),
                'name' => 'Ruang Rapat Kecil',
                'category' => 'ruang_rapat',
                'description' => 'Kapasitas 10 orang',
                'status' => 'tersedia',
            ],
            [
                'id' => 'ast_' . Str::random(10),
                'name' => 'Laptop Asus ROG',
                'category' => 'peralatan',
                'description' => 'Laptop spek tinggi untuk pengolahan data berat',
                'status' => 'tersedia',
            ],
            [
                'id' => 'ast_' . Str::random(10),
                'name' => 'Proyektor Epson',
                'category' => 'peralatan',
                'description' => 'Proyektor portabel',
                'status' => 'tersedia',
            ],
        ];

        foreach ($assets as $asset) {
            Asset::updateOrCreate(
                ['name' => $asset['name']],
                $asset
            );
        }
    }
}
