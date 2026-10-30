<?php

if (! function_exists('angka')) {
    /** Format angka gaya Indonesia: 0,7909 / 1.250,5 */
    function angka(float|int|string|null $nilai, int $desimal = 4): string
    {
        return $nilai === null ? '-' : number_format((float) $nilai, $desimal, ',', '.');
    }
}

if (! function_exists('angka_ringkas')) {
    /** Tanpa nol di belakang koma: 350 / 0,15 / 12,5 */
    function angka_ringkas(float|int|string|null $nilai): string
    {
        if ($nilai === null) {
            return '-';
        }
        $teks = number_format((float) $nilai, 4, ',', '.');

        return rtrim(rtrim($teks, '0'), ',');
    }
}

if (! function_exists('angka_ringkas_input')) {
    /** Untuk value <input type="number">: titik desimal, tanpa nol berlebih (350, 0.15) */
    function angka_ringkas_input(float|int|string $nilai): string
    {
        return rtrim(rtrim(number_format((float) $nilai, 4, '.', ''), '0'), '.');
    }
}
