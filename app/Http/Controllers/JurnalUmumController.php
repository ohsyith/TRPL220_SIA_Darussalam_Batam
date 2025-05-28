<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Akuntan_Unit;
use App\Models\Unit;
use App\Models\Akuntan_Divisi;
use App\Models\Divisi;
use App\Models\Buku_Besar;
use App\Models\Jurnal_Umum;
use Illuminate\Http\Request;
use App\Models\Detail_Jurnal_Umum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JurnalUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $query = Detail_Jurnal_Umum::with(['jurnal_umum', 'akun']);

    //     // Filter berdasarkan rentang tanggal
    //     if ($request->filled('start_date') && $request->filled('end_date')) {
    //         $query->whereHas('jurnal_umum', function ($q) use ($request) {
    //             $q->whereBetween('tanggal', [$request->start_date, $request->end_date]);
    //         });
    //     }

    //     // Pencarian teks bebas
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->whereHas('jurnal_umum', function ($q) use ($search) {
    //             $q->where('no_bukti', 'like', "%$search%")
    //                 ->orWhere('keterangan', 'like', "%$search%");
    //         })->orWhereHas('akun', function ($q) use ($search) {
    //             $q->where('akun', 'like', "%$search%");
    //         });
    //     }

    //     $detailjurnalumum = $query->get(); // Ambil data jurnal umum
        
    //     // Ambil ID jurnal umum yang sudah di-posting ke buku besar
    //     $postedJurnalIds = DB::table('buku_besar')
    //     ->whereNotNull('id_jurnal_umum')
    //     ->pluck('id_jurnal_umum')
    //     ->toArray();

    //         // dd($postedJurnalIds); // Uncomment baris ini untuk debugging


    //     return view('jurnal-umum', compact('detailjurnalumum', 'postedJurnalIds'));
    // }


    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil ID unit/divisi berdasarkan role
        $id_unit = null;
        $id_divisi = null;

        if ($user->role === 'akuntan_unit') {
            $id_unit = Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
        } elseif ($user->role === 'akuntan_divisi') {
            $id_divisi = Akuntan_Divisi::where('id_akuntan_divisi', $user->id_user)->value('id_divisi');
        }

        // Query detail jurnal umum
        $query = Detail_Jurnal_Umum::with(['jurnal_umum.unit', 'jurnal_umum.divisi', 'akun']);

        // Filter berdasarkan rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('jurnal_umum', function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            });
        }

        // Pencarian teks bebas
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('jurnal_umum', function ($sub) use ($search) {
                    $sub->where('no_bukti', 'like', "%$search%")
                        ->orWhere('keterangan', 'like', "%$search%");
                })->orWhereHas('akun', function ($sub) use ($search) {
                    $sub->where('akun', 'like', "%$search%");
                });
            });
        }

        // 🔐 Filter berdasarkan unit/divisi sesuai role
        if ($id_unit !== null) {
            $query->whereHas('jurnal_umum', function ($q) use ($id_unit) {
                $q->where('id_unit', $id_unit);
            });
        }

        if ($id_divisi !== null) {
            $query->whereHas('jurnal_umum', function ($q) use ($id_divisi) {
                $q->where('id_divisi', $id_divisi);
            });
        }

        $detailjurnalumum = $query
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->orderByDesc('jurnal_umum.no_bukti')
            ->select('detail_jurnal_umum.*') // Ambil hanya kolom detail_jurnal_umum agar relasi tetap jalan
            ->get();

        // Ambil ID jurnal umum yang sudah di-posting ke buku besar
        $postedJurnalIds = DB::table('buku_besar')
            ->whereNotNull('id_jurnal_umum')
            ->pluck('id_jurnal_umum')
            ->toArray();

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


    
    public function store(Request $request)
    {   
        $id_user_login = Auth::user()->id_user;
        DB::statement("SET @current_user_id = $id_user_login");

        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'jenis_transaksi' => 'required|string',
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
                'jenis_transaksi' => $request->jenis_transaksi,
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

            // Redirect dengan pesan sukses dan no_bukti
            return redirect()->route('jurnal-umum.index')->with('success', 'Data berhasil disimpan. No Bukti: ' . $no_bukti);
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
    public function edit($id)
    {
        $jurnalUmum = Jurnal_Umum::with('detail_jurnal_umum')->findOrFail($id);
        $akun = Akun::all();
        $unit = Unit::all();
        $divisi = Divisi::all();

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

        return view('jurnal-umum-edit', compact('jurnalUmum', 'akun', 'unit', 'divisi', 'id_unit', 'id_divisi'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $id_user_login = Auth::user()->id_user;
        DB::statement("SET @current_user_id = $id_user_login");
    
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'jenis_transaksi' => 'required|string',
            'id_unit' => 'required|exists:unit,id_unit',
            'id_divisi' => 'required|exists:divisi,id_divisi',
            'id_akun' => 'required|array',
            'id_akun.*' => 'exists:akun,id_akun',
            'debit' => 'required|array',
            'kredit' => 'required|array',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $jurnal = Jurnal_Umum::findOrFail($id);

            $jurnal->update([
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'jenis_transaksi' => $request->jenis_transaksi,
                'id_unit' => $request->id_unit,
                'id_divisi' => $request->id_divisi,
                'kode_sumbangan' => $request->kode_sumbangan ?? '',
                'kode_ph' => $request->kode_ph ?? ''
            ]);

            // Hapus detail lama
            Detail_Jurnal_Umum::where('id_jurnal_umum', $jurnal->id_jurnal_umum)->delete();

            // Tambah ulang detail baru
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

            // Optional: atur ulang buku besar kalau ada
            Buku_Besar::where('id_jurnal_umum', $jurnal->id_jurnal_umum)->delete();

            if ($request->has('postingBukuBesar')) {
                Buku_Besar::create([
                    'id_jurnal_umum' => $jurnal->id_jurnal_umum,
                ]);
            }

            return redirect()->route('jurnal-umum.index')->with('success', 'Data berhasil diperbarui');
        });
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id_user_login = Auth::user()->id_user;
        DB::statement("SET @current_user_id = $id_user_login");

        $jurnal = Jurnal_Umum::findOrFail($id);

        // Hapus data terkait di buku besar yang memiliki id_jurnal_umum
        Buku_Besar::where('id_jurnal_umum', $id)->delete();

        // Hapus detail jurnal yang terkait dengan jurnal umum
        Detail_Jurnal_Umum::where('id_jurnal_umum', $id)->delete();

        // Hapus jurnal umum itu sendiri
        $jurnal->delete();

        // Redirect kembali ke halaman daftar jurnal umum dengan pesan sukses
        return redirect()->route('jurnal-umum.index')->with('success', 'Data berhasil dihapus');
    }




}