<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Dashboard') – SPK SAW Vendor SIMRS</title>

    {{-- Semua aset disimpan lokal di public/vendor, jadi aplikasi tetap jalan tanpa internet --}}
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">

    <style>
        @foreach ([400 => 'Regular', 500 => 'Medium', 600 => 'SemiBold', 700 => 'Bold', 800 => 'ExtraBold'] as $berat => $file)
        @font-face { font-family: 'Plus Jakarta Sans'; font-weight: {{ $berat }}; font-display: swap; src: url('{{ asset("vendor/fonts/PlusJakartaSans-$file.woff2") }}') format('woff2'); }
        @endforeach

        /* Palet "ruang operasi": hijau scrub, mint steril, emas hanya untuk juara */
        :root {
            --scrub: #0c3f3a;          /* sidebar & panel juara */
            --hijau: #0f6b61;          /* warna utama (tombol, link, batang juara) */
            --hijau-gelap: #0a524a;
            --mint: #dff0eb;           /* latar aktif & aksen lembut */
            --mint-batang: #9ccfc3;    /* batang grafik non-juara */
            --latar: #eef3f1;          /* latar halaman */
            --kartu: #ffffff;
            --tinta: #13211f;
            --tinta-2: #4b5d5a;
            --redup: #7d8c89;
            --garis: #dbe5e2;
            --emas: #c98a00;           /* hanya untuk peringkat 1 */
            --bs-body-font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            --bs-body-color: var(--tinta);
            --bs-primary: var(--hijau);
            --bs-primary-rgb: 15, 107, 97;
            --bs-link-color: var(--hijau);
            --bs-link-color-rgb: 15, 107, 97;
            --bs-link-hover-color: var(--hijau-gelap);
            --bs-link-hover-color-rgb: 10, 82, 74;
            --bs-border-color: var(--garis);
        }
        body { background: var(--latar); color: var(--tinta); font-size: .925rem; -webkit-font-smoothing: antialiased; }
        a { text-underline-offset: 3px; }
        .btn { border-radius: 10px; font-weight: 600; padding: .5rem 1rem; }
        .btn-sm { border-radius: 8px; padding: .25rem .55rem; }
        .btn-primary { --bs-btn-bg: var(--hijau); --bs-btn-border-color: var(--hijau); --bs-btn-hover-bg: var(--hijau-gelap); --bs-btn-hover-border-color: var(--hijau-gelap); --bs-btn-active-bg: var(--scrub); --bs-btn-focus-shadow-rgb: 15, 107, 97; }
        .btn-outline-primary { --bs-btn-color: var(--hijau); --bs-btn-border-color: #b9d8d0; --bs-btn-hover-bg: var(--hijau); --bs-btn-hover-border-color: var(--hijau); --bs-btn-active-bg: var(--hijau-gelap); }
        .btn-light { --bs-btn-bg: #fff; --bs-btn-border-color: var(--garis); --bs-btn-hover-bg: var(--mint); }
        .form-control, .form-select, .input-group-text { border-radius: 10px; border-color: #cddcd8; }
        .input-group-text { background: #f3f8f6; color: var(--tinta-2); }
        .form-control:focus, .form-check-input:focus { border-color: var(--hijau); box-shadow: 0 0 0 .2rem rgba(15, 107, 97, .18); }
        .form-check-input:checked { background-color: var(--hijau); border-color: var(--hijau); }
        :focus-visible { outline: 2px solid var(--hijau); outline-offset: 2px; }

        /* Sidebar */
        .sidebar { width: 248px; background: var(--scrub); position: fixed; inset: 0 auto 0 0; z-index: 1030; display: flex; flex-direction: column; overflow-y: auto; }
        .sidebar .brand { padding: 1.4rem 1.25rem 1.2rem; color: #fff; }
        .sidebar .brand .logo { width: 40px; height: 40px; border-radius: 11px; background: var(--mint); color: var(--scrub); display: grid; place-items: center; font-size: 1.2rem; }
        .sidebar .brand .nama-app { font-weight: 800; font-size: 1rem; letter-spacing: -.01em; }
        .sidebar .brand small { color: #9cc4bc; font-size: .76rem; }
        .sidebar .menu-label { color: #7fa9a1; font-size: .74rem; padding: 1.1rem 1.5rem .35rem; font-weight: 600; }
        .sidebar a.nav-link { color: #d3e6e2; padding: .58rem .9rem; margin: 2px .75rem; border-radius: 10px; display: flex; gap: .7rem; align-items: center; font-weight: 500; }
        .sidebar a.nav-link i { font-size: 1.05rem; opacity: .85; }
        .sidebar a.nav-link:hover { background: rgba(223, 240, 235, .08); color: #fff; }
        .sidebar a.nav-link.active { background: var(--mint); color: var(--scrub); font-weight: 700; }
        .sidebar a.nav-link.active i { opacity: 1; }
        .sidebar .kaki { margin-top: auto; padding: 1.1rem 1.5rem 1.4rem; color: #7fa9a1; font-size: .74rem; line-height: 1.5; }
        .sidebar .kaki strong { color: #d3e6e2; font-weight: 600; display: block; }

        .konten { margin-left: 248px; min-height: 100vh; }
        .bar-hp { display: none; }
        .kepala { padding: 2rem 2rem 1.25rem; display: flex; align-items: flex-end; gap: 1rem; flex-wrap: wrap; }
        .kepala h1 { font-size: 1.75rem; font-weight: 800; letter-spacing: -.02em; margin: 0; line-height: 1.15; }
        .kepala .sub { color: var(--tinta-2); font-size: .9rem; margin-top: .3rem; max-width: 70ch; }
        .isi { padding: 0 2rem 3rem; }

        .kartu { background: var(--kartu); border: 1px solid var(--garis); border-radius: 16px; }
        .kartu-header { padding: 1.1rem 1.35rem; border-bottom: 1px solid var(--garis); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .kartu-header h2 { font-size: 1.02rem; font-weight: 700; margin: 0; letter-spacing: -.01em; }
        .kartu-header .ket { color: var(--tinta-2); font-size: .8rem; margin: .2rem 0 0; }
        .kartu-body { padding: 1.35rem; }

        .table { --bs-table-bg: transparent; --bs-table-hover-bg: #f5faf8; margin: 0; font-size: .875rem; }
        .table thead th { background: #f3f8f6; color: var(--tinta-2); font-weight: 600; font-size: .8rem; border-bottom: 1px solid var(--garis); white-space: nowrap; }
        .table td, .table th { padding: .7rem 1rem; vertical-align: middle; border-color: #e8efed; }
        .table tbody tr:last-child td { border-bottom: 0; }
        .baris-juara td { background: #fbf6e8 !important; }
        .num { text-align: right; font-variant-numeric: tabular-nums; }
        .kode { font-weight: 700; color: var(--hijau); font-variant-numeric: tabular-nums; }

        .badge-jenis { font-weight: 600; padding: .3em .6em; border-radius: 6px; font-size: .74rem; }
        .badge-benefit { background: #dcf1e7; color: #17694a; }
        .badge-cost { background: #fbe5e0; color: #a23a26; }

        .no-langkah { display: inline-grid; place-items: center; width: 28px; height: 28px; border-radius: 50%; background: var(--mint); color: var(--scrub); font-weight: 800; font-size: .85rem; margin-right: .55rem; vertical-align: 1px; }
        .rumus { background: #f3f8f6; border: 1px solid var(--garis); border-radius: 12px; padding: .8rem 1rem; font-size: .88rem; }
        .rumus .f { font-weight: 600; font-variant-numeric: tabular-nums; }

        .form-label { font-weight: 600; font-size: .86rem; }
        .aksi { white-space: nowrap; }
        .alert { border-radius: 12px; border: 0; }
        .alert-success { background: var(--mint); color: var(--scrub); }
        .alert-warning { background: #fdf3dc; color: #6b4a00; }
        .alert-warning a { color: #6b4a00; font-weight: 600; }
        .progress { background: #edf3f1; }

        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); transition: transform .2s; }
            .sidebar.buka { transform: none; box-shadow: 0 0 0 100vmax rgba(12, 63, 58, .35); }
            .konten { margin-left: 0; }
            .bar-hp { display: flex; align-items: center; gap: .75rem; padding: .75rem 1rem; background: var(--scrub); color: #fff; font-weight: 700; position: sticky; top: 0; z-index: 1020; }
            .bar-hp .btn { color: #fff; border: 0; background: transparent; font-size: 1.3rem; padding: 0 .25rem; }
            .kepala { padding: 1.25rem 1rem 1rem; }
            .kepala h1 { font-size: 1.4rem; }
            .isi { padding: 0 1rem 2rem; }
        }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
        @media print {
            .sidebar, .bar-hp, .tombol-kepala, .no-print { display: none !important; }
            .konten { margin: 0; }
            body { background: #fff; }
            .kartu { border: 0; }
        }
    </style>
    @stack('style')
</head>
<body>
@php
    $menu = [
        ['dashboard', 'Dashboard', 'bi-grid-1x2', 'dashboard'],
        null,
        ['kriteria.index', 'Data Kriteria', 'bi-list-check', 'kriteria.*'],
        ['alternatif.index', 'Data Alternatif', 'bi-building', 'alternatif.*'],
        ['penilaian.index', 'Data Penilaian', 'bi-table', 'penilaian.*'],
        null,
        ['perhitungan', 'Perhitungan SAW', 'bi-calculator', 'perhitungan'],
        ['hasil', 'Hasil Akhir', 'bi-trophy', 'hasil'],
    ];
    $label = ['Ringkasan', 'Data', 'Metode SAW'];
    $iLabel = 0;
@endphp

<aside class="sidebar" id="sidebar">
    <div class="brand d-flex align-items-center gap-2">
        <div class="logo"><i class="bi bi-hospital"></i></div>
        <div>
            <div class="nama-app lh-sm">SPK Vendor SIMRS</div>
            <small>Metode SAW</small>
        </div>
    </div>
    <nav class="pb-3">
        <div class="menu-label">{{ $label[$iLabel++] }}</div>
        @foreach ($menu as $m)
            @if ($m === null)
                <div class="menu-label">{{ $label[$iLabel++] }}</div>
            @else
                <a href="{{ route($m[0]) }}" class="nav-link {{ request()->routeIs($m[3]) ? 'active' : '' }}">
                    <i class="bi {{ $m[2] }}"></i> {{ $m[1] }}
                </a>
            @endif
        @endforeach
    </nav>
    <div class="kaki">
        <strong>RSUD Harapan Sehat</strong>
        Studi kasus Kelompok 4, mata kuliah Sistem Pendukung Keputusan
    </div>
</aside>

<div class="konten">
    <div class="bar-hp">
        <button class="btn" aria-label="Buka menu" onclick="document.getElementById('sidebar').classList.toggle('buka')"><i class="bi bi-list"></i></button>
        SPK Vendor SIMRS
    </div>
    <header class="kepala">
        <div class="flex-grow-1">
            <h1>@yield('judul', 'Dashboard')</h1>
            <div class="sub">@yield('subjudul')</div>
        </div>
        <div class="tombol-kepala d-flex gap-2">@yield('aksi')</div>
    </header>

    <main class="isi">
        @if (session('sukses'))
            <div class="alert alert-success d-flex align-items-center gap-2 alert-dismissible fade show no-print" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('konten')
    </main>
</div>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
@stack('script')
</body>
</html>
