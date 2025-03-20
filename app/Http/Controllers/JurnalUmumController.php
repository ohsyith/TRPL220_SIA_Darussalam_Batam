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
        $jenis_transaksi = Jenis_Transaksi::all();
        $unit = Unit::all();
        $divisi = Divisi::all();
        $akun = Akun::all();
        return view('input-transaksi', compact('jenis_transaksi', 'unit', 'divisi', 'akun'));
    }


    
    public function store(Request $request)
    {   
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'id_jenis_transaksi' => 'required|exists:jenis_transaksi,id_jenis_transaksi',
            'id_unit' => 'required|exists:unit,id_unit',
            'id_divisi' => 'required|exists:divisi,id_divisi',
            'id_akun' => 'required|array',
            'id_akun.*' => 'exists:akun,id_akun',
            'debit' => 'required|array',
            'kredit' => 'required|array',
        ]);

        return DB::transaction(function () use ($request) {
            // Format tanggal menjadi YYYYMMDD
            $tanggalFormatted = date('Ymd', strtotime($request->tanggal));

            // Hitung jumlah entri dengan tanggal yang sama
            $count = Jurnal_Umum::whereDate('tanggal', $request->tanggal)->count() + 1;

            // Format urutan menjadi 3 digit, misalnya 003, 012, dll.
            $urutan = str_pad($count, 3, '0', STR_PAD_LEFT);

            // Buat no_bukti
            $no_bukti = "INV-$tanggalFormatted-$urutan";

            // Simpan ke tabel jurnal_umum
            $jurnal = Jurnal_Umum::create([
                'tanggal' => $request->tanggal,
                'no_bukti' => $no_bukti,
                'keterangan' => $request->keterangan,
                'id_jenis_transaksi' => $request->id_jenis_transaksi,
                'id_unit' => $request->id_unit,
                'id_divisi' => $request->id_divisi,
                'kode_sumbangan' => $request->kode_sumbangan ?? '',
                'kode_ph' => $request->kode_ph ?? ''
            ]);

            // Simpan ke tabel detail_jurnal_umum
            foreach ($request->id_akun as $key => $id_akun) {
                $debit = (int) preg_replace('/\D/', '', $request->debit[$key]) ?: 0;
                $kredit = (int) preg_replace('/\D/', '', $request->kredit[$key]) ?: 0;

                if ($debit > 0) {
                    Detail_Jurnal_Umum::create([
                        'id_jurnal_umum' => $jurnal->id_jurnal_umum,
                        'id_akun' => $id_akun,
                        'nominal' => $debit,
                        'debit_kredit' => 'debit'
                    ]);
                }

                if ($kredit > 0) {
                    Detail_Jurnal_Umum::create([
                        'id_jurnal_umum' => $jurnal->id_jurnal_umum,
                        'id_akun' => $id_akun,
                        'nominal' => $kredit,
                        'debit_kredit' => 'kredit'
                    ]);
                }
            }

            // **Jika checkbox "Posting ke Buku Besar" dicentang, insert ke buku_besar**
            if ($request->has('postingBukuBesar')) {
                Buku_Besar::create([
                    'id_jurnal_umum' => $jurnal->id_jurnal_umum,
                ]);
            }

            return response()->json(['message' => 'Data berhasil disimpan', 'no_bukti' => $no_bukti]);
        });
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