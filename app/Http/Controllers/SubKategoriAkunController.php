<?php

namespace App\Http\Controllers;
use App\Models\Sub_Kategori_Akun;
use Illuminate\Http\Request;

class SubKategoriAkunController extends Controller
{
    public function index()
    {
        $subkategoriakun = Sub_Kategori_Akun::all();

        return view('sub-kategori-akun', compact('subkategoriakun'));
    }

}
