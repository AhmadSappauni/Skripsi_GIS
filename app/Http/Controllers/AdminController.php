<?php

namespace App\Http\Controllers;

use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File; // Penting untuk hapus file

class AdminController extends Controller
{
    // 1. TAMPILKAN DASHBOARD
    // 1. TAMPILKAN DASHBOARD (SEARCH & ADVANCED FILTER)
    public function index(Request $request)
    {
        $query = Wisata::query();

        // 1. Logic Search Biasa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_tempat', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // 2. Logic Filter Kategori
        if ($request->filled('kategori_filter')) {
            $query->where('kategori', $request->kategori_filter);
        }

        // 3. Logic Filter Harga (Gratis / Berbayar)
        if ($request->filled('harga_filter')) {
            if ($request->harga_filter == 'gratis') {
                $query->where('harga_tiket', 0);
            } elseif ($request->harga_filter == 'berbayar') {
                $query->where('harga_tiket', '>', 0);
            }
        }

        // 4. Logic Filter Jam (24 Jam)
        if ($request->filled('jam_filter') && $request->jam_filter == '24jam') {
            $query->where('jam_buka', '24 Jam');
        }

        // 5. Logic Filter Hari
        if ($request->filled('hari_filter')) {
            $hari = $request->hari_filter;
            // Karena data hari disimpan sebagai JSON/String misal "Senin,Selasa"
            $query->where('hari_buka', 'like', "%{$hari}%");
        }

        $dataWisata = $query->latest()->paginate(10)->withQueryString(); // withQueryString agar filter tidak hilang saat ganti halaman

        return view('admin.index', compact('dataWisata'));
    }

    // 2. FORM TAMBAH
    public function create()
    {
        return view('admin.create');
    }

    // 3. PROSES SIMPAN (STORE) - PERBAIKAN LOGIKA GAMBAR
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nama_tempat' => 'required|string',
            'kategori'    => 'required|string',
            'latitude'    => 'required',
            'longitude'   => 'required',
            'hari_buka'   => 'required|array',
            'gambar_file' => 'nullable|image|max:2048', // Max 2MB agar DB tidak terlalu berat
            'gambar_url'  => 'nullable|url',
        ]);

        $finalGambar = null;

        // --- LOGIKA BARU: SIMPAN SEBAGAI BASE64 KE DATABASE ---
        
        // Prioritas 1: User Upload File -> Ubah jadi kode teks (Base64)
        if ($request->hasFile('gambar_file')) {
            $file = $request->file('gambar_file');
            
            // Baca isi file
            $path = file_get_contents($file->getRealPath());
            // Encode ke Base64
            $base64 = base64_encode($path);
            // Gabungkan header tipe data
            $finalGambar = 'data:' . $file->getMimeType() . ';base64,' . $base64;
            
        } 
        // Prioritas 2: User Isi URL
        elseif ($request->filled('gambar_url')) {
            $finalGambar = $request->gambar_url;
        }

        // Logika Jam Buka
        $jamBuka = $request->has('is_24_jam')
            ? '24 Jam'
            : ($request->jam_buka . ' - ' . $request->jam_tutup . ' WITA');

        // Simpan ke Database
        Wisata::create([
            'nama_tempat' => $request->nama_tempat,
            'harga_tiket' => $request->harga_tiket ?? 0,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi ?? '',
            'gambar'      => $finalGambar, // <-- Ini sekarang isinya teks panjang (Base64)
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'alamat'      => $request->alamat ?? '',
            'hari_buka'   => implode(',', $request->hari_buka),
            'jam_buka'    => $jamBuka,
        ]);

        return redirect()->route('admin.index')->with('sukses', 'Data wisata berhasil disimpan!');
    }

    // 4. FORM EDIT
    public function edit($id)
    {
        $wisata = Wisata::findOrFail($id);
        return view('admin.edit', compact('wisata'));
    }

    // 5. PROSES UPDATE - PERBAIKAN HAPUS FILE LAMA
    public function update(Request $request, $id)
    {
        $wisata = Wisata::findOrFail($id);

        $request->validate([
            'nama_tempat' => 'required|string',
            'latitude'    => 'required',
            'longitude'   => 'required',
            'hari_buka'   => 'required|array',
            'gambar_file' => 'nullable|image|max:2048',
            'gambar_url'  => 'nullable|url',
        ]);

        $finalGambar = $wisata->gambar; // Default: Pakai gambar lama

        // --- LOGIKA BARU: UPDATE GAMBAR BASE64 ---
        
        // Skenario A: User Upload File Baru
        if ($request->hasFile('gambar_file')) {
            // Kita tidak perlu File::delete() lagi karena tidak ada file fisik yang dihapus
            
            $file = $request->file('gambar_file');
            $path = file_get_contents($file->getRealPath());
            $base64 = base64_encode($path);
            $finalGambar = 'data:' . $file->getMimeType() . ';base64,' . $base64;
        } 
        // Skenario B: User Ganti URL
        elseif ($request->filled('gambar_url')) {
            $finalGambar = $request->gambar_url;
        }

        // Logika Jam
        $jamBuka = $request->has('is_24_jam')
            ? '24 Jam'
            : ($request->jam_buka . ' - ' . $request->jam_tutup . ' WITA');

        // Update Database
        $wisata->update([
            'nama_tempat' => $request->nama_tempat,
            'kategori'    => $request->kategori,
            'harga_tiket' => $request->harga_tiket,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'deskripsi'   => $request->deskripsi,
            'alamat'      => $request->alamat,
            'hari_buka'   => implode(',', $request->hari_buka),
            'jam_buka'    => $jamBuka,
            'gambar'      => $finalGambar,
        ]);

        return redirect()->route('admin.index')->with('sukses', 'Data berhasil diperbarui!');
    }

    // 6. HAPUS DATA
    public function destroy($id)
    {
        $wisata = Wisata::find($id);

        if ($wisata) {
            // Hapus file fisik jika ada di folder images
            if ($wisata->gambar && File::exists(public_path($wisata->gambar))) {
                File::delete(public_path($wisata->gambar));
            }

            $wisata->delete();
            return redirect()->route('admin.index')->with('sukses', 'Data berhasil dihapus!');
        }

        return redirect()->route('admin.index')->with('error', 'Data tidak ditemukan!');
    }
}