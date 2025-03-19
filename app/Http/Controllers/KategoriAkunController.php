<?php

namespace App\Http\Controllers;
use App\Models\Kategori_Akun;
use Illuminate\Http\Request;

class KategoriAkunController extends Controller
{
    public function index()
    {
        $kategoriakun = Kategori_Akun::all();

        return view('kategori-akun', compact('kategoriakun'));
    }


    
}
