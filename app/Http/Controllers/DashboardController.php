<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Services\SawService;

class DashboardController extends Controller
{
    public function __invoke(SawService $saw)
    {
        $data = $saw->hitung();
        $hasil = $data['hasil'];

        $jumlahAlternatif = $data['alternatif']->count() + $data['tidakLengkap']->count();

        $terbaik = $hasil->first();
        $kedua = $hasil->get(1);

        return view('dashboard', [
            ...$data,
            'jumlahAlternatif' => $jumlahAlternatif,
            'nilaiTerisi' => Penilaian::count(),
            'nilaiDibutuhkan' => $jumlahAlternatif * $data['kriteria']->count(),
            'terbaik' => $terbaik,
            'kedua' => $kedua,
            'selisih' => $terbaik && $kedua ? $terbaik['nilai'] - $kedua['nilai'] : null,
        ]);
    }
}
