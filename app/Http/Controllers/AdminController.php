<?php

namespace App\Http\Controllers;

use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // 1. TAMPILKAN DASHBOARD (SEARCH & PAGINATION)
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Gunakan When untuk query builder yang lebih rapi
        $dataWisata = Wisata::when($search, function ($query, $search) {
            return $query->where('nama_tempat', 'like', "%{$search}%")
                ->orWhere('kategori', 'like', "%{$search}%")
                ->orWhere('alamat', 'like', "%{$search}%");
        })
            ->latest() // Urutkan dari yang terbaru
            ->paginate(10); // Gunakan Paginate agar tidak berat jika data ribuan

        return view('admin.index', compact('dataWisata'));
    }

    // 2. FORM TAMBAH DATA
    public function create()
    {
        return view('admin.create');
    }

    // 3. PROSES SIMPAN DATA (CREATE)
    public function store(Request $request)
    {
        // VALIDASI
        $request->validate([
            'nama_tempat' => 'required|string',
            'kategori' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required',
            'jam_operasional' => 'required',
        ]);

        // HANDLE FILE
        $gambarPath = null;
        if ($request->hasFile('gambar_file')) {
            $gambarPath = $request->file('gambar_file')
                ->store('wisata', 'public');
        }

        if ($request->is_24_jam) {
            $jamOperasional = '24 Jam';
            $jamBuka = null;
            $jamTutup = null;
        } else {
            $jamOperasional = $request->jam_operasional;
            $jamBuka = $request->jam_buka;
            $jamTutup = $request->jam_tutup;
        }

        // SIMPAN
        Wisata::create([
            'nama_tempat'     => $request->nama_tempat,
            'harga_tiket'     => $request->harga_tiket ?? 0,
            'kategori'        => $request->kategori,
            'deskripsi'       => $request->deskripsi ?? '',
            'gambar'          => $gambarPath,
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
            'alamat'          => $request->alamat ?? '',

            'jam_operasional' => $jamOperasional,
            'jam_buka'        => $jamBuka,
            'jam_tutup'       => $jamTutup,

            'hari_buka'       => json_encode($request->hari_buka),
            'is_24_jam'       => $request->is_24_jam ? 1 : 0,
        ]);

        return redirect()->route('admin.index')
            ->with('success', 'Data wisata berhasil disimpan');
    }



    // 4. FORM EDIT DATA
    public function edit($id)
    {
        $wisata = Wisata::findOrFail($id);
        return view('admin.edit', compact('wisata'));
    }

    // 5. PROSES UPDATE DATA
    public function update(Request $request, $id)
    {
        $wisata = Wisata::findOrFail($id);

        $request->validate([
            'nama_tempat' => 'required|string|max:255',
            'harga_tiket' => 'required|numeric|min:0',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'kategori'    => 'required',
            'hari_buka'   => 'required|array',
            'jam_buka'    => 'required|string',
            // Gambar opsional saat update (nullable)
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gambar_url'  => 'nullable|url',
        ]);

        $finalGambar = $wisata->gambar; // Default: Tetap pakai gambar lama

        // LOGIKA GANTI GAMBAR
        if ($request->hasFile('gambar_file')) {
            // 1. Hapus gambar lama (jika ada di server lokal)
            if ($wisata->gambar && !Str::startsWith($wisata->gambar, 'http')) {
                Storage::disk('public')->delete($wisata->gambar);
            }
            // 2. Upload gambar baru
            $finalGambar = $request->file('gambar_file')->store('wisata', 'public');
        } elseif ($request->filled('gambar_url')) {
            // Jika user mengisi URL baru, ganti gambar lama dengan URL ini
            // (Opsional: Bisa tambahkan logic hapus gambar lokal juga disini jika mau bersih)
            $finalGambar = $request->gambar_url;
        }

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
            'jam_buka'    => $request->jam_buka,
            'gambar'      => $finalGambar,
        ]);

        return redirect()->route('admin.index')->with('sukses', 'Data Berhasil Diperbarui!');
    }

    // 6. HAPUS DATA
    public function destroy($id)
    {
        $wisata = Wisata::find($id); // Pakai find biar tidak error 404 halaman putih

        if ($wisata) {
            // Hapus file fisik jika bukan URL internet
            if ($wisata->gambar && !Str::startsWith($wisata->gambar, 'http')) {
                Storage::disk('public')->delete($wisata->gambar);
            }

            $wisata->delete();
            return redirect()->route('admin.index')->with('sukses', 'Data & File Berhasil Dihapus! 🗑️');
        }

        return redirect()->route('admin.index')->with('error', 'Data tidak ditemukan!');
    }
}
