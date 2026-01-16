<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wisata extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nama_tempat', 'kategori', 'harga_tiket', 
        'latitude', 'longitude', 'gambar', 
        'deskripsi', 'alamat',
        'hari_buka', 'jam_buka' 
    ];
}