<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Hak_Akses;

class DashboardController extends Controller
{
    
    public function index()
    {
        $role = Auth::user()->role;
        $hakAkses = Hak_Akses::where('id_akuntan_unit', auth()->user()->id_akuntan_unit)->first();

        return view('index', compact('role', 'hakAkses'));
    }

}
