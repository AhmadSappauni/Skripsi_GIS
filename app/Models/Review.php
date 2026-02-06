<?php

namespace App\Models;

use App\Models\Wisata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;
    protected $fillable = ['wisata_id', 'user_id', 'rating', 'komentar', 'nama_pengulas'];

    // Relasi balik ke wisata
    public function wisata() {
        return $this->belongsTo(Wisata::class);
    }
}
