@if ($tidakLengkap->isNotEmpty())
    <div class="alert alert-warning d-flex gap-2 align-items-start">
        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
        <div>
            Alternatif berikut belum lengkap nilainya sehingga <strong>tidak ikut dihitung</strong>:
            @foreach ($tidakLengkap as $a)
                <a href="{{ route('penilaian.edit', $a) }}">{{ $a->kode }} {{ $a->nama }}</a>@if (! $loop->last), @endif
            @endforeach
        </div>
    </div>
@endif
