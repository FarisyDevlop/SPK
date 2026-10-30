<?php

namespace App\Http\Controllers;

use App\Services\SawService;

class PerhitunganController extends Controller
{
    public function __construct(private SawService $saw)
    {
    }

    /** Langkah-langkah perhitungan SAW */
    public function index()
    {
        return view('perhitungan.index', $this->saw->hitung());
    }

    /** Hasil akhir / perankingan */
    public function hasil()
    {
        return view('hasil.index', $this->saw->hitung());
    }
}
