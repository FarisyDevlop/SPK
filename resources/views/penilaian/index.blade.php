@extends('layouts.app')

@section('judul', 'Data Penilaian')
@section('subjudul', 'Matriks keputusan (X): nilai setiap alternatif pada setiap kriteria')

@section('konten')
    <div class="kartu">
        <div class="kartu-header">
            <div>
                <h2>Matriks Keputusan</h2>
                <p class="ket">Klik <i class="bi bi-pencil"></i> untuk mengisi atau mengubah nilai satu alternatif</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Kode</th>
                    <th>Vendor SIMRS</th>
                    @foreach ($kriteria as $k)
                        <th class="num" title="{{ $k->label }}">
                            {{ $k->kode }}<br>
                            <span class="badge-jenis badge-{{ $k->jenis }}" style="font-size:.64rem">{{ $k->jenis }}</span>
                        </th>
                    @endforeach
                    <th class="text-end" style="width:110px">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($alternatif as $a)
                    @php $nilai = $a->penilaian->pluck('nilai', 'kriteria_id'); @endphp
                    <tr>
                        <td class="kode">{{ $a->kode }}</td>
                        <td class="fw-semibold">{{ $a->nama }}</td>
                        @foreach ($kriteria as $k)
                            <td class="num">
                                @if ($nilai->has($k->id))
                                    {{ angka_ringkas($nilai[$k->id]) }}
                                @else
                                    <span class="text-danger" title="Belum diisi">—</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="text-end aksi">
                            <a href="{{ route('penilaian.edit', $a) }}" class="btn btn-sm btn-outline-primary" title="Isi / ubah nilai"><i class="bi bi-pencil"></i></a>
                            @if ($nilai->isNotEmpty())
                                @include('partials.tombol-hapus', [
                                    'url' => route('penilaian.destroy', $a),
                                    'pesan' => "Kosongkan semua nilai {$a->kode}?",
                                    'judul' => 'Kosongkan nilai',
                                    'ikon' => 'bi-eraser',
                                ])
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ $kriteria->count() + 3 }}" class="text-center text-secondary py-4">Belum ada alternatif. Tambahkan dulu di menu Data Alternatif.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="kartu-body border-top small text-secondary">
            @foreach ($kriteria as $k)
                <span class="me-3"><strong>{{ $k->kode }}</strong> {{ $k->label }}</span>
            @endforeach
        </div>
    </div>
@endsection
