<?php

namespace App\Services;

use App\Models\Alternatif;
use App\Models\Kriteria;
use Illuminate\Support\Collection;

/**
 * Algoritma SAW (Simple Additive Weighting)
 *
 * Langkah:
 * 1. Matriks keputusan X  : x_ij = nilai alternatif i pada kriteria j
 * 2. Normalisasi R        : benefit -> r_ij = x_ij / max(x_j)
 *                           cost    -> r_ij = min(x_j) / x_ij
 * 3. Matriks terbobot     : w_j * r_ij
 * 4. Nilai preferensi     : V_i = Σ (w_j * r_ij)
 * 5. Perankingan          : V terbesar = alternatif terbaik
 */
class SawService
{
    public function hitung(): array
    {
        $kriteria = Kriteria::orderBy('kode')->get();
        $semuaAlternatif = Alternatif::with('penilaian')->orderBy('kode')->get();

        // --- Langkah 1: susun matriks keputusan X ---
        $matriks = [];
        $lengkap = collect();
        $tidakLengkap = collect();

        foreach ($semuaAlternatif as $alt) {
            $nilai = $alt->penilaian->pluck('nilai', 'kriteria_id');
            $baris = [];
            foreach ($kriteria as $k) {
                $baris[$k->id] = $nilai->has($k->id) ? (float) $nilai[$k->id] : null;
            }

            // Alternatif yang nilainya belum lengkap tidak ikut dihitung
            if ($kriteria->isNotEmpty() && ! in_array(null, $baris, true)) {
                $matriks[$alt->id] = $baris;
                $lengkap->push($alt);
            } else {
                $tidakLengkap->push($alt);
            }
        }

        // --- Bobot yang dipakai: otomatis dinormalisasi supaya Σw = 1 ---
        // Kalau total bobot bukan 100% (misal setelah menambah kriteria baru),
        // setiap bobot dibagi total bobot: w' = w / Σw
        $totalBobot = (float) $kriteria->sum('bobot');
        $bobot = [];
        foreach ($kriteria as $k) {
            $bobot[$k->id] = $totalBobot > 0 ? $k->bobot / $totalBobot : 0;
        }

        // --- Langkah 2: cari max/min tiap kriteria & normalisasi ---
        $pembagi = [];
        foreach ($kriteria as $k) {
            $kolom = array_column($matriks, $k->id);
            $pembagi[$k->id] = [
                'max' => $kolom ? max($kolom) : 0,
                'min' => $kolom ? min($kolom) : 0,
            ];
        }

        $normalisasi = [];
        $terbobot = [];
        $hasil = [];

        foreach ($lengkap as $alt) {
            $total = 0;
            foreach ($kriteria as $k) {
                $x = $matriks[$alt->id][$k->id];
                $r = $this->normalisasi($x, $k->jenis, $pembagi[$k->id]);

                $normalisasi[$alt->id][$k->id] = $r;
                $terbobot[$alt->id][$k->id] = $bobot[$k->id] * $r;   // Langkah 3
                $total += $bobot[$k->id] * $r;                       // Langkah 4
            }
            $hasil[] = ['alternatif' => $alt, 'nilai' => $total];
        }

        // --- Langkah 5: urutkan dari nilai V terbesar ---
        usort($hasil, fn ($a, $b) => $b['nilai'] <=> $a['nilai']);
        foreach ($hasil as $i => &$baris) {
            $baris['peringkat'] = $i + 1;
        }
        unset($baris);

        return [
            'kriteria' => $kriteria,
            'alternatif' => $lengkap,
            'tidakLengkap' => $tidakLengkap,
            'matriks' => $matriks,
            'pembagi' => $pembagi,
            'normalisasi' => $normalisasi,
            'terbobot' => $terbobot,
            'hasil' => collect($hasil),
            'totalBobot' => round($totalBobot, 4),
            'bobot' => $bobot,                                    // bobot yang dipakai (Σ = 1)
            'bobotDisesuaikan' => abs($totalBobot - 1) > 0.0005,
        ];
    }

    private function normalisasi(float $x, string $jenis, array $pembagi): float
    {
        if ($jenis === 'cost') {
            // nilai lebih kecil lebih baik
            return $x == 0 ? 1.0 : $pembagi['min'] / $x;
        }

        // benefit: nilai lebih besar lebih baik
        return $pembagi['max'] == 0 ? 0.0 : $x / $pembagi['max'];
    }
}
