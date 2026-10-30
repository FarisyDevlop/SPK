@extends('layouts.app')

@section('judul', 'Data Alternatif')
@section('subjudul', 'Vendor SIMRS yang dibandingkan')
@section('aksi')
    <a href="{{ route('alternatif.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Alternatif</a>
@endsection

@section('konten')
    @php $jumlahKriteria = \App\Models\Kriteria::count(); @endphp
    <div class="kartu">
        <div class="kartu-header">
            <div>
                <h2>Daftar Alternatif</h2>
                <p class="ket">Setelah menambah alternatif, isi nilainya di menu Data Penilaian</p>
            </div>
            <span class="badge text-bg-light border fs-6 fw-semibold">{{ $alternatif->count() }} alternatif</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width:60px">No</th>
                    <th>Kode</th>
                    <th>Nama Vendor</th>
                    <th>Keterangan</th>
                    <th>Status Penilaian</th>
                    <th class="text-end" style="width:150px">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($alternatif as $a)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="kode">{{ $a->kode }}</td>
                        <td class="fw-semibold">{{ $a->nama }}</td>
                        <td class="text-secondary">{{ $a->keterangan ?? '-' }}</td>
                        <td>
                            @if ($jumlahKriteria > 0 && $a->penilaian_count >= $jumlahKriteria)
                                <span class="text-success"><i class="bi bi-check-circle-fill"></i> Lengkap</span>
                            @else
                                <span class="text-warning-emphasis"><i class="bi bi-exclamation-circle-fill"></i> {{ $a->penilaian_count }}/{{ $jumlahKriteria }} terisi</span>
                            @endif
                        </td>
                        <td class="text-end aksi">
                            <a href="{{ route('penilaian.edit', $a) }}" class="btn btn-sm btn-outline-secondary" title="Isi nilai"><i class="bi bi-ui-checks-grid"></i></a>
                            <a href="{{ route('alternatif.edit', $a) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="bi bi-pencil"></i></a>
                            @include('partials.tombol-hapus', [
                                'url' => route('alternatif.destroy', $a),
                                'pesan' => "Hapus alternatif {$a->kode} - {$a->nama} beserta nilainya?",
                            ])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data alternatif.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
