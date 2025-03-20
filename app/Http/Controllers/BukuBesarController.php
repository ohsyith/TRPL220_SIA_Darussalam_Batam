<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Buku_Besar;
use Illuminate\Http\Request;
use App\Models\Detail_Jurnal_Umum;
use Illuminate\Support\Facades\DB;

class BukuBesarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil ID jurnal umum yang sudah diposting ke buku besar
        $postedJurnalIds = DB::table('buku_besar')
                            ->pluck('id_jurnal_umum')
                            ->toArray();
        
        // Ambil data detail_jurnal_umum yang hanya ada di buku besar
        $query = Detail_Jurnal_Umum::with(['jurnal_umum', 'akun'])
                ->whereIn('id_jurnal_umum', $postedJurnalIds); // Hanya jurnal yang sudah diposting
        
        // Filter berdasarkan tanggal dari jurnal umum
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('jurnal_umum', function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            });
        }

        // Filter berdasarkan akun
        if ($request->filled('akun')) {
            $query->where('id_akun', $request->akun);
        }

        // Filter berdasarkan pencarian umum (misalnya cari di keterangan atau no_bukti)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('jurnal_umum', function ($q) use ($search) {
                $q->where('no_bukti', 'LIKE', "%$search%")
                ->orWhere('keterangan', 'LIKE', "%$search%");
            });
        }

        // Ambil hasil filter
        $detail_jurnal = $query->get();

        // Ambil daftar akun untuk dropdown filter
        $akunList = Akun::all();

        return view('buku-besar', compact('detail_jurnal', 'akunList'));
    }





    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Buku_Besar $buku_Besar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Buku_Besar $buku_Besar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Buku_Besar $buku_Besar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buku_Besar $buku_Besar)
    {
        //
    }
}
