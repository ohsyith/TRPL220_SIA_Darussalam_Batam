<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Divisi;
use App\Models\Buku_Besar;
use App\Models\Jurnal_Umum ;
use App\Models\Akuntan_Unit;
use App\Models\Akuntan_Divisi;
use Illuminate\Http\Request;
use App\Models\Detail_Jurnal_Umum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BukuBesarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     // Ambil ID jurnal umum yang sudah diposting ke buku besar
    //     $postedJurnalIds = DB::table('buku_besar')
    //                         ->pluck('id_jurnal_umum')
    //                         ->toArray();
        
    //     // Ambil data detail_jurnal_umum yang hanya ada di buku besar
    //     $query = Detail_Jurnal_Umum::with(['jurnal_umum', 'akun'])
    //             ->whereIn('id_jurnal_umum', $postedJurnalIds); // Hanya jurnal yang sudah diposting
        
    //     // Filter berdasarkan tanggal dari jurnal umum
    //     if ($request->filled('start_date') && $request->filled('end_date')) {
    //         $query->whereHas('jurnal_umum', function ($q) use ($request) {
    //             $q->whereBetween('tanggal', [$request->start_date, $request->end_date]);
    //         });
    //     }

    //     // Filter berdasarkan akun
    //     if ($request->filled('akun')) {
    //         $query->where('id_akun', $request->akun);
    //     }

    //     // Filter berdasarkan pencarian umum (misalnya cari di keterangan atau no_bukti)
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->whereHas('jurnal_umum', function ($q) use ($search) {
    //             $q->where('no_bukti', 'LIKE', "%$search%")
    //             ->orWhere('keterangan', 'LIKE', "%$search%");
    //         });
    //     }

    //     // Ambil hasil filter
    //     $detail_jurnal = $query->get();

    //     // Ambil daftar akun untuk dropdown filter
    //     $akunList = Akun::all();

    //     return view('buku-besar', compact('detail_jurnal', 'akunList'));
    // }

    // public function index(Request $request)
    // {
    //     // Parameter untuk stored procedure
    //     $akun_id = $request->filled('akun') ? $request->akun : null;
    //     $start_date = $request->filled('start_date') ? $request->start_date : null;
    //     $end_date = $request->filled('end_date') ? $request->end_date : null;
        
    //     // Panggil stored procedure
    //     $detail_jurnal = DB::select(
    //         'CALL get_laporan_buku_besar(?, ?, ?)', 
    //         [$akun_id, $start_date, $end_date]
    //     );
        
    //     $detail_jurnal = collect($detail_jurnal);
        
    //     // Hitung total debit & kredit
    //     $total_debit = $detail_jurnal->where('debit_kredit', 'debit')->sum('nominal');
    //     $total_kredit = $detail_jurnal->where('debit_kredit', 'kredit')->sum('nominal');
        
    //     // Ambil daftar akun untuk dropdown filter
    //     $akunList = Akun::all();
        
    //     return view('buku-besar', compact('detail_jurnal', 'akunList', 'total_debit', 'total_kredit'));
    // }

    public function index(Request $request)
    {
        $akun_id = $request->filled('akun') ? $request->akun : 1;
        $start_date = $request->filled('start_date') ? $request->start_date : null;
        $end_date = $request->filled('end_date') ? $request->end_date : null;

        $user = Auth::user();

        $id_unit = null;
        $id_divisi = null;

        if ($user->role === 'akuntan_unit') {
            $id_unit = Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
        } elseif ($user->role === 'akuntan_divisi') {
            $id_divisi = Akuntan_Divisi::where('id_akuntan_divisi', $user->id_user)->value('id_divisi');
        }

        // Ambil data dari stored procedure
        $detail_jurnal = DB::select(
            'CALL laporan_buku_besar(?, ?, ?, ?, ?)', 
            [$akun_id, $start_date, $end_date, $id_unit, $id_divisi]
        );

        // Ubah ke collection agar bisa pakai filter
        $detail_jurnal = collect($detail_jurnal);

        // Filter berdasarkan input pencarian
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $detail_jurnal = $detail_jurnal->filter(function ($item) use ($search) {
                return str_contains(strtolower($item->no_bukti), $search)
                    || str_contains(strtolower($item->keterangan), $search)
                    || str_contains(strtolower($item->akun), $search)
                    || str_contains(strtolower($item->unit ?? ''), $search)
                    || str_contains(strtolower($item->divisi ?? ''), $search)
                    || str_contains(strtolower($item->kode_sumbangan ?? ''), $search)
                    || str_contains(strtolower($item->kode_ph ?? ''), $search);
            });
        }

        $total_debit = $detail_jurnal->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $detail_jurnal->where('debit_kredit', 'kredit')->sum('nominal');

        $akunList = Akun::all();

        return view('buku-besar', compact('detail_jurnal', 'akunList', 'total_debit', 'total_kredit'));
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

        $id_user_login = Auth::user()->id_user;
        DB::statement("SET @current_user_id = $id_user_login");
    
        $request->validate([
            'id_jurnal_umum' => 'required|exists:jurnal_umum,id_jurnal_umum',
        ]);
    
        if (Buku_Besar::where('id_jurnal_umum', $request->id_jurnal_umum)->exists()) {
            return redirect()->back()->with('error', 'Jurnal sudah diposting.');
        }
    
        Buku_Besar::create([
            'id_jurnal_umum' => $request->id_jurnal_umum,
        ]);
    
        return redirect()->back()->with('success', 'Berhasil diposting ke Buku Besar.');
    }
    

    public function postingSemua(Request $request)
    {
        $id_user_login = Auth::user()->id_user;
        DB::statement("SET @current_user_id = $id_user_login");

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = Jurnal_Umum::query();

        // Jika ada filter tanggal, tambahkan kondisi whereBetween
        if ($start_date && $end_date) {
            $query->whereBetween('tanggal', [$start_date, $end_date]);
        }

        // Ambil ID jurnal yang belum diposting dan sesuai rentang tanggal
        $jurnalBelumDiposting = $query->whereNotIn('id_jurnal_umum', function ($query) {
            $query->select('id_jurnal_umum')->from('buku_besar')->whereNotNull('id_jurnal_umum');
        })->pluck('id_jurnal_umum');

        foreach ($jurnalBelumDiposting as $id_jurnal) {
            Buku_Besar::create([
                'id_jurnal_umum' => $id_jurnal,
            ]);
        }

        return redirect()->back()->with('success', 'Semua jurnal dalam rentang tanggal berhasil diposting ke Buku Besar.');
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
