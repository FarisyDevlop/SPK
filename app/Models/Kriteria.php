<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    protected $table = 'kriteria';

    protected $fillable = ['kode', 'nama', 'satuan', 'bobot', 'jenis'];

    protected $casts = [
        'bobot' => 'float',
    ];

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    /** Nama lengkap + satuan, mis. "Biaya Investasi & Lisensi (Rp Juta)" */
    public function getLabelAttribute(): string
    {
        return $this->satuan ? "{$this->nama} ({$this->satuan})" : $this->nama;
    }
}
