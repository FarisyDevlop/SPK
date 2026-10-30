<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alternatif extends Model
{
    protected $table = 'alternatif';

    protected $fillable = ['kode', 'nama', 'keterangan'];

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }
}
