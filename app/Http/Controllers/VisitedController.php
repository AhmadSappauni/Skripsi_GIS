<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    // ✅ WAJIB ADA
use Illuminate\Support\Facades\Session; // ✅ WAJIB ADA (Kalo ini hilang, pasti Error 500)

class VisitedController extends Controller
{
    public function toggle($id)
    {
        $status = '';
        $message = '';

        // SKENARIO 1: USER LOGIN (Database)
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            $result = $user->visitedPlaces()->toggle($id);
            $status = count($result['attached']) > 0 ? 'added' : 'removed';
        } 
        
        // SKENARIO 2: TAMU / GUEST (Session Browser)
        else {
            $sessionKey = 'guest_visited_ids';
            
            // Ambil data dari session, default array kosong
            $currentVisited = Session::get($sessionKey, []);
            
            // Jaga-jaga kalau datanya rusak/bukan array
            if (!is_array($currentVisited)) {
                $currentVisited = [];
            }

            // Pastikan ID yang masuk adalah integer
            $id = (int)$id;

            if (in_array($id, $currentVisited)) {
                // HAPUS (Remove)
                $currentVisited = array_diff($currentVisited, [$id]);
                $status = 'removed';
            } else {
                // TAMBAH (Add)
                $currentVisited[] = $id;
                $status = 'added';
            }
            
            // Simpan array baru ke session (re-index biar rapi)
            Session::put($sessionKey, array_values($currentVisited));
            Session::save(); // Paksa simpan session
        }

        return response()->json([
            'status' => 'success',
            'action' => $status,
            'message' => $status == 'added' ? 'Ditandai dikunjungi' : 'Batal dikunjungi'
        ]);
    }

    public function saveNote(Request $request, $id)
    {
        $noteContent = $request->input('note');

        // SKENARIO 1: USER LOGIN (Simpan ke Database)
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // Update kolom 'catatan' di tabel pivot
            $user->visitedPlaces()->updateExistingPivot($id, ['catatan' => $noteContent]);
        }
        
        // SKENARIO 2: TAMU / GUEST (Simpan ke Session)
        else {
            $sessionKey = 'guest_notes';
            $currentNotes = Session::get($sessionKey, []);
            
            // Simpan catatan berdasarkan ID wisata
            $currentNotes[$id] = $noteContent;
            
            Session::put($sessionKey, $currentNotes);
            Session::save();
        }

        return response()->json(['status' => 'success', 'message' => 'Catatan tersimpan!']);
    }

    // Ambil semua ID & Catatan user ini
    public function getVisitedData()
    {
        $data = [];
        
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            // Ambil ID dan Catatan dari pivot
            $visited = $user->visitedPlaces()->get();
            foreach($visited as $v) {
                $data[$v->id] = $v->pivot->catatan;
            }
        } else {
            // Guest: Ambil dari session
            $data = Session::get('guest_notes', []);
        }

        return response()->json($data);
    }
}