@extends('layouts.app')

@section('judul', 'Data Kriteria')
@section('subjudul', 'Kriteria penilaian, bobot (w), dan jenis atribut benefit / cost')
@section('aksi')
    <a href="{{ route('kriteria.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Kriteria</a>
@endsection

@section('konten')
    @include('partials.peringatan-bobot')

    <div class="kartu">
        <div class="kartu-header">
            <div>
                <h2>Daftar Kriteria</h2>
                <p class="ket">Benefit: makin besar makin baik. Cost: makin kecil makin baik.</p>
            </div>
            <span class="badge text-bg-light border fs-6 fw-semibold">Total bobot: {{ angka_ringkas($totalBobot * 100) }}%</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width:60px">No</th>
                    <th>Kode</th>
                    <th>Nama Kriteria</th>
                    <th>Satuan</th>
                    <th class="num">Persentase</th>
                    <th class="num">Bobot (w)</th>
                    <th>Jenis</th>
                    <th class="text-end no-print" style="width:110px">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($kriteria as $k)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="kode">{{ $k->kode }}</td>
                        <td>{{ $k->nama }}</td>
                        <td class="text-secondary">{{ $k->satuan ?? '-' }}</td>
                        <td class="num">{{ angka_ringkas($k->bobot * 100) }}%</td>
                        <td class="num">{{ angka_ringkas($k->bobot) }}</td>
                        <td><span class="badge-jenis badge-{{ $k->jenis }}">{{ ucfirst($k->jenis) }}</span></td>
                        <td class="text-end aksi no-print">
                            <a href="{{ route('kriteria.edit', $k) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="bi bi-pencil"></i></a>
                            @include('partials.tombol-hapus', [
                                'url' => route('kriteria.destroy', $k),
                                'pesan' => "Hapus kriteria {$k->kode}? Nilai penilaian pada kriteria ini juga akan terhapus.",
                            ])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-secondary py-4">Belum ada data kriteria.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
