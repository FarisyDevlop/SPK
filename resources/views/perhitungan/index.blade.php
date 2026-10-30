@extends('layouts.app')

@section('judul', 'Perhitungan SAW')
@section('subjudul', 'Empat langkah metode Simple Additive Weighting, dari nilai asli sampai peringkat')
@section('aksi')
    <button onclick="window.print()" class="btn btn-light border"><i class="bi bi-printer"></i> Cetak</button>
@endsection

@section('konten')
    @include('partials.peringatan-bobot')
    @include('partials.tidak-lengkap')

    @if ($alternatif->isEmpty() || $kriteria->isEmpty())
        <div class="kartu kartu-body text-secondary">Data belum cukup untuk dihitung. Lengkapi data kriteria, alternatif, dan penilaian.</div>
    @else
    {{-- Langkah 1 --}}
    <div class="kartu mb-4">
        <div class="kartu-header">
            <div>
                <h2><span class="no-langkah">1</span>Matriks keputusan (X)</h2>
                <p class="ket">Nilai asli setiap alternatif pada setiap kriteria</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Alternatif</th>
                    @foreach ($kriteria as $k)<th class="num">{{ $k->kode }}</th>@endforeach
                </tr>
                </thead>
                <tbody>
                @foreach ($alternatif as $a)
                    <tr>
                        <td><span class="kode">{{ $a->kode }}</span> {{ $a->nama }}</td>
                        @foreach ($kriteria as $k)<td class="num">{{ angka_ringkas($matriks[$a->id][$k->id]) }}</td>@endforeach
                    </tr>
                @endforeach
                </tbody>
                <tfoot class="table-group-divider">
                <tr>
                    <th class="text-secondary fw-semibold">Jenis</th>
                    @foreach ($kriteria as $k)<td class="num"><span class="badge-jenis badge-{{ $k->jenis }}">{{ $k->jenis }}</span></td>@endforeach
                </tr>
                <tr>
                    <th class="text-secondary fw-semibold">Max</th>
                    @foreach ($kriteria as $k)<td class="num {{ $k->jenis === 'benefit' ? 'fw-bold' : 'text-secondary' }}">{{ angka_ringkas($pembagi[$k->id]['max']) }}</td>@endforeach
                </tr>
                <tr>
                    <th class="text-secondary fw-semibold">Min</th>
                    @foreach ($kriteria as $k)<td class="num {{ $k->jenis === 'cost' ? 'fw-bold' : 'text-secondary' }}">{{ angka_ringkas($pembagi[$k->id]['min']) }}</td>@endforeach
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Langkah 2 --}}
    <div class="kartu mb-4">
        <div class="kartu-header">
            <div>
                <h2><span class="no-langkah">2</span>Normalisasi matriks (R)</h2>
                <p class="ket">Nilai diubah ke skala 0–1 agar setiap kriteria bisa dibandingkan</p>
            </div>
        </div>
        <div class="kartu-body pb-0">
            <div class="rumus row g-2 mx-0">
                <div class="col-md-6"><span class="badge-jenis badge-benefit">Benefit</span> &nbsp;<span class="f">r<sub>ij</sub> = x<sub>ij</sub> / max(x<sub>j</sub>)</span></div>
                <div class="col-md-6"><span class="badge-jenis badge-cost">Cost</span> &nbsp;<span class="f">r<sub>ij</sub> = min(x<sub>j</sub>) / x<sub>ij</sub></span></div>
            </div>
        </div>
        <div class="table-responsive mt-3">
            <table class="table">
                <thead>
                <tr>
                    <th>Alternatif</th>
                    @foreach ($kriteria as $k)<th class="num">{{ $k->kode }}</th>@endforeach
                </tr>
                </thead>
                <tbody>
                @foreach ($alternatif as $a)
                    <tr>
                        <td><span class="kode">{{ $a->kode }}</span> {{ $a->nama }}</td>
                        @foreach ($kriteria as $k)
                            @php
                                $x = angka_ringkas($matriks[$a->id][$k->id]);
                                $p = $k->jenis === 'cost' ? angka_ringkas($pembagi[$k->id]['min']) . ' / ' . $x : $x . ' / ' . angka_ringkas($pembagi[$k->id]['max']);
                            @endphp
                            <td class="num" title="{{ $p }}">{{ angka($normalisasi[$a->id][$k->id]) }}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @php $contoh = $alternatif->first(); $kc = $kriteria->first(); @endphp
        <div class="kartu-body border-top small text-secondary">
            Contoh: r<sub>{{ $contoh->kode }},{{ $kc->kode }}</sub> ({{ $kc->jenis }}) =
            @if ($kc->jenis === 'cost')
                {{ angka_ringkas($pembagi[$kc->id]['min']) }} / {{ angka_ringkas($matriks[$contoh->id][$kc->id]) }}
            @else
                {{ angka_ringkas($matriks[$contoh->id][$kc->id]) }} / {{ angka_ringkas($pembagi[$kc->id]['max']) }}
            @endif
            = <strong>{{ angka($normalisasi[$contoh->id][$kc->id]) }}</strong>. Arahkan kursor ke sel untuk melihat pembagiannya.
        </div>
    </div>

    {{-- Langkah 3 --}}
    <div class="kartu mb-4">
        <div class="kartu-header">
            <div>
                <h2><span class="no-langkah">3</span>Perkalian dengan bobot (w × r)</h2>
                <p class="ket">Setiap nilai normalisasi dikalikan bobot kriterianya</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Alternatif</th>
                    @foreach ($kriteria as $k)<th class="num">{{ $k->kode }}<br><span class="fw-normal text-lowercase">w = {{ angka_ringkas(round($bobot[$k->id], 4)) }}</span></th>@endforeach
                    <th class="num">V<sub>i</sub> (Σ)</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($alternatif as $a)
                    <tr>
                        <td><span class="kode">{{ $a->kode }}</span> {{ $a->nama }}</td>
                        @foreach ($kriteria as $k)<td class="num">{{ angka($terbobot[$a->id][$k->id]) }}</td>@endforeach
                        <td class="num fw-bold">{{ angka(array_sum($terbobot[$a->id])) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Langkah 4 --}}
    <div class="kartu">
        <div class="kartu-header">
            <div>
                <h2><span class="no-langkah">4</span>Nilai preferensi (V) dan peringkat</h2>
                <p class="ket">V<sub>i</sub> = Σ (w<sub>j</sub> × r<sub>ij</sub>) — nilai terbesar adalah alternatif terbaik</p>
            </div>
            <a href="{{ route('hasil') }}" class="btn btn-primary btn-sm no-print"><i class="bi bi-trophy"></i> Lihat hasil akhir</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr><th style="width:90px">Peringkat</th><th>Alternatif</th><th>Rincian</th><th class="num">Nilai V</th></tr>
                </thead>
                <tbody>
                @foreach ($hasil as $h)
                    @php $a = $h['alternatif']; @endphp
                    <tr class="{{ $h['peringkat'] === 1 ? 'baris-juara' : '' }}">
                        <td class="fw-bold">{{ $h['peringkat'] }}</td>
                        <td><span class="kode">{{ $a->kode }}</span> {{ $a->nama }}</td>
                        <td class="small text-secondary">
                            @foreach ($kriteria as $k)({{ angka_ringkas(round($bobot[$k->id], 4)) }} × {{ angka($normalisasi[$a->id][$k->id], 2) }})@if (! $loop->last) + @endif @endforeach
                        </td>
                        <td class="num fw-bold">{{ angka($h['nilai']) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection
