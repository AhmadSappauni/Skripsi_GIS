<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wisata extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nama_tempat', 'kategori', 'harga_tiket', 
        'latitude', 'longitude', 'gambar', 'galeri',
        'deskripsi', 'alamat', 'fasilitas',
        'hari_buka', 'jam_buka' 
    ];
    protected $casts = [
        'galeri' => 'array', 
        'fasilitas' => 'array',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest(); 
    }
}

