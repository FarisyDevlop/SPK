@extends('layouts.app')

@section('judul', 'Hasil Akhir')
@section('subjudul', 'Perankingan vendor SIMRS berdasarkan nilai preferensi SAW')
@section('aksi')
    <button onclick="window.print()" class="btn btn-light border"><i class="bi bi-printer"></i> Cetak</button>
@endsection

@section('konten')
    @include('partials.tidak-lengkap')

    @php $terbaik = $hasil->first(); $maks = $terbaik['nilai'] ?? 1; @endphp

    @if ($terbaik)
        <div class="kartu mb-4" style="background: var(--scrub); color: #fff; border: 0">
            <div class="kartu-body d-flex flex-wrap align-items-center gap-4 p-4">
                <div class="flex-grow-1">
                    <div style="color:#9cc4bc; font-size:.86rem">Vendor yang direkomendasikan</div>
                    <div class="fw-bold mt-1" style="font-size:1.9rem; letter-spacing:-.02em">{{ $terbaik['alternatif']->nama }}</div>
                    <div style="color:#9cc4bc">{{ $terbaik['alternatif']->kode }}, nilai preferensi tertinggi dari {{ $hasil->count() }} vendor</div>
                </div>
                <div class="text-end">
                    <div style="color:#9cc4bc; font-size:.86rem">Nilai V</div>
                    <div class="fw-bold" style="font-size:2.8rem; line-height:1; font-variant-numeric: tabular-nums">{{ angka($terbaik['nilai']) }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="kartu">
        <div class="kartu-header"><h2>Tabel peringkat</h2></div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr><th style="width:90px">Peringkat</th><th>Kode</th><th>Nama Vendor</th><th style="width:38%">Perbandingan</th><th class="num">Nilai V</th></tr>
                </thead>
                <tbody>
                @forelse ($hasil as $h)
                    <tr class="{{ $h['peringkat'] === 1 ? 'baris-juara' : '' }}">
                        <td>
                            @if ($h['peringkat'] <= 3)
                                <span class="badge rounded-pill fs-6" style="{{ ['', 'background: var(--emas)', 'background: var(--hijau)', 'background: var(--mint); color: var(--scrub)'][$h['peringkat']] }}">#{{ $h['peringkat'] }}</span>
                            @else
                                <span class="ps-2 fw-semibold">#{{ $h['peringkat'] }}</span>
                            @endif
                        </td>
                        <td class="kode">{{ $h['alternatif']->kode }}</td>
                        <td class="fw-semibold">{{ $h['alternatif']->nama }}</td>
                        <td>
                            <div class="progress" style="height: 8px" role="progressbar" aria-label="Nilai V {{ $h['alternatif']->kode }}">
                                <div class="progress-bar" style="width: {{ round($h['nilai'] / $maks * 100, 1) }}%; background: {{ $h['peringkat'] === 1 ? 'var(--hijau)' : 'var(--mint-batang)' }}"></div>
                            </div>
                        </td>
                        <td class="num fw-bold">{{ angka($h['nilai']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada hasil. Lengkapi data penilaian terlebih dahulu.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="kartu-body border-top small text-secondary">
            Nilai V dihitung dengan rumus V<sub>i</sub> = Σ (w<sub>j</sub> × r<sub>ij</sub>). Lihat detail tiap langkah di menu
            <a href="{{ route('perhitungan') }}">Perhitungan SAW</a>.
        </div>
    </div>
@endsection
