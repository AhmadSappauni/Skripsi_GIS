<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wisata;

class RekomendasiController extends Controller
{
    public function cari(Request $request)
    {
        $allWisata = Wisata::all(); 

        if ($request->filled('lat') && $request->input('action') === 'cari_rute') {
            // 1. INPUT DASAR
            $userLat  = $request->input('lat'); 
            $userLong = $request->input('long'); 
            $budget   = $request->input('budget', 100000); 
            
            // --- FITUR BARU: LIMIT TUJUAN ---
            // Ambil input limit, default 100 (tak terbatas) jika user tidak isi
            $limitTujuan = $request->input('limit', 100); 

            // Ambil Array ID Manual & Pastikan isinya Angka (Integer)
            $rawRuteFix = $request->input('rute_fix', []);
            $ruteFixIds = array_map('intval', $rawRuteFix); 

            // Filter Tambahan
            $kategori = $request->input('kategori');
            $wilayah  = $request->input('wilayah');
            $hari     = $request->input('hari');

            // 2. SIAPKAN KANDIDAT WISATA
            
            // A. Kandidat Manual
            $manualCandidates = $allWisata->whereIn('id', $ruteFixIds);

            // B. Kandidat Otomatis
            $query = Wisata::query();
            if ($kategori) $query->where('kategori', $kategori);
            if ($wilayah)  $query->where('alamat', 'LIKE', '%' . $wilayah . '%');
            if ($hari)     $query->where('hari_buka', 'LIKE', '%' . $hari . '%');
            $autoCandidates = $query->get();

            // 3. GABUNGKAN
            $poolKandidat = $manualCandidates->merge($autoCandidates)->unique('id');

            // 4. PERSIAPAN VARIABEL HASIL
            $hasilRekomendasi = collect(); 
            $sudahMasukIds = []; 
            
            $sisaBudget = $budget;
            $totalBiaya = 0;
            
            // Hitung Budget Reserved untuk Manual
            $reservedBudget = $manualCandidates->sum('harga_tiket');

            // Titik Awal
            $currentLat  = $userLat;
            $currentLong = $userLong;

            // --- CEK START POINT ---
            foreach ($allWisata as $cekWisata) {
                $jarakCek = $this->hitungJarak($userLat, $userLong, $cekWisata->latitude, $cekWisata->longitude);
                if ($jarakCek < 0.1) { 
                    $sudahMasukIds[] = $cekWisata->id; 
                    if (in_array($cekWisata->id, $ruteFixIds)) {
                         $reservedBudget -= $cekWisata->harga_tiket;
                    }
                }
            }

            // Hapus start point dari pool
            $poolKandidat = $poolKandidat->whereNotIn('id', $sudahMasukIds);

            // ====================================================================
            // UNIFIED GREEDY LOOP (LENGKAP DENGAN LIMIT)
            // ====================================================================
            while ($poolKandidat->count() > 0) {
                
                // --- CEK LIMIT: BERHENTI JIKA SUDAH CUKUP ---
                // Syarat Berhenti: 
                // 1. Jumlah hasil >= Limit User
                // 2. DAN tidak ada sisa wisata Manual di pool (Manual wajib masuk dulu)
                
                $sisaManualDiPool = $poolKandidat->whereIn('id', $ruteFixIds)->count();
                
                if ($hasilRekomendasi->count() >= $limitTujuan && $sisaManualDiPool == 0) {
                    break; // STOP LOOP
                }

                // 1. Hitung Jarak
                foreach ($poolKandidat as $k) {
                    $k->jarak_temp = $this->hitungJarak($currentLat, $currentLong, $k->latitude, $k->longitude);
                }

                // 2. Urutkan (Greedy)
                $poolKandidat = $poolKandidat->sortBy('jarak_temp');
                $nearest = $poolKandidat->first();

                // Cek Manual Selection by ID
                $isManualSelection = in_array($nearest->id, $ruteFixIds);

                // 3. Cek Kelayakan
                $bisaMasuk = false;

                if ($isManualSelection) {
                    $reservedBudget -= $nearest->harga_tiket; 
                    $bisaMasuk = true; 
                } else {
                    if (($sisaBudget - $nearest->harga_tiket) >= $reservedBudget) {
                        $bisaMasuk = true;
                    }
                }

                // 4. Eksekusi (Dengan Fitur Terobos)
                if ($bisaMasuk && ($isManualSelection || $sisaBudget >= $nearest->harga_tiket)) {
                    
                    $nearest->jarak_km = $nearest->jarak_temp;
                    $hasilRekomendasi->push($nearest);
                    $sudahMasukIds[] = $nearest->id;

                    $sisaBudget -= $nearest->harga_tiket; 
                    $totalBiaya += $nearest->harga_tiket;
                    
                    $currentLat  = $nearest->latitude;
                    $currentLong = $nearest->longitude;

                }

                // 5. Hapus dari kandidat
                $poolKandidat = $poolKandidat->reject(function ($value) use ($nearest) {
                    return $value->id == $nearest->id;
                });
            }

            // Siapkan Data Wisata Lain
            $wisataLain = $allWisata->whereNotIn('id', $sudahMasukIds)->values();

            return view('index', [
                'hasil'        => $hasilRekomendasi,
                'total_biaya'  => $totalBiaya,
                'sisa_budget'  => $sisaBudget,
                'wisata_lain'  => $wisataLain, 
                'semua_wisata' => $allWisata,  
                'budget_harian'=> $budget 
            ]);
        }

        return view('index', [ 'semua_wisata' => $allWisata ]);
    }

    // --- FUNGSI RUMUS JARAK (PRIVATE) ---
    private function hitungJarak($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Radius bumi dalam KM
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