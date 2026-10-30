@extends('layouts.app')

@section('judul', 'Dashboard')
@section('subjudul', 'Pemilihan vendor SIMRS untuk RSUD Harapan Sehat dengan metode Simple Additive Weighting')
@section('aksi')
    <a href="{{ route('perhitungan') }}" class="btn btn-light"><i class="bi bi-calculator"></i> Lihat perhitungan</a>
    <a href="{{ route('hasil') }}" class="btn btn-primary"><i class="bi bi-trophy"></i> Hasil akhir</a>
@endsection

@push('style')
<style>
    /* Strip KPI: satu kartu dibagi empat */
    .strip { display: grid; grid-template-columns: repeat(4, 1fr); }
    .strip a { padding: 1.15rem 1.35rem; text-decoration: none; color: inherit; border-left: 1px solid var(--garis); }
    .strip a:first-child { border-left: 0; }
    .strip a:hover { background: #f7fbfa; }
    .strip .angka { font-size: 1.85rem; font-weight: 800; letter-spacing: -.02em; line-height: 1.1; font-variant-numeric: tabular-nums; }
    .strip .label { font-weight: 600; font-size: .86rem; margin-top: .2rem; }
    .strip .detail { color: var(--redup); font-size: .78rem; }
    .strip .detail i { font-size: .8rem; }
    .strip .ok { color: #17694a; }
    .strip .awas { color: #a23a26; }

    /* Panel juara */
    .juara { background: var(--scrub); color: #fff; border: 0; }
    .juara .kecil { color: #9cc4bc; font-size: .84rem; font-weight: 500; }
    .juara .nama-juara { font-size: 1.9rem; font-weight: 800; letter-spacing: -.02em; line-height: 1.15; }
    .juara .nilai { font-size: 3.4rem; font-weight: 800; letter-spacing: -.03em; font-variant-numeric: tabular-nums; line-height: 1; }
    .juara .medali { display: inline-grid; place-items: center; width: 26px; height: 26px; border-radius: 50%; background: var(--emas); color: #fff; font-size: .8rem; font-weight: 800; }
    .finish { background: rgba(255, 255, 255, .06); border-radius: 12px; padding: .9rem 1rem .7rem; }
    .finish .jdl { font-size: .84rem; font-weight: 700; }
    .finish .ket { font-size: .76rem; color: #9cc4bc; }
    .finish .brs { display: grid; grid-template-columns: 112px 1fr 52px; gap: .6rem; align-items: center; height: 26px; font-size: .8rem; }
    .finish .brs .nm { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #d3e6e2; }
    .finish .lintasan { position: relative; height: 2px; background: rgba(255, 255, 255, .18); }
    .finish .titik { position: absolute; top: 50%; width: 12px; height: 12px; margin: -6px 0 0 -6px; border-radius: 50%; background: var(--mint-batang); box-shadow: 0 0 0 2px var(--scrub); }
    .finish .titik.satu { background: var(--emas); width: 14px; height: 14px; margin: -7px 0 0 -7px; }
    .finish .v { text-align: right; font-weight: 700; font-variant-numeric: tabular-nums; }
    .finish .skala { display: grid; grid-template-columns: 112px 1fr 52px; gap: .6rem; font-size: .7rem; color: #7fa9a1; }
    .finish .skala div:nth-child(2) { display: flex; justify-content: space-between; }

    /* Grafik batang HTML */
    .grafik { position: relative; }
    .grafik .baris { display: grid; grid-template-columns: var(--lebar-label, 190px) 1fr 64px; align-items: center; gap: .75rem; padding: 5px 0; }
    .grafik .nama { font-size: .84rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .grafik .nama b { color: var(--hijau); font-weight: 700; }
    .grafik .jalur { position: relative; height: 24px; }
    .grafik .batang { position: absolute; left: 0; top: 4px; bottom: 4px; border-radius: 0 4px 4px 0; background: var(--mint-batang); transition: opacity .15s; }
    .grafik .batang.utama { background: var(--hijau); }
    .grafik .nilai { font-size: .82rem; font-weight: 700; text-align: right; font-variant-numeric: tabular-nums; }
    .grafik .grid { position: absolute; top: 0; bottom: 0; border-left: 1px solid #e8efed; }
    .grafik .grid.nol { border-left-color: #b7c7c3; }
    .sumbu { display: grid; grid-template-columns: var(--lebar-label, 190px) 1fr 64px; gap: .75rem; font-size: .72rem; color: var(--redup); }
    .sumbu .skala { position: relative; height: 16px; }
    .sumbu .skala span { position: absolute; transform: translateX(-50%); }
    .sumbu .skala span:first-child { transform: none; }
    .sumbu .skala span:last-child { transform: translateX(-100%); }

    .tumpuk { position: absolute; left: 0; top: 4px; bottom: 4px; display: flex; }
    .tumpuk .seg { height: 100%; border-right: 2px solid #fff; cursor: default; transition: opacity .15s; }
    .tumpuk .seg:last-child { border-radius: 0 4px 4px 0; border-right: 0; }
    .grafik:hover .seg, .grafik:hover .batang { opacity: .5; }
    .grafik .seg:hover, .grafik .batang:hover { opacity: 1; }
    .legenda { display: flex; flex-wrap: wrap; gap: .4rem 1.1rem; font-size: .8rem; color: var(--tinta-2); }
    .legenda i { display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: .4rem; vertical-align: -1px; }

    #tooltip { position: fixed; pointer-events: none; z-index: 2000; background: var(--tinta); color: #fff; font-size: .8rem; padding: .5rem .7rem; border-radius: 9px; max-width: 280px; opacity: 0; transition: opacity .1s; line-height: 1.45; }
    #tooltip.tampil { opacity: 1; }

    .langkah { counter-reset: lk; }
    .langkah a { display: flex; gap: .85rem; padding: .75rem 0; text-decoration: none; color: inherit; border-bottom: 1px solid #e8efed; }
    .langkah a:last-child { border-bottom: 0; }
    .langkah a::before { counter-increment: lk; content: counter(lk); flex: none; width: 28px; height: 28px; border-radius: 50%; background: var(--mint); color: var(--scrub); font-weight: 800; display: grid; place-items: center; font-size: .8rem; }
    .langkah a:hover .jdl { color: var(--hijau); }
    .langkah .jdl { font-weight: 700; font-size: .88rem; }
    .langkah .ket { color: var(--tinta-2); font-size: .8rem; }

    @media (max-width: 991.98px) {
        .strip { grid-template-columns: repeat(2, 1fr); }
        .strip a:nth-child(3) { border-left: 0; }
        .strip a:nth-child(n+3) { border-top: 1px solid var(--garis); }
    }
    @media (max-width: 575.98px) {
        .grafik .baris, .sumbu { --lebar-label: 92px; grid-template-columns: var(--lebar-label) 1fr 52px; gap: .5rem; }
        .juara .nilai { font-size: 2.6rem; }
        .finish .brs, .finish .skala { grid-template-columns: 84px 1fr 48px; }
    }
</style>
@endpush

@section('konten')
    @include('partials.peringatan-bobot')
    @include('partials.tidak-lengkap')

    {{-- Ringkasan data --}}
    @php
        $benefit = $kriteria->where('jenis', 'benefit')->count();
        $cost = $kriteria->where('jenis', 'cost')->count();
        $bobotPas = abs($totalBobot - 1) <= 0.0005;
        $nilaiLengkap = $nilaiDibutuhkan > 0 && $nilaiTerisi >= $nilaiDibutuhkan;
    @endphp
    <div class="kartu strip mb-4">
        <a href="{{ route('kriteria.index') }}">
            <div class="angka">{{ $kriteria->count() }}</div>
            <div class="label">Kriteria</div>
            <div class="detail">{{ $benefit }} benefit, {{ $cost }} cost</div>
        </a>
        <a href="{{ route('alternatif.index') }}">
            <div class="angka">{{ $jumlahAlternatif }}</div>
            <div class="label">Vendor dibandingkan</div>
            <div class="detail">{{ $alternatif->count() }} ikut perhitungan</div>
        </a>
        <a href="{{ route('kriteria.index') }}">
            <div class="angka">{{ angka_ringkas($totalBobot * 100) }}%</div>
            <div class="label">Total bobot</div>
            <div class="detail {{ $bobotPas ? 'ok' : 'awas' }}"><i class="bi {{ $bobotPas ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }}"></i> {{ $bobotPas ? 'Sudah 100%' : 'Disesuaikan otomatis' }}</div>
        </a>
        <a href="{{ route('penilaian.index') }}">
            <div class="angka">{{ $nilaiTerisi }}<span class="text-secondary fw-semibold fs-5">/{{ $nilaiDibutuhkan }}</span></div>
            <div class="label">Nilai terisi</div>
            <div class="detail {{ $nilaiLengkap ? 'ok' : 'awas' }}"><i class="bi {{ $nilaiLengkap ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }}"></i> {{ $nilaiLengkap ? 'Matriks lengkap' : 'Ada nilai kosong' }}</div>
        </a>
    </div>

    @if ($terbaik)
    <div class="row g-4 mb-4">
        {{-- Rekomendasi --}}
        @php
            $tiga = $hasil->take(3);
            $lo = floor(($tiga->min('nilai') - 0.001) * 1000) / 1000;
            $hi = ceil(($tiga->max('nilai') + 0.001) * 1000) / 1000;
            $posisi = fn ($v) => round(($v - $lo) / max($hi - $lo, 0.0001) * 100, 2);
        @endphp
        <div class="col-lg-5">
            <div class="kartu juara h-100">
                <div class="kartu-body d-flex flex-column h-100 p-4">
                    <div class="kecil">Vendor yang direkomendasikan</div>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <span class="medali" title="Peringkat 1">1</span>
                        <span class="nama-juara">{{ $terbaik['alternatif']->nama }}</span>
                    </div>
                    <div class="kecil mt-1">{{ $terbaik['alternatif']->kode }}, peringkat pertama dari {{ $hasil->count() }} vendor</div>
                    <div class="d-flex align-items-baseline gap-2 my-4">
                        <div class="nilai">{{ angka($terbaik['nilai']) }}</div>
                        <div class="kecil">nilai preferensi V</div>
                    </div>

                    @if ($kedua)
                    <div class="finish mt-auto">
                        <div class="jdl">Selisih tiga teratas</div>
                        <div class="ket mb-2">
                            Unggul {{ angka($selisih) }} dari {{ $kedua['alternatif']->nama }}.
                            @if ($selisih < 0.01) Selisihnya sangat tipis, jadi peringkat 2 juga layak dipertimbangkan. @endif
                        </div>
                        @foreach ($tiga as $h)
                            <div class="brs">
                                <div class="nm">{{ $h['alternatif']->kode }} {{ $h['alternatif']->nama }}</div>
                                <div class="lintasan">
                                    <span class="titik {{ $h['peringkat'] === 1 ? 'satu' : '' }}" style="left: {{ $posisi($h['nilai']) }}%"
                                          data-tip="<b>#{{ $h['peringkat'] }} {{ e($h['alternatif']->nama) }}</b><br>V = {{ angka($h['nilai']) }}"></span>
                                </div>
                                <div class="v">{{ angka($h['nilai']) }}</div>
                            </div>
                        @endforeach
                        <div class="skala mt-1"><div></div><div><span>{{ angka($lo, 3) }}</span><span>{{ angka($hi, 3) }}</span></div><div></div></div>
                        <div class="ket mt-1">Skala diperbesar supaya selisih kecil terlihat.</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Grafik ranking --}}
        <div class="col-lg-7">
            <div class="kartu h-100">
                <div class="kartu-header">
                    <div>
                        <h2>Peringkat semua vendor</h2>
                        <p class="ket">Skala 0 sampai 1. Semakin panjang batangnya, semakin baik vendornya.</p>
                    </div>
                    <a href="{{ route('hasil') }}" class="small fw-semibold">Lihat tabel peringkat</a>
                </div>
                <div class="kartu-body">
                    <div class="grafik">
                        @foreach ($hasil as $h)
                            <div class="baris">
                                <div class="nama" title="{{ $h['alternatif']->nama }}"><b>{{ $h['alternatif']->kode }}</b> {{ $h['alternatif']->nama }}</div>
                                <div class="jalur">
                                    @foreach ([0, 25, 50, 75, 100] as $g)<div class="grid {{ $g === 0 ? 'nol' : '' }}" style="left: {{ $g }}%"></div>@endforeach
                                    <div class="batang {{ $h['peringkat'] === 1 ? 'utama' : '' }}" style="width: {{ round($h['nilai'] * 100, 2) }}%"
                                         data-tip="<b>#{{ $h['peringkat'] }} {{ $h['alternatif']->kode }} – {{ e($h['alternatif']->nama) }}</b><br>V = {{ angka($h['nilai']) }}"></div>
                                </div>
                                <div class="nilai">{{ angka($h['nilai']) }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="sumbu mt-1"><div></div><div class="skala">@foreach ([0, .25, .5, .75, 1] as $s)<span style="left: {{ $s * 100 }}%">{{ angka_ringkas($s) }}</span>@endforeach</div><div></div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kontribusi kriteria --}}
    @php $warna = ['#1d6fa3', '#e0743c', '#16a085', '#c99300', '#c2588c', '#4f8a2b', '#6250d6', '#e34948']; @endphp
    <div class="kartu mb-4">
        <div class="kartu-header">
            <div>
                <h2>Dari mana nilai V berasal</h2>
                <p class="ket">Tiap warna adalah sumbangan satu kriteria (bobot × nilai normalisasi). Panjang total batang sama dengan nilai V.</p>
            </div>
        </div>
        <div class="kartu-body">
            <div class="legenda mb-3">
                @foreach ($kriteria as $i => $k)
                    <span><i style="background: {{ $warna[$i % count($warna)] }}"></i>{{ $k->kode }} {{ $k->nama }}</span>
                @endforeach
            </div>
            <div class="grafik" style="--lebar-label: 190px">
                @foreach ($hasil as $h)
                    @php $a = $h['alternatif']; @endphp
                    <div class="baris">
                        <div class="nama"><b>{{ $a->kode }}</b> {{ $a->nama }}</div>
                        <div class="jalur">
                            @foreach ([0, 25, 50, 75, 100] as $g)<div class="grid {{ $g === 0 ? 'nol' : '' }}" style="left: {{ $g }}%"></div>@endforeach
                            <div class="tumpuk" style="width: {{ round($h['nilai'] * 100, 2) }}%">
                                @foreach ($kriteria as $i => $k)
                                    @php $kontribusi = $terbobot[$a->id][$k->id]; @endphp
                                    <div class="seg" style="flex: {{ $kontribusi }} 0 0; background: {{ $warna[$i % count($warna)] }}"
                                         data-tip="<b>{{ $a->kode }}, {{ $k->kode }} {{ e($k->nama) }}</b><br>{{ angka($bobot[$k->id], 4) }} × {{ angka($normalisasi[$a->id][$k->id]) }} = {{ angka($kontribusi) }}"></div>
                                @endforeach
                            </div>
                        </div>
                        <div class="nilai">{{ angka($h['nilai']) }}</div>
                    </div>
                @endforeach
            </div>
            <div class="sumbu mt-1"><div></div><div class="skala">@foreach ([0, .25, .5, .75, 1] as $s)<span style="left: {{ $s * 100 }}%">{{ angka_ringkas($s) }}</span>@endforeach</div><div></div></div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        {{-- Bobot kriteria --}}
        <div class="col-lg-7">
            <div class="kartu h-100">
                <div class="kartu-header">
                    <div>
                        <h2>Bobot kriteria</h2>
                        <p class="ket">Seberapa besar pengaruh tiap kriteria pada nilai akhir</p>
                    </div>
                    <a href="{{ route('kriteria.index') }}" class="small fw-semibold">Ubah bobot</a>
                </div>
                <div class="kartu-body">
                    @php $bobotMaks = max($bobot ? max($bobot) : 0, 0.0001); @endphp
                    <div class="grafik" style="--lebar-label: 275px">
                        @foreach ($kriteria as $k)
                            <div class="baris">
                                <div class="nama" title="{{ $k->label }}"><b>{{ $k->kode }}</b> {{ $k->nama }}
                                    <span class="badge-jenis badge-{{ $k->jenis }} ms-1" style="font-size:.62rem">{{ $k->jenis }}</span></div>
                                <div class="jalur">
                                    <div class="grid nol" style="left:0"></div>
                                    <div class="batang" style="width: {{ round($bobot[$k->id] / $bobotMaks * 100, 2) }}%; background: var(--hijau)"
                                         data-tip="<b>{{ $k->kode }} {{ e($k->nama) }}</b><br>Bobot dipakai {{ angka($bobot[$k->id] * 100, 2) }}%@if ($bobotDisesuaikan) (diisi {{ angka_ringkas($k->bobot * 100) }}%)@endif, {{ $k->jenis }}"></div>
                                </div>
                                <div class="nilai">{{ angka_ringkas(round($bobot[$k->id] * 100, 2)) }}%</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Alur metode --}}
        <div class="col-lg-5">
            <div class="kartu h-100">
                <div class="kartu-header"><h2>Alur metode SAW</h2></div>
                <div class="kartu-body py-2 langkah">
                    <a href="{{ route('kriteria.index') }}"><div><div class="jdl">Tentukan kriteria & bobot</div><div class="ket">Bobot (w) dengan Σw = 1, jenis benefit atau cost</div></div></a>
                    <a href="{{ route('penilaian.index') }}"><div><div class="jdl">Isi matriks keputusan (X)</div><div class="ket">Nilai setiap alternatif pada setiap kriteria</div></div></a>
                    <a href="{{ route('perhitungan') }}"><div><div class="jdl">Normalisasi (R)</div><div class="ket">Benefit: x / max, cost: min / x</div></div></a>
                    <a href="{{ route('perhitungan') }}"><div><div class="jdl">Hitung nilai preferensi (V)</div><div class="ket">V = Σ (w × r)</div></div></a>
                    <a href="{{ route('hasil') }}"><div><div class="jdl">Perankingan</div><div class="ket">Nilai V terbesar adalah vendor terbaik</div></div></a>
                </div>
            </div>
        </div>
    </div>

    <div id="tooltip" role="tooltip"></div>
@endsection

@push('script')
<script>
    // Tooltip sederhana untuk semua elemen yang punya atribut data-tip
    const tip = document.getElementById('tooltip');
    document.querySelectorAll('[data-tip]').forEach(el => {
        el.addEventListener('mouseenter', () => { tip.innerHTML = el.dataset.tip; tip.classList.add('tampil'); });
        el.addEventListener('mousemove', e => {
            const x = Math.min(e.clientX + 14, window.innerWidth - tip.offsetWidth - 8);
            tip.style.left = x + 'px';
            tip.style.top = (e.clientY - tip.offsetHeight - 12) + 'px';
        });
        el.addEventListener('mouseleave', () => tip.classList.remove('tampil'));
    });
</script>
@endpush
