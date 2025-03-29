<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Detail_Jurnal_Umum;
use Illuminate\Support\Facades\DB;

class LaporanKomprehensifController extends Controller
{
    public function index(Request $request)
    {
        // Ambil input tanggal dari request
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');

        // Query dasar dengan join tabel terkait
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->join('akun', 'detail_jurnal_umum.id_akun', '=', 'akun.id_akun')
            ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
            ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
            ->whereIn('kategori_akun.kategori_akun', ['Pendapatan', 'Beban']) 
            ->where('jurnal_umum.jenis_transaksi', 'tidak terikat');

        // Filter berdasarkan tanggal jika ada
        if ($tanggal_mulai && $tanggal_selesai) {
            $query->whereBetween('jurnal_umum.tanggal', [$tanggal_mulai, $tanggal_selesai]);
        }

        // Ambil data sebelum `get()`
        $pendapatan = (clone $query)
            ->where('detail_jurnal_umum.debit_kredit', 'kredit')
            ->select('akun.akun AS nama_akun', DB::raw('SUM(detail_jurnal_umum.nominal) as total'))
            ->groupBy('akun.akun')
            ->get();
        $total_pendapatan = $pendapatan->sum('total');

        $beban = (clone $query)
            ->where('detail_jurnal_umum.debit_kredit', 'debit')
            ->select('akun.akun AS nama_akun', DB::raw('SUM(detail_jurnal_umum.nominal) as total'))
            ->groupBy('akun.akun')
            ->get();
        $total_beban = $beban->sum('total');

        // Surplus/Defisit Tanpa Pembatasan
        $surplus_defisit = $total_pendapatan - $total_beban;

        // Pendapatan & Beban Dengan Pembatasan (jenis_transaksi = terikat)
        $query_terikat = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->join('akun', 'detail_jurnal_umum.id_akun', '=', 'akun.id_akun')
            ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
            ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
            ->whereIn('kategori_akun.kategori_akun', ['Pendapatan', 'Beban']) 
            ->where('jurnal_umum.jenis_transaksi', 'terikat');

        if ($tanggal_mulai && $tanggal_selesai) {
            $query_terikat->whereBetween('jurnal_umum.tanggal', [$tanggal_mulai, $tanggal_selesai]);
        }

        $pendapatan_terikat = (clone $query_terikat)
            ->where('detail_jurnal_umum.debit_kredit', 'kredit')
            ->select('akun.akun AS nama_akun', DB::raw('SUM(detail_jurnal_umum.nominal) as total'))
            ->groupBy('akun.akun')
            ->get();
        $total_pendapatan_terikat = $pendapatan_terikat->sum('total');

        $beban_terikat = (clone $query_terikat)
            ->where('detail_jurnal_umum.debit_kredit', 'debit')
            ->select('akun.akun AS nama_akun', DB::raw('SUM(detail_jurnal_umum.nominal) as total'))
            ->groupBy('akun.akun')
            ->get();
        $total_beban_terikat = $beban_terikat->sum('total');

        // Surplus/Defisit Dengan Pembatasan
        $surplus_defisit_terikat = $total_pendapatan_terikat - $total_beban_terikat;

        // Penghasilan Komprehensif Lain (jika ada logika tambahan)
        $penghasilan_komprehensif_lain = 0; 

        // Total Penghasilan Komprehensif
        $total_penghasilan_komprehensif = $surplus_defisit + $surplus_defisit_terikat + $penghasilan_komprehensif_lain;

        return view('laporan-komprehensif', compact(
            'pendapatan', 'beban', 'total_pendapatan', 'total_beban', 'surplus_defisit',
            'pendapatan_terikat', 'total_pendapatan_terikat', 'beban_terikat', 'total_beban_terikat', 'surplus_defisit_terikat',
            'penghasilan_komprehensif_lain', 'total_penghasilan_komprehensif', 'tanggal_mulai', 'tanggal_selesai'
        ));
    }






}
