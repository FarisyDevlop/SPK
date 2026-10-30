@extends('layouts.app')

@php $ubah = $alternatif->exists; @endphp
@section('judul', $ubah ? 'Ubah Alternatif' : 'Tambah Alternatif')
@section('subjudul', 'Data vendor yang akan dinilai')

@section('konten')
    <div class="kartu" style="max-width: 720px">
        <div class="kartu-header"><h2>{{ $ubah ? "Ubah {$alternatif->kode}" : 'Alternatif Baru' }}</h2></div>
        <form class="kartu-body" method="POST" action="{{ $ubah ? route('alternatif.update', $alternatif) : route('alternatif.store') }}">
            @csrf
            @if ($ubah) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-sm-3">
                    <label class="form-label" for="kode">Kode</label>
                    <input type="text" id="kode" name="kode" class="form-control @error('kode') is-invalid @enderror"
                           value="{{ old('kode', $alternatif->kode) }}" placeholder="V9" required>
                    @error('kode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-sm-9">
                    <label class="form-label" for="nama">Nama Vendor</label>
                    <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama', $alternatif->nama) }}" placeholder="Contoh: Medika Soft" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="keterangan">Keterangan <span class="text-secondary fw-normal">(opsional)</span></label>
                    <input type="text" id="keterangan" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                           value="{{ old('keterangan', $alternatif->keterangan) }}">
                    @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> {{ $ubah ? 'Simpan' : 'Simpan & Isi Nilai' }}</button>
                <a href="{{ route('alternatif.index') }}" class="btn btn-light border">Batal</a>
            </div>
        </form>
    </div>
@endsection
