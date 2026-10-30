<?php

namespace Database\Seeders;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Database\Seeder;

/**
 * Data awal dari spreadsheet BaselineSPK_Kelompok[4]
 * Studi kasus: RSUD Harapan Sehat – Pemilihan Vendor SIMRS (Metode SAW)
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Jangan isi ulang kalau data sudah ada (aman dijalankan setiap deploy)
        if (Kriteria::exists() || Alternatif::exists()) {
            $this->command?->info('Data sudah ada, seeder dilewati.');

            return;
        }

        $kriteria = [
            ['K1', 'Biaya Investasi & Lisensi', 'Rp Juta', 0.15, 'cost'],
            ['K2', 'Kelengkapan Fungsionalitas', 'Skor 0-100', 0.25, 'benefit'],
            ['K3', 'Keamanan & Enkripsi Data', 'Skor 0-100', 0.25, 'benefit'],
            ['K4', 'Dukungan Teknis 24/7', 'Bulan', 0.10, 'benefit'],
            ['K5', 'Kemudahan UI/UX', 'Skala 1-5', 0.10, 'benefit'],
            ['K6', 'Waktu Implementasi', 'Minggu', 0.15, 'cost'],
        ];

        foreach ($kriteria as [$kode, $nama, $satuan, $bobot, $jenis]) {
            Kriteria::create(compact('kode', 'nama', 'satuan', 'bobot', 'jenis'));
        }

        // kode, nama, [K1, K2, K3, K4, K5, K6]
        $alternatif = [
            ['V1', 'Medika Soft',        [350, 85, 90, 12, 4, 8]],
            ['V2', 'HealthTech Solusi',  [420, 92, 95, 24, 5, 12]],
            ['V3', 'Siber Medika',       [280, 75, 80, 6, 3, 6]],
            ['V4', 'Nusantara Care',     [310, 80, 85, 12, 4, 7]],
            ['V5', 'ProHospital IT',     [450, 95, 92, 36, 4, 14]],
            ['V6', 'Smart Clinic OS',    [250, 70, 75, 3, 5, 4]],
            ['V7', 'Prima Sistem',       [380, 88, 88, 12, 3, 10]],
            ['V8', 'Data Medis Utama',   [400, 90, 98, 24, 4, 11]],
        ];

        $listKriteria = Kriteria::orderBy('kode')->get();

        foreach ($alternatif as [$kode, $nama, $nilai]) {
            $alt = Alternatif::create(compact('kode', 'nama'));
            foreach ($listKriteria as $i => $k) {
                Penilaian::create([
                    'alternatif_id' => $alt->id,
                    'kriteria_id' => $k->id,
                    'nilai' => $nilai[$i],
                ]);
            }
        }
    }
}
