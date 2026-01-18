<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('wisatas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tempat');
            $table->text('deskripsi')->nullable();
            $table->string('alamat')->nullable();
            // Koordinat kita pisah biar gampang dihitung rumusnya
            $table->decimal('latitude', 10, 8); 
            $table->decimal('longitude', 11, 8);
            $table->integer('harga_tiket'); // Penting untuk Greedy
            $table->string('kategori');
            $table->string('gambar')->nullable(); // URL atau path gambar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisatas');
    }
};
