<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    /** Tampilkan matriks keputusan (alternatif x kriteria) */
    public function index()
    {
        $kriteria = Kriteria::orderBy('kode')->get();
        $alternatif = Alternatif::with('penilaian')->orderBy('kode')->get();

        return view('penilaian.index', compact('kriteria', 'alternatif'));
    }

    /** Form input / ubah nilai satu alternatif */
    public function edit(Alternatif $alternatif)
    {
        $kriteria = Kriteria::orderBy('kode')->get();
        $nilai = $alternatif->penilaian()->pluck('nilai', 'kriteria_id');

        return view('penilaian.form', compact('alternatif', 'kriteria', 'nilai'));
    }

    public function update(Request $request, Alternatif $alternatif)
    {
        $kriteria = Kriteria::all();

        $aturan = [];
        foreach ($kriteria as $k) {
            $aturan["nilai.{$k->id}"] = ['required', 'numeric', 'min:0'];
        }
        $request->validate($aturan, [], $kriteria->mapWithKeys(
            fn ($k) => ["nilai.{$k->id}" => "nilai {$k->kode}"]
        )->all());

        foreach ($kriteria as $k) {
            Penilaian::updateOrCreate(
                ['alternatif_id' => $alternatif->id, 'kriteria_id' => $k->id],
                ['nilai' => $request->input("nilai.{$k->id}")]
            );
        }

        return redirect()->route('penilaian.index')->with('sukses', "Nilai {$alternatif->kode} – {$alternatif->nama} berhasil disimpan.");
    }

    /** Kosongkan semua nilai milik satu alternatif */
    public function destroy(Alternatif $alternatif)
    {
        $alternatif->penilaian()->delete();

        return redirect()->route('penilaian.index')->with('sukses', "Nilai {$alternatif->kode} berhasil dikosongkan.");
    }
}
