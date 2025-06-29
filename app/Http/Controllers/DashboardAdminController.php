<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Akun;
use App\Models\Unit;
use App\Models\Divisi;
use App\Models\Jurnal_Umum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $labels = [];
        $data = [];

        for ($i = 29; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($tanggal)->format('d M');

            $jumlah = Jurnal_Umum::whereDate('tanggal', $tanggal)->count();
            $data[] = $jumlah;
        }

        return view('admin.index', compact('user', 'labels', 'data'));
    }

    public function index2()
    {
        $user = auth()->user();

        $labels = [];
        $data = [];

        for ($i = 29; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($tanggal)->format('d M');

            $jumlah = Jurnal_Umum::whereDate('tanggal', $tanggal)->count();
            $data[] = $jumlah;
        }

        return view('admin.index', compact('user', 'labels', 'data'));
    }
}
