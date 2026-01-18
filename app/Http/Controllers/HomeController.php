<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wisata;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil semua data wisata
        $wisata = Wisata::all();
        
        // Kirim ke view 'welcome'
        return view('welcome', compact('wisata'));
    }
}