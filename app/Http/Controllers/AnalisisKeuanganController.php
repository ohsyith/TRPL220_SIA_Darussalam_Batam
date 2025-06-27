<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Unit;
use App\Models\Divisi;
use Illuminate\Http\Request;

class AnalisisKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $start = "$tahun-01-01";
        $end = "$tahun-12-31";

        $tahun_lalu = $tahun - 1;
        $start_lalu = "$tahun_lalu-01-01";
        $end_lalu = "$tahun_lalu-12-31";

        $id_unit = $request->input('unit');
        $id_divisi = $request->input('divisi');

        $akunList = Akun::with(['sub_kategori_akun.kategori_akun', 'detail_jurnal_umum.jurnal_umum'])->get();

        $data = [];
        $data_lalu = [];

        foreach ($akunList as $akun) {
            $kategori = strtoupper($akun->sub_kategori_akun->kategori_akun->kategori_akun ?? '');
            $sub_kategori = strtoupper($akun->sub_kategori_akun->sub_kategori_akun ?? '');

            $saldo_awal = ($akun->saldo_awal_debit ?? 0) - ($akun->saldo_awal_kredit ?? 0);
            $mutasi = 0;
            $mutasi_lalu = 0;

            foreach ($akun->detail_jurnal_umum as $detail) {
                $jurnal = $detail->jurnal_umum;
                if (!$jurnal) continue;

                if ($id_unit && $jurnal->id_unit != $id_unit) continue;
                if ($id_divisi && $jurnal->id_divisi != $id_divisi) continue;

                $tanggal = $jurnal->tanggal ?? null;

                if ($tanggal >= $start && $tanggal <= $end) {
                    $mutasi += $detail->debit_kredit === 'debit' ? $detail->nominal : -$detail->nominal;
                }

                if ($tanggal >= $start_lalu && $tanggal <= $end_lalu) {
                    $mutasi_lalu += $detail->debit_kredit === 'debit' ? $detail->nominal : -$detail->nominal;
                }
            }

            $saldo_akhir = $saldo_awal + $mutasi;
            $saldo_akhir_lalu = $saldo_awal + $mutasi_lalu;

            $data[] = [
                'kategori' => $kategori,
                'sub_kategori' => $sub_kategori,
                'saldo' => $saldo_akhir,
            ];

            $data_lalu[] = [
                'kategori' => $kategori,
                'sub_kategori' => $sub_kategori,
                'saldo' => $saldo_akhir_lalu,
            ];
        }

        $sum = fn($kategori, $list) => collect($list)->where('kategori', $kategori)->sum('saldo');
        $sumSub = fn($sub, $list) => collect($list)->where('sub_kategori', $sub)->sum('saldo');

        $total_aset = $sum('ASET', $data);
        $total_aset_lalu = $sum('ASET', $data_lalu);

        $aset_lancar = $sumSub('ASET LANCAR', $data);
        $persediaan = $sumSub('PERSEDIAAN', $data);
        $kewajiban_lancar = $sumSub('KEWAJIBAN LANCAR', $data);
        $kewajiban_panjang = $sumSub('KEWAJIBAN JANGKA PANJANG', $data);
        $total_kewajiban = $sum('KEWAJIBAN', $data);
        $modal = $sum('MODAL', $data);
        $pendapatan = $sum('PENERIMAAN DAN SUMBANGAN', $data);
        $beban = $sum('BEBAN', $data);

        $laba_bersih = $pendapatan - $beban;

        $rata_rata_aset = ($total_aset + $total_aset_lalu) / 2;

        $roi = $pendapatan > 0 ? $laba_bersih / $pendapatan : 0;
        $roa = $total_aset > 0 ? $laba_bersih / $total_aset : 0;
        $rasio_lancar = $kewajiban_lancar > 0 ? $aset_lancar / $kewajiban_lancar : 0;
        $quick_ratio = $kewajiban_lancar > 0 ? ($aset_lancar - $persediaan) / $kewajiban_lancar : 0;
        $dar = $total_aset > 0 ? $total_kewajiban / $total_aset : 0;
        $der = $modal > 0 ? $total_kewajiban / $modal : 0;
        $atr = $rata_rata_aset > 0 ? $pendapatan / $rata_rata_aset : 0;

        $units = Unit::all();
        $divisis = Divisi::all();

        $chart_labels = ['ROI', 'ROA', 'Rasio Lancar', 'Quick Ratio', 'DAR', 'DER', 'ATR'];
        $chart_values = [
            round($roi * 100, 2),
            round($roa * 100, 2),
            round($rasio_lancar, 2),
            round($quick_ratio, 2),
            round($dar * 100, 2),
            round($der, 2),
            round($atr, 2),
        ];


        return view('analisis-keuangan', compact(
            'tahun', 'id_unit', 'id_divisi', 'units', 'divisis',
            'total_aset', 'aset_lancar', 'persediaan',
            'kewajiban_lancar', 'kewajiban_panjang', 'total_kewajiban',
            'modal', 'pendapatan', 'beban', 'laba_bersih',
            'roi', 'roa', 'rasio_lancar', 'quick_ratio', 'dar', 'der', 'atr',
            'chart_labels', 'chart_values'
        ));

    }
}
