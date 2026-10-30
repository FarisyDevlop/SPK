<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::orderBy('kode')->get();
        $totalBobot = round($kriteria->sum('bobot'), 4);

        return view('kriteria.index', compact('kriteria', 'totalBobot'));
    }

    public function create()
    {
        return $this->form(new Kriteria());
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        DB::transaction(function () use ($request, $data) {
            $kriteria = Kriteria::create($data);
            $this->simpanNilai($request, $kriteria);
        });

        return redirect()->route('kriteria.index')->with('sukses', 'Kriteria dan nilai tiap vendor berhasil disimpan.');
    }

    public function edit(Kriteria $kriteria)
    {
        return $this->form($kriteria);
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        $data = $this->validasi($request, $kriteria);

        DB::transaction(function () use ($request, $kriteria, $data) {
            $kriteria->update($data);
            $this->simpanNilai($request, $kriteria);
        });

        return redirect()->route('kriteria.index')->with('sukses', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete(); // nilai penilaian ikut terhapus (cascade)

        return redirect()->route('kriteria.index')->with('sukses', 'Kriteria berhasil dihapus.');
    }

    /**
     * Ubah semua bobot supaya totalnya tepat 100%.
     * Rumus: w baru = w lama / Σw (perbandingan antar kriteria tetap sama).
     */
    public function normalisasi()
    {
        $kriteria = Kriteria::orderBy('kode')->get();
        $total = $kriteria->sum('bobot');

        if ($kriteria->isEmpty() || $total <= 0) {
            return back();
        }

        DB::transaction(function () use ($kriteria, $total) {
            // dibulatkan 4 desimal; bobot yang awalnya sama tetap sama
            foreach ($kriteria as $k) {
                $k->update(['bobot' => round($k->bobot / $total, 4)]);
            }
        });

        return back()->with('sukses', 'Bobot sudah disesuaikan, totalnya sekarang 100%.');
    }

    private function form(Kriteria $kriteria)
    {
        $alternatif = Alternatif::orderBy('kode')->get();
        $nilai = $kriteria->exists
            ? $kriteria->penilaian()->pluck('nilai', 'alternatif_id')
            : collect();
        $totalBobotLain = round(Kriteria::where('id', '!=', $kriteria->id ?? 0)->sum('bobot'), 4);

        return view('kriteria.form', compact('kriteria', 'alternatif', 'nilai', 'totalBobotLain'));
    }

    /** Simpan nilai setiap alternatif untuk kriteria ini */
    private function simpanNilai(Request $request, Kriteria $kriteria): void
    {
        foreach ($request->input('nilai', []) as $alternatifId => $nilai) {
            if ($nilai === null || $nilai === '') {
                continue;
            }
            Penilaian::updateOrCreate(
                ['alternatif_id' => $alternatifId, 'kriteria_id' => $kriteria->id],
                ['nilai' => $nilai]
            );
        }
    }

    private function validasi(Request $request, ?Kriteria $kriteria = null): array
    {
        $alternatif = Alternatif::orderBy('kode')->get();

        $aturan = [
            'kode' => ['required', 'string', 'max:10', Rule::unique('kriteria', 'kode')->ignore($kriteria?->id)],
            'nama' => ['required', 'string', 'max:255'],
            'satuan' => ['nullable', 'string', 'max:100'],
            'bobot' => ['required', 'numeric', 'gt:0', 'lte:1'],
            'jenis' => ['required', Rule::in(['benefit', 'cost'])],
        ];
        $label = ['kode' => 'kode kriteria', 'nama' => 'nama kriteria'];

        // Nilai tiap vendor wajib diisi supaya kriteria baru langsung ikut dihitung
        foreach ($alternatif as $a) {
            $aturan["nilai.{$a->id}"] = ['required', 'numeric', 'min:0'];
            $label["nilai.{$a->id}"] = "nilai {$a->kode}";
        }

        $data = $request->validate($aturan, [], $label);
        unset($data['nilai']);

        return $data;
    }
}
