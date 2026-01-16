<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wisata; // Panggil Model yang tadi dibuat

class WisataSeeder extends Seeder
{
    public function run()
    {
        // Data 1: Murah & Dekat Pusat Banjarbaru
        Wisata::create([
            'nama_tempat' => 'Taman Van Der Pijl',
            'deskripsi'   => 'Taman kota di pusat Banjarbaru, cocok untuk santai.',
            'alamat'      => 'Jl. Pangeran Suriansyah, Banjarbaru',
            'latitude'    => -3.440422,
            'longitude'   => 114.829355,
            'harga_tiket' => 5000, // Anggap parkir
            'kategori'    => 'Alam',
            'gambar'      => 'https://placehold.co/600x400'
        ]);

        // Data 2: Agak Mahal & Agak Jauh
        Wisata::create([
            'nama_tempat' => 'Amanah Borneo Park',
            'deskripsi'   => 'Wahana rekreasi keluarga dan edukasi.',
            'alamat'      => 'Jl. Taruna Bhakti, Banjarbaru',
            'latitude'    => -3.486822,
            'longitude'   => 114.832766,
            'harga_tiket' => 30000,
            'kategori'    => 'Alam',
            'gambar'      => 'https://placehold.co/600x400'
        ]);

        // Data 3: Murah & Jauh (Tahura)
        Wisata::create([
            'nama_tempat' => 'Tahura Sultan Adam',
            'deskripsi'   => 'Hutan raya dengan pemandangan bukit dan waduk.',
            'alamat'      => 'Mandiangin, Kabupaten Banjar',
            'latitude'    => -3.535600,
            'longitude'   => 114.996500,
            'harga_tiket' => 15000,
            'kategori'    => 'Alam',
            'gambar'      => 'https://placehold.co/600x400'
        ]);

        // Data 4: Religi (Martapura)
        Wisata::create([
            'nama_tempat' => 'Masjid Agung Al-Karomah',
            'deskripsi'   => 'Masjid terbesar dan bersejarah di Martapura.',
            'alamat'      => 'Jl. A. Yani, Martapura',
            'latitude'    => -3.413700,
            'longitude'   => 114.845800,
            'harga_tiket' => 0, // Gratis
            'kategori'    => 'Religi',
            'gambar'      => 'https://placehold.co/600x400'
        ]);

        // Data 5: Kuliner (Soto Bang Amat - Banjarmasin)
        Wisata::create([
            'nama_tempat' => 'Soto Bang Amat',
            'deskripsi'   => 'Kuliner legendaris di pinggir sungai.',
            'alamat'      => 'Jl. Banua Anyar, Banjarmasin',
            'latitude'    => -3.298900,
            'longitude'   => 114.609500,
            'harga_tiket' => 25000, // Estimasi harga makan
            'kategori'    => 'Kuliner',
            'gambar'      => 'https://placehold.co/600x400'
        ]);
    }
}