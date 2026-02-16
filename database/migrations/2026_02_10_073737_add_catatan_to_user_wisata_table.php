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
        Schema::table('user_wisata', function (Blueprint $table) {
            // Tambah kolom text yang boleh kosong (nullable)
            $table->text('catatan')->nullable()->after('wisata_id'); 
        });
    }

    function down()
    {
        Schema::table('user_wisata', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};
