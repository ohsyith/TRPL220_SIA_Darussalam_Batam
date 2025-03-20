<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Kategori_Akun;
use Illuminate\Http\Request;
use App\Models\Detail_Jurnal_Umum;
use Illuminate\Support\Facades\DB;

class NeracaSaldoController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua akun dari database (agar semua akun muncul)
        $semua_akun = Akun::with(['sub_kategori_akun.kategori_akun'])->get();

        // Ambil ID jurnal umum yang sudah diposting ke buku besar
        $postedJurnalIds = DB::table('buku_besar')->pluck('id_jurnal_umum')->toArray();

        // Ambil saldo transaksi berdasarkan filter tanggal jika ada
        $query = Detail_Jurnal_Umum::whereIn('id_jurnal_umum', $postedJurnalIds)
            ->selectRaw('id_akun, 
                         SUM(CASE WHEN debit_kredit = "debit" THEN nominal ELSE 0 END) as total_debit, 
                         SUM(CASE WHEN debit_kredit = "kredit" THEN nominal ELSE 0 END) as total_kredit')
            ->groupBy('id_akun');

        // Jika ada filter tanggal, tambahkan kondisi ke query
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('jurnal_umum', function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            });
        }

        // Ambil hasil query dan ubah menjadi associative array agar mudah diakses di Blade
        $saldo_akun = $query->get()->keyBy('id_akun');

        return view('neraca-saldo', compact('semua_akun', 'saldo_akun'));
    }

}





