<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();          // K1, K2, ...
            $table->string('nama');                          // Biaya Investasi & Lisensi
            $table->string('satuan')->nullable();            // Rp Juta, Skor 0-100, ...
            $table->decimal('bobot', 6, 4);                  // 0.15 = 15%
            $table->enum('jenis', ['benefit', 'cost']);      // atribut kriteria
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};
