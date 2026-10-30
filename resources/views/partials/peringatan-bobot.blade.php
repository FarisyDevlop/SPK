@if (abs($totalBobot - 1) > 0.0005)
    <div class="alert alert-warning d-flex gap-3 align-items-center no-print">
        <i class="bi bi-sliders fs-5"></i>
        <div class="flex-grow-1">
            Total bobot yang diisi <strong>{{ angka_ringkas($totalBobot * 100) }}%</strong>, bukan 100%.
            Perhitungan tetap jalan karena setiap bobot otomatis disesuaikan dengan rumus
            <strong>w ÷ Σw</strong>, jadi perbandingan antar kriteria tetap sama.
            Kalau mau, simpan bobot hasil penyesuaian itu supaya totalnya pas 100%.
        </div>
        <form action="{{ route('kriteria.normalisasi') }}" method="POST"
              onsubmit="return confirm('Ubah semua bobot supaya totalnya 100%? Perbandingan antar kriteria tetap sama.')">
            @csrf
            <button class="btn btn-sm btn-dark text-nowrap"><i class="bi bi-magic"></i> Jadikan 100%</button>
        </form>
    </div>
@endif
