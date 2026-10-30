@extends('layouts.app')

@php $ubah = $kriteria->exists; @endphp
@section('judul', $ubah ? 'Ubah Kriteria' : 'Tambah Kriteria')
@section('subjudul', 'Isi data kriteria, lalu nilai setiap vendor pada kriteria ini supaya langsung ikut dihitung')

@section('konten')
    <div class="kartu" style="max-width: 860px">
        <div class="kartu-header"><h2>{{ $ubah ? "Ubah {$kriteria->kode}" : 'Kriteria Baru' }}</h2></div>
        <form class="kartu-body" method="POST" action="{{ $ubah ? route('kriteria.update', $kriteria) : route('kriteria.store') }}">
            @csrf
            @if ($ubah) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-sm-3">
                    <label class="form-label" for="kode">Kode</label>
                    <input type="text" id="kode" name="kode" class="form-control @error('kode') is-invalid @enderror"
                           value="{{ old('kode', $kriteria->kode) }}" placeholder="K7" required>
                    @error('kode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-sm-9">
                    <label class="form-label" for="nama">Nama Kriteria</label>
                    <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama', $kriteria->nama) }}" placeholder="Contoh: Biaya Pemeliharaan" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="satuan">Satuan <span class="text-secondary fw-normal">(opsional)</span></label>
                    <input type="text" id="satuan" name="satuan" class="form-control @error('satuan') is-invalid @enderror"
                           value="{{ old('satuan', $kriteria->satuan) }}" placeholder="Rp Juta, Skor 0-100, Bulan, ...">
                    @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="bobot">Bobot (w)</label>
                    <div class="input-group has-validation">
                        <input type="number" step="0.0001" min="0" max="1" id="bobot" name="bobot"
                               class="form-control @error('bobot') is-invalid @enderror"
                               value="{{ old('bobot', $kriteria->bobot) }}" placeholder="0.15" required>
                        <span class="input-group-text" id="persen">= 0%</span>
                        @error('bobot') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-text" id="info-total">
                        Bobot kriteria lain: {{ angka_ringkas($totalBobotLain * 100) }}%.
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label d-block">Jenis Kriteria</label>
                    @foreach (['benefit' => 'Benefit — semakin besar nilainya semakin baik', 'cost' => 'Cost — semakin kecil nilainya semakin baik'] as $opsi => $teks)
                        <div class="form-check">
                            <input class="form-check-input @error('jenis') is-invalid @enderror" type="radio" name="jenis"
                                   id="jenis-{{ $opsi }}" value="{{ $opsi }}" @checked(old('jenis', $kriteria->jenis ?? 'benefit') === $opsi)>
                            <label class="form-check-label" for="jenis-{{ $opsi }}">{{ $teks }}</label>
                        </div>
                    @endforeach
                    @error('jenis') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
            </div>

            @if ($alternatif->isNotEmpty())
                <hr class="my-4">
                <h3 class="fs-6 fw-bold mb-1">Nilai setiap vendor pada kriteria ini</h3>
                <p class="text-secondary small mb-3">Isi nilai asli sesuai satuan, misalnya biaya dalam Rp Juta atau skor 0–100.</p>
                <div class="row g-3">
                    @foreach ($alternatif as $a)
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label" for="nilai-{{ $a->id }}"><span class="kode">{{ $a->kode }}</span> {{ $a->nama }}</label>
                            <input type="number" step="any" min="0" id="nilai-{{ $a->id }}" name="nilai[{{ $a->id }}]"
                                   class="form-control @error('nilai.' . $a->id) is-invalid @enderror"
                                   value="{{ old('nilai.' . $a->id, isset($nilai[$a->id]) ? angka_ringkas_input($nilai[$a->id]) : '') }}" required>
                            @error('nilai.' . $a->id) <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                <a href="{{ route('kriteria.index') }}" class="btn btn-light border">Batal</a>
            </div>
        </form>
    </div>
@endsection

@push('script')
<script>
    const bobot = document.getElementById('bobot'), persen = document.getElementById('persen');
    const info = document.getElementById('info-total');
    const lain = {{ $totalBobotLain }};
    const fmt = v => (v * 100).toLocaleString('id-ID', {maximumFractionDigits: 2}) + '%';
    const tampil = () => {
        const w = parseFloat(bobot.value) || 0, total = lain + w;
        persen.textContent = '= ' + fmt(w);
        let teks = `Bobot kriteria lain ${fmt(lain)}, total setelah disimpan ${fmt(total)}.`;
        if (Math.abs(total - 1) > 0.0005) {
            teks += ' Tidak masalah, saat perhitungan bobot otomatis disesuaikan supaya totalnya 100%.';
        }
        info.textContent = teks;
    };
    bobot.addEventListener('input', tampil); tampil();
</script>
@endpush
