<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Wisata;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'wisata_id' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string'
        ]);

        // === LOGIKA TEST MODE (TANPA LOGIN) ===
        // Nanti kalau mau production, ganti baris bawah ini dengan: Auth::id()
        $userId = 1; 
        $nama = 'Pengunjung Test'; // Atau ambil dari input kalau mau

        Review::create([
            'wisata_id' => $request->wisata_id,
            'user_id'   => $userId,
            'nama_pengulas' => $nama,
            'rating'    => $request->rating,
            'komentar'  => $request->komentar
        ]);

        // Hitung Rata-rata baru untuk update tampilan (Opsional tapi bagus)
        $newAvg = Review::where('wisata_id', $request->wisata_id)->avg('rating');

        return response()->json([
            'status' => 'success', 
            'message' => 'Ulasan berhasil dikirim!',
            'new_avg' => round($newAvg, 1)
        ]);
    }
}