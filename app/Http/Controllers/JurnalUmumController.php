<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Unit;
use App\Models\Jenis_Transaksi;
use App\Models\Divisi;
use App\Models\Jurnal_Umum;
use App\Models\Buku_Besar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Detail_Jurnal_Umum;

class JurnalUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Detail_Jurnal_Umum::with(['jurnal_umum', 'akun']);

        // Filter berdasarkan rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('jurnal_umum', function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            });
        }

        // Pencarian teks bebas
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('jurnal_umum', function ($q) use ($search) {
                $q->where('no_bukti', 'like', "%$search%")
                    ->orWhere('keterangan', 'like', "%$search%");
            })->orWhereHas('akun', function ($q) use ($search) {
                $q->where('akun', 'like', "%$search%");
            });
        }

        $detailjurnalumum = $query->get(); // Ambil data jurnal umum
        
        // Ambil ID jurnal umum yang sudah di-posting ke buku besar
        $postedJurnalIds = DB::table('buku_besar')
        ->whereNotNull('id_jurnal_umum')
        ->pluck('id_jurnal_umum')
        ->toArray();

            // dd($postedJurnalIds); // Uncomment baris ini untuk debugging


        return view('jurnal-umum', compact('detailjurnalumum', 'postedJurnalIds'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $id_unit = null;
        $id_divisi = null;

        if ($user->role === 'akuntan_unit') {
            $akuntanUnit = Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->first();
            $id_unit = $akuntanUnit?->id_unit;
        }

        if ($user->role === 'akuntan_divisi') {
            $akuntanDivisi = Akuntan_Divisi::where('id_akuntan_divisi', $user->id_user)->first();
            $id_divisi = $akuntanDivisi?->id_divisi;
        }


        $unit = Unit::all();
        $divisi = Divisi::all();
        $akun = Akun::all();

        return view('input-transaksi', compact('unit', 'divisi', 'akun', 'id_unit', 'id_divisi'));
    }






    /**
     * Display the specified resource.
     */
    public function show(Jurnal_Umum $jurnal_Umum)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jurnal_Umum $jurnal_Umum)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurnal_Umum $jurnal_Umum)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurnal_Umum $jurnal_Umum)
    {
        //
    }
}