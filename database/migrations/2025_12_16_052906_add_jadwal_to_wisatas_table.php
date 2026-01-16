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
        Schema::table('wisatas', function (Blueprint $table) {
            // Menyimpan hari buka (Misal: "Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu")
            $table->string('hari_buka')->nullable()->default('Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu');
            
            // Menyimpan jam buka (Misal: "08:00 - 17:00")
            $table->string('jam_buka')->nullable()->default('08:00 - 17:00');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('wisatas', function (Blueprint $table) {
            $table->dropColumn(['hari_buka', 'jam_buka']);
        });
    }
};
