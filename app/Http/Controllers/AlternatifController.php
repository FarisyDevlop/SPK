<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlternatifController extends Controller
{
    public function index()
    {
        $alternatif = Alternatif::withCount('penilaian')->orderBy('kode')->get();

        return view('alternatif.index', compact('alternatif'));
    }

    public function create()
    {
        return view('alternatif.form', ['alternatif' => new Alternatif()]);
    }

    public function store(Request $request)
    {
        $alternatif = Alternatif::create($this->validasi($request));

        return redirect()->route('penilaian.edit', $alternatif)
            ->with('sukses', 'Alternatif ditambahkan. Silakan isi nilainya pada setiap kriteria.');
    }

    public function edit(Alternatif $alternatif)
    {
        return view('alternatif.form', compact('alternatif'));
    }

    public function update(Request $request, Alternatif $alternatif)
    {
        $alternatif->update($this->validasi($request, $alternatif));

        return redirect()->route('alternatif.index')->with('sukses', 'Alternatif berhasil diperbarui.');
    }

    public function destroy(Alternatif $alternatif)
    {
        $alternatif->delete();

        return redirect()->route('alternatif.index')->with('sukses', 'Alternatif berhasil dihapus.');
    }

    private function validasi(Request $request, ?Alternatif $alternatif = null): array
    {
        return $request->validate([
            'kode' => ['required', 'string', 'max:10', Rule::unique('alternatif', 'kode')->ignore($alternatif?->id)],
            'nama' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [], [
            'kode' => 'kode alternatif',
            'nama' => 'nama alternatif',
        ]);
    }
}
