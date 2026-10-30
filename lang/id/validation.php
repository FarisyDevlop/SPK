<?php

// Pesan validasi Bahasa Indonesia (hanya aturan yang dipakai di aplikasi ini)
return [
    'required' => ':Attribute wajib diisi.',
    'unique' => ':Attribute sudah digunakan.',
    'numeric' => ':Attribute harus berupa angka.',
    'string' => ':Attribute harus berupa teks.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'max' => ['string' => ':Attribute maksimal :max karakter.', 'numeric' => ':Attribute maksimal :max.'],
    'min' => ['numeric' => ':Attribute minimal :min.', 'string' => ':Attribute minimal :min karakter.'],
    'gt' => ['numeric' => ':Attribute harus lebih besar dari :value.'],
    'lte' => ['numeric' => ':Attribute maksimal :value.'],

    'attributes' => [
        'bobot' => 'bobot',
        'jenis' => 'jenis kriteria',
        'satuan' => 'satuan',
        'keterangan' => 'keterangan',
    ],
];
