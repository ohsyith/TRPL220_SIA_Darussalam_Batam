<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArusKasController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        // Kas Awal dari akun kategori AKTIVA dan subkategori Kas/Bank
        $kasAwal = DB::table('detail_jurnal_umum')
        ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
        ->join('akun', 'detail_jurnal_umum.id_akun', '=', 'akun.id_akun')
        ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
        ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
        ->whereYear('jurnal_umum.tanggal', '<', $tahun)
        ->where('kategori_akun.kategori_akun', 'AKTIVA')
        ->whereIn('sub_kategori_akun.sub_kategori_akun', ['Kas', 'Bank'])
        ->selectRaw("SUM(CASE WHEN detail_jurnal_umum.debit_kredit = 'debit' THEN nominal ELSE -nominal END) AS saldo_awal")
        ->value('saldo_awal') ?? 0;


        // Kas dari Aktivitas Operasi
        $kasOperasi = $this->getTotalKas($tahun, ['PENERIMAAN DAN SUMBANGAN', 'BEBAN'], [
            'Penerimaan dan Sumbangan Pendidikan',
            'Penerimaan dan Sumbangan Non Pendidikan',
            'Beban Operasional',
            'Beban Non Operasional'
        ]);

        // Kas dari Aktivitas Investasi
        $kasInvestasi = $this->getTotalKas($tahun, ['AKTIVA'], ['Aktiva Tetap']);

        // Kas dari Aktivitas Pendanaan
        $kasPendanaan = $this->getTotalKas($tahun, ['KEWAJIBAN'], ['Kewajiban Jangka Panjang']);

        // Perhitungan akhir
        $kenaikanKas = $kasOperasi + $kasInvestasi + $kasPendanaan;
        $kasAkhir = $kasAwal + $kenaikanKas;

        return view('arus-kas', compact(
            'tahun',
            'kasAwal',
            'kasOperasi',
            'kasInvestasi',
            'kasPendanaan',
            'kenaikanKas',
            'kasAkhir'
        ));
    }

    private function getTotalKas($tahun, array $kategoriAkun, array $subKategoriAkun)
    {
        return DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->join('akun', 'detail_jurnal_umum.id_akun', '=', 'akun.id_akun')
            ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
            ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
            ->whereYear('jurnal_umum.tanggal', $tahun)
            ->whereIn('kategori_akun.kategori_akun', $kategoriAkun)
            ->whereIn('sub_kategori_akun.sub_kategori_akun', $subKategoriAkun)
            ->selectRaw("SUM(CASE WHEN detail_jurnal_umum.debit_kredit = 'debit' THEN nominal ELSE -nominal END) AS total")
            ->value('total') ?? 0;
    }



}
