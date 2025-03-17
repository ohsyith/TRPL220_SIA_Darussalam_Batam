<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Unit;
use App\Models\Jenis_Transaksi;
use App\Models\Divisi;
use App\Models\Jurnal_Umum;
use Illuminate\Http\Request;
use App\Models\Detail_Jurnal_Umum;

class JurnalUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoriakun = Kategori_Akun::all();
        return view('input-transaksi', compact('kategoriakun'));
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'id_akun' => 'required|array',
            'id_akun.*' => 'exists:akun,id_akun',
            'debit' => 'required|array',
            'kredit' => 'required|array',
        ]);

        // Debugging untuk melihat apakah request dikirim lebih dari sekali
        // dd($request->all()); // ← Uncomment ini untuk cek data request

        // Format tanggal menjadi YYYYMMDD
        $tanggalFormatted = date('Ymd', strtotime($request->tanggal));

        // Hitung jumlah entri dengan tanggal yang sama
        $count = Jurnal_Umum::whereDate('tanggal', $request->tanggal)->count() + 1;

        // Format urutan menjadi 3 digit, misalnya 003, 012, dll.
        $urutan = str_pad($count, 3, '0', STR_PAD_LEFT);

        // Buat no_bukti
        $no_bukti = "INV-$tanggalFormatted-$urutan";

        // Cek apakah jurnal dengan tanggal dan no_bukti yang sama sudah ada
        $jurnal = Jurnal_Umum::firstOrCreate(
            ['tanggal' => $request->tanggal, 'no_bukti' => $no_bukti], // Cek jika sudah ada
            ['keterangan' => $request->keterangan] // Jika tidak ada, buat baru
        );

        // Simpan ke tabel detail_jurnal_umum
        foreach ($request->id_akun as $key => $id_akun) {
            $debit = (int) preg_replace('/\D/', '', $request->debit[$key]) ?: 0;
            $kredit = (int) preg_replace('/\D/', '', $request->kredit[$key]) ?: 0;

            if ($debit > 0) {
                Detail_Jurnal_Umum::create([
                    'id_jurnal_umum' => $jurnal->id_jurnal_umum,
                    'id_akun' => $id_akun,
                    'nominal' => $debit,
                    'jenis' => 'debit'
                ]);
            }

            if ($kredit > 0) {
                Detail_Jurnal_Umum::create([
                    'id_jurnal_umum' => $jurnal->id_jurnal_umum,
                    'id_akun' => $id_akun,
                    'nominal' => $kredit,
                    'jenis' => 'kredit'
                ]);
            }
        }

        return response()->json(['message' => 'Data berhasil disimpan', 'no_bukti' => $no_bukti]);
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