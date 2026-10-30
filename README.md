# SPK Pemilihan Vendor SIMRS – Metode SAW (Laravel 12)

Studi kasus **RSUD Harapan Sehat**, Kelompok 4 Sistem Pendukung Keputusan.
Data awal diambil dari spreadsheet *BaselineSPK_Kelompok[4]*.

## Cara menjalankan

Syarat: **PHP 8.2 atau lebih baru** dengan ekstensi `pdo_sqlite` aktif (XAMPP/Laragon sudah ada).

```bash
cd spk-saw
php artisan serve
```

Buka **http://127.0.0.1:8000** di browser.

Folder `vendor`, file `.env`, dan database SQLite (`database/database.sqlite`) yang sudah terisi
data spreadsheet sudah disertakan, jadi tidak perlu `composer install` atau migrate.

### Mengembalikan data ke kondisi awal spreadsheet
```bash
php artisan migrate:fresh --seed
```

### Kalau mau pakai MySQL (XAMPP/phpMyAdmin)
1. Buat database kosong, misalnya `spk_saw`.
2. Ubah `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=spk_saw
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Jalankan `php artisan migrate:fresh --seed`.

## Deploy online (GitHub + Render + Neon)

File yang dipakai: `Dockerfile`, `docker/`, `render.yaml`. Database online memakai PostgreSQL di Neon.

1. **Neon**: buat project (region Singapore), matikan *Connection pooling*, salin connection string
   `postgresql://...neon.tech/neondb?sslmode=require...`
2. **GitHub**: buat repo kosong, lalu dari folder proyek:
   ```bash
   git init
   git add .
   git commit -m "SPK SAW Laravel"
   git branch -M main
   git remote add origin https://github.com/USERNAME/spk-saw.git
   git push -u origin main
   ```
3. **Render**: New → Blueprint → pilih repo → isi `APP_KEY` (hasil `php artisan key:generate --show`) dan `DB_URL` (string dari Neon) → Apply.

Setiap kali server menyala, `docker/start.sh` menjalankan migrasi dan mengisi data spreadsheet
kalau database masih kosong. Untuk update, cukup `git add . && git commit -m "..." && git push`,
Render akan deploy ulang otomatis.

## Menu aplikasi

| Menu | Isi |
|---|---|
| Dashboard | Ringkasan data, vendor terbaik, grafik peringkat V, kontribusi tiap kriteria, bobot kriteria, alur SAW |
| Data Kriteria | CRUD kriteria: kode, nama, satuan, bobot (w), jenis benefit/cost |
| Data Alternatif | CRUD vendor SIMRS |
| Data Penilaian | Matriks keputusan X, input/ubah/kosongkan nilai per vendor |
| Perhitungan SAW | Langkah 1–4: matriks X, normalisasi R, w × r, nilai V |
| Hasil Akhir | Tabel peringkat + tombol cetak |

Tidak ada login dan CRUD user. CRUD hanya untuk data.

## Algoritma SAW

Kode utama ada di `app/Services/SawService.php`.

1. **Matriks keputusan X**: nilai tiap alternatif pada tiap kriteria (tabel `penilaian`).
2. **Normalisasi R**
   - Benefit: `r = x / max(x kolom)`
   - Cost: `r = min(x kolom) / x`
3. **Bobot**: `w × r`
4. **Nilai preferensi**: `V = Σ (w × r)`
5. **Peringkat**: V terbesar adalah alternatif terbaik.

Alternatif yang nilainya belum lengkap tidak ikut dihitung (muncul peringatan).
Kalau total bobot ≠ 100%, aplikasi juga menampilkan peringatan.

### Hasil dengan data spreadsheet

| Peringkat | Vendor | V |
|---|---|---|
| 1 | V5 ProHospital IT | 0,7909 |
| 2 | V2 HealthTech Solusi | 0,7904 |
| 3 | V6 Smart Clinic OS | 0,7839 |
| 4 | V8 Data Medis Utama | 0,7818 |
| 5 | V1 Medika Soft | 0,7488 |
| 6 | V4 Nusantara Care | 0,7474 |
| 7 | V3 Siber Medika | 0,7120 |
| 8 | V7 Prima Sistem | 0,7081 |

Hasil ini sama dengan perhitungan di spreadsheet.

## Struktur file penting

```
app/Services/SawService.php          algoritma SAW
app/Http/Controllers/                Dashboard, Kriteria, Alternatif, Penilaian, Perhitungan
app/Models/                          Kriteria, Alternatif, Penilaian
database/migrations/2026_10_30_*     tabel kriteria, alternatif, penilaian
database/seeders/DatabaseSeeder.php  data dari spreadsheet
resources/views/                     tampilan (Blade + Bootstrap 5)
routes/web.php                       daftar route
public/vendor/                       Bootstrap, ikon, font (lokal, jalan tanpa internet)
```
