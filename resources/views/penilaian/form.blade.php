@extends('layouts.app')

@section('judul', 'Input Nilai Alternatif')
@section('subjudul', $alternatif->kode . ' – ' . $alternatif->nama)

@section('konten')
    <div class="kartu" style="max-width: 820px">
        <div class="kartu-header">
            <div>
                <h2>{{ $alternatif->kode }} – {{ $alternatif->nama }}</h2>
                <p class="ket">Masukkan nilai asli (sebelum normalisasi) sesuai satuan masing-masing kriteria</p>
            </div>
        </div>
        @if ($kriteria->isEmpty())
            <div class="kartu-body text-secondary">Belum ada kriteria. Tambahkan dulu di menu <a href="{{ route('kriteria.index') }}">Data Kriteria</a>.</div>
        @else
        <form class="kartu-body" method="POST" action="{{ route('penilaian.update', $alternatif) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                @foreach ($kriteria as $k)
                    <div class="col-md-6">
                        <label class="form-label" for="nilai-{{ $k->id }}">
                            {{ $k->kode }} {{ $k->nama }}
                            <span class="badge-jenis badge-{{ $k->jenis }} ms-1" style="font-size:.66rem">{{ $k->jenis }}</span>
                        </label>
                        <div class="input-group has-validation">
                            <input type="number" step="any" min="0" id="nilai-{{ $k->id }}" name="nilai[{{ $k->id }}]"
                                   class="form-control @error('nilai.' . $k->id) is-invalid @enderror"
                                   value="{{ old('nilai.' . $k->id, isset($nilai[$k->id]) ? angka_ringkas_input($nilai[$k->id]) : '') }}" required>
                            @if ($k->satuan)<span class="input-group-text">{{ $k->satuan }}</span>@endif
                            @error('nilai.' . $k->id) <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan Nilai</button>
                <a href="{{ route('penilaian.index') }}" class="btn btn-light border">Batal</a>
            </div>
        </form>
        @endif
    </div>
@endsection
