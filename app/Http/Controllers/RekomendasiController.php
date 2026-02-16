<?php

namespace App\Http\Controllers;

use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth; // ✅ JANGAN LUPA IMPORT INI

class RekomendasiController extends Controller
{
    public function cari(Request $request)
    {
        // 1. Ambil Data Wisata & Hitung Rating
        $allWisata = Wisata::with('reviews')->get(); 
        foreach($allWisata as $w) {
            $w->rata_rata = $w->reviews->avg('rating') ?? 0;
            $w->jumlah_ulasan = $w->reviews->count();
        }

        // ✅ TAMBAHAN PENTING: Ambil data 'Jejak Petualang' user
        $visitedIds = [];

        if (Auth::check()) {
            // Kalau Login: Ambil dari Database
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $visitedIds = $user->visitedPlaces()->pluck('wisata_id')->toArray();
        } else {
            // Kalau Tamu: Ambil dari Session
            $visitedIds = session()->get('guest_visited_ids', []);
        }

        // ============================================================
        // JIKA ADA REQUEST CARI RUTE
        // ============================================================
        if ($request->filled('lat') && $request->input('action') === 'cari_rute') {
            
            // --- INPUT DASAR ---
            $userLat  = $request->input('lat'); 
            $userLong = $request->input('long'); 
            $budget   = $request->input('budget', 100000); 
            $limitTujuan = $request->input('limit', 100); 

            $rawRuteFix = $request->input('rute_fix', []);
            $ruteFixIds = array_map('intval', $rawRuteFix); 

            $kategori = $request->input('kategori');
            $wilayah  = $request->input('wilayah');
            $hari     = $request->input('hari');
            $minRating = $request->input('min_rating', 0);

            // --- FILTER KANDIDAT ---
            $manualCandidates = $allWisata->whereIn('id', $ruteFixIds);
            $autoCandidates = $allWisata;

            if ($kategori) {
                $autoCandidates = $autoCandidates->where('kategori', $kategori);
            }
            if ($wilayah) {
                $autoCandidates = $autoCandidates->filter(function($item) use ($wilayah) {
                    return false !== stripos($item->alamat, $wilayah);
                });
            }
            if ($hari) {
                $autoCandidates = $autoCandidates->filter(function($item) use ($hari) {
                    return false !== stripos($item->hari_buka, $hari);
                });
            }
            if ($minRating > 0) {
                $autoCandidates = $autoCandidates->filter(function($item) use ($minRating) {
                    return $item->rata_rata >= $minRating;
                });
            }

            $poolKandidat = $manualCandidates->merge($autoCandidates)->unique('id');

            // --- PERSIAPAN VARIABEL ---
            $hasilRekomendasi = collect(); 
            $sudahMasukIds = []; 
            $sisaBudget = $budget;
            $totalBiaya = 0;
            
            $reservedBudget = $manualCandidates->sum('harga_tiket');

            $currentLat  = $userLat;
            $currentLong = $userLong;

            // Cek Start Point (Radius 100m)
            foreach ($allWisata as $cekWisata) {
                $jarakCek = $this->hitungJarak($userLat, $userLong, $cekWisata->latitude, $cekWisata->longitude);
                if ($jarakCek < 0.1) { 
                    $sudahMasukIds[] = $cekWisata->id; 
                    if (in_array($cekWisata->id, $ruteFixIds)) {
                         $reservedBudget -= $cekWisata->harga_tiket;
                    }
                }
            }
            $poolKandidat = $poolKandidat->whereNotIn('id', $sudahMasukIds);

            // --- ALGORITMA GREEDY ---
            while ($poolKandidat->count() > 0) {
                
                $sisaManualDiPool = $poolKandidat->whereIn('id', $ruteFixIds)->count();
                
                if ($hasilRekomendasi->count() >= $limitTujuan && $sisaManualDiPool == 0) {
                    break; 
                }

                foreach ($poolKandidat as $k) {
                    $k->jarak_temp = $this->hitungJarak($currentLat, $currentLong, $k->latitude, $k->longitude);
                }

                $poolKandidat = $poolKandidat->sortBy('jarak_temp');
                $nearest = $poolKandidat->first();

                $isManualSelection = in_array($nearest->id, $ruteFixIds);
                $bisaMasuk = false;

                if ($isManualSelection) {
                    $reservedBudget -= $nearest->harga_tiket; 
                    $bisaMasuk = true; 
                } else {
                    if (($sisaBudget - $nearest->harga_tiket) >= $reservedBudget) {
                        $bisaMasuk = true;
                    }
                }

                if ($bisaMasuk && ($isManualSelection || $sisaBudget >= $nearest->harga_tiket)) {
                    $nearest->jarak_km = $nearest->jarak_temp;
                    $hasilRekomendasi->push($nearest);
                    $sudahMasukIds[] = $nearest->id;

                    $sisaBudget -= $nearest->harga_tiket; 
                    $totalBiaya += $nearest->harga_tiket;
                    
                    $currentLat  = $nearest->latitude;
                    $currentLong = $nearest->longitude;
                }

                $poolKandidat = $poolKandidat->reject(function ($value) use ($nearest) {
                    return $value->id == $nearest->id;
                });
            }

            $wisataLain = $allWisata->whereNotIn('id', $sudahMasukIds)->values();

            // ✅ KEMBALIKAN VIEW DENGAN visitedIds
            return view('index', [
                'hasil'         => $hasilRekomendasi,
                'total_biaya'   => $totalBiaya,
                'sisa_budget'   => $sisaBudget,
                'wisata_lain'   => $wisataLain, 
                'semua_wisata'  => $allWisata,  
                'budget_harian' => $budget,
                'visitedIds'    => $visitedIds // <--- KIRIM DATA INI KE VIEW
            ]);
        }

        // ============================================================
        // DEFAULT VIEW (TAMPILAN AWAL)
        // ============================================================
        return view('index', [ 
            'semua_wisata' => $allWisata,
            'visitedIds'   => $visitedIds // <--- KIRIM DATA INI JUGA
        ]);
    }

    // --- FUNGSI RUMUS JARAK ---
    private function hitungJarak($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; 
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        return round($distance, 2);
    }

    public function show($id)
    {
        $wisata = Wisata::findOrFail($id);
        return view('detail', [
            'wisata' => $wisata
        ]);
    }
}