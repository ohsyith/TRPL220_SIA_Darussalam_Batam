<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Akun;
use App\Models\Unit;
use App\Models\Akuntan_Unit;
use App\Models\Divisi;
use Illuminate\Http\Request;
use App\Models\Kategori_Akun;
use App\Models\Detail_Jurnal_Umum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NeracaSaldoController extends Controller
{


    //RAW
    // public function index(Request $request)
    // {
    //     $user = Auth::user();

    //     $id_unit = $request->unit;
    //     $id_divisi = $request->divisi;

    //     // Otomatis set unit/divisi dari role jika tidak dipilih
    //     if (!$id_unit && $user->role === 'akuntan_unit') {
    //         $id_unit = \App\Models\Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
    //     }
    //     if (!$id_divisi && $user->role === 'akuntan_divisi') {
    //         $id_divisi = \App\Models\Akuntan_Divisi::where('id_akuntan_divisi', $user->id_user)->value('id_divisi');
    //     }

    //     $start = $request->start_date ?? now()->startOfYear()->toDateString();
    //     $end = $request->end_date ?? now()->toDateString();
    //     $tahun_lalu = Carbon::parse($end)->year - 1;

    //     // ========== LOGIKA KHUSUS UNTUK ASET NETO DENGAN/TANPA PEMBATASAN ==========
    //     // Ambil akun ASET NETO
    //     $akunDengan = DB::table('akun')
    //         ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
    //         ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
    //         ->where('kategori_akun.kategori_akun', 'ASET NETO')
    //         ->where('sub_kategori_akun.sub_kategori_akun', 'Dengan Pembatasan')
    //         ->select('akun.*')
    //         ->first();

    //     $akunTanpa = DB::table('akun')
    //         ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
    //         ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
    //         ->where('kategori_akun.kategori_akun', 'ASET NETO')
    //         ->where('sub_kategori_akun.sub_kategori_akun', 'Tanpa Pembatasan')
    //         ->select('akun.*')
    //         ->first();

    //     $getKenaikan = function ($id_akun, $start, $end) use ($id_unit, $id_divisi) {
    //         $query = DB::table('detail_jurnal_umum')
    //             ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
    //             ->whereIn('jurnal_umum.id_jurnal_umum', DB::table('buku_besar')->pluck('id_jurnal_umum')->toArray()) // Hanya jurnal yang sudah diposting
    //             ->where('detail_jurnal_umum.id_akun', $id_akun)
    //             ->whereBetween('jurnal_umum.tanggal', [$start, $end]);

    //         if ($id_unit) $query->where('jurnal_umum.id_unit', $id_unit);
    //         if ($id_divisi) $query->where('jurnal_umum.id_divisi', $id_divisi);

    //         return $query->select(
    //             DB::raw("SUM(CASE WHEN debit_kredit = 'debit' THEN nominal ELSE 0 END) as total_debit"),
    //             DB::raw("SUM(CASE WHEN debit_kredit = 'kredit' THEN nominal ELSE 0 END) as total_kredit")
    //         )->first();
    //     };

    //     $data_aset_neto = [
    //         'dengan_pembatasan' => [
    //             'saldo_awal' => $akunDengan ? $akunDengan->saldo_awal_kredit - $akunDengan->saldo_awal_debit : 0,
    //             'kenaikan_periode_lalu' => 0,
    //             'kenaikan_periode_berjalan' => 0,
    //             'saldo_akhir' => 0,
    //         ],
    //         'tanpa_pembatasan' => [
    //             'saldo_awal' => $akunTanpa ? $akunTanpa->saldo_awal_kredit - $akunTanpa->saldo_awal_debit : 0,
    //             'kenaikan_periode_lalu' => 0,
    //             'kenaikan_periode_berjalan' => 0,
    //             'saldo_akhir' => 0,
    //         ],
    //     ];

    //     // Hitung kenaikan periode lalu
    //     if ($akunDengan) {
    //         $lalu = $getKenaikan($akunDengan->id_akun, '1900-01-01', date('Y-m-d', strtotime($start . ' -1 day')));
    //         $data_aset_neto['dengan_pembatasan']['kenaikan_periode_lalu'] = ($lalu->total_kredit ?? 0) - ($lalu->total_debit ?? 0);
    //     }

    //     if ($akunTanpa) {
    //         $lalu = $getKenaikan($akunTanpa->id_akun, '1900-01-01', date('Y-m-d', strtotime($start . ' -1 day')));
    //         $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_lalu'] = ($lalu->total_kredit ?? 0) - ($lalu->total_debit ?? 0);
    //     }

    //     // Hitung kenaikan periode berjalan
    //     $getTotalManual = function ($isPendapatan, $jenis_transaksi, $start, $end) use ($id_unit, $id_divisi) {
    //         $kategori = $isPendapatan ? 'PENERIMAAN DAN SUMBANGAN' : 'BEBAN';
    //         $debit_kredit = $isPendapatan ? 'kredit' : 'debit';

    //         return DB::table('detail_jurnal_umum as dju')
    //             ->join('jurnal_umum as ju', 'dju.id_jurnal_umum', '=', 'ju.id_jurnal_umum')
    //             ->join('akun as a', 'dju.id_akun', '=', 'a.id_akun')
    //             ->join('sub_kategori_akun as ska', 'a.id_sub_kategori_akun', '=', 'ska.id_sub_kategori_akun')
    //             ->join('kategori_akun as ka', 'ska.id_kategori_akun', '=', 'ka.id_kategori_akun')
    //             ->whereIn('ju.id_jurnal_umum', DB::table('buku_besar')->pluck('id_jurnal_umum')->toArray()) // Hanya jurnal yang sudah diposting
    //             ->where('ka.kategori_akun', $kategori)
    //             ->where('ju.jenis_transaksi', $jenis_transaksi)
    //             ->whereBetween('ju.tanggal', [$start, $end])
    //             ->where('dju.debit_kredit', $debit_kredit)
    //             ->when($id_unit, fn($q) => $q->where('ju.id_unit', $id_unit))
    //             ->when($id_divisi, fn($q) => $q->where('ju.id_divisi', $id_divisi))
    //             ->sum('dju.nominal');
    //     };

    //     $pendapatan_terikat = $getTotalManual(true, 'Terikat', $start, $end);
    //     $beban_terikat = $getTotalManual(false, 'Terikat', $start, $end);
    //     $pendapatan_tidak_terikat = $getTotalManual(true, 'Tidak Terikat', $start, $end);
    //     $beban_tidak_terikat = $getTotalManual(false, 'Tidak Terikat', $start, $end);

    //     $saldoAwalPendapatan = DB::table('akun')
    //         ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
    //         ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
    //         ->where('kategori_akun.kategori_akun', 'PENERIMAAN DAN SUMBANGAN')
    //         ->sum('akun.saldo_awal_kredit');

    //     $saldoAwalBeban = DB::table('akun')
    //         ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
    //         ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
    //         ->where('kategori_akun.kategori_akun', 'BEBAN')
    //         ->sum('akun.saldo_awal_debit');

    //     $total_raw = $pendapatan_terikat + $pendapatan_tidak_terikat;
    //     $kenaikan_terikat = $pendapatan_terikat - $beban_terikat;
    //     $kenaikan_tidak_terikat = $pendapatan_tidak_terikat - $beban_tidak_terikat;

    //     if ($total_raw > 0) {
    //         $proporsi_terikat = $pendapatan_terikat / $total_raw;
    //         $proporsi_tidak_terikat = $pendapatan_tidak_terikat / $total_raw;

    //         $kenaikan_terikat += $saldoAwalPendapatan * $proporsi_terikat - $saldoAwalBeban * $proporsi_terikat;
    //         $kenaikan_tidak_terikat += $saldoAwalPendapatan * $proporsi_tidak_terikat - $saldoAwalBeban * $proporsi_tidak_terikat;
    //     }

    //     $data_aset_neto['dengan_pembatasan']['kenaikan_periode_berjalan'] = $kenaikan_terikat;
    //     $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_berjalan'] = $kenaikan_tidak_terikat;

    //     $data_aset_neto['dengan_pembatasan']['saldo_akhir'] =
    //         $data_aset_neto['dengan_pembatasan']['saldo_awal'] +
    //         $data_aset_neto['dengan_pembatasan']['kenaikan_periode_lalu'] +
    //         $data_aset_neto['dengan_pembatasan']['kenaikan_periode_berjalan'];

    //     $data_aset_neto['tanpa_pembatasan']['saldo_akhir'] =
    //         $data_aset_neto['tanpa_pembatasan']['saldo_awal'] +
    //         $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_lalu'] +
    //         $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_berjalan'];

    //     // ========== LOGIKA UMUM UNTUK AKUN LAINNYA ==========
    //     // Hanya ambil akun kategori 1, 2, 3
    //     $semua_akun = Akun::with(['sub_kategori_akun.kategori_akun', 'detail_jurnal_umum.jurnal_umum'])
    //         ->whereHas('sub_kategori_akun.kategori_akun', function ($query) {
    //             $query->whereIn('kategori_akun', ['AKTIVA', 'KEWAJIBAN', 'ASET NETO']);
    //         })
    //         ->get();

    //     $saldo_akun = collect();
    //     $totalKewajibanAsetNeto = 0;
    //     $totalPeriodeLaluKewajibanAsetNeto = 0;

    //     $postedJurnalIds = DB::table('buku_besar')->pluck('id_jurnal_umum')->toArray();

    //     foreach ($semua_akun as $akun) {
    //         $saldo_awal_debit = $akun->saldo_awal_debit ?? 0;
    //         $saldo_awal_kredit = $akun->saldo_awal_kredit ?? 0;

    //         $mutasi_debit = 0;
    //         $mutasi_kredit = 0;
    //         $debit_lalu = 0;
    //         $kredit_lalu = 0;

    //         foreach ($akun->detail_jurnal_umum as $detail) {
    //             $jurnal = $detail->jurnal_umum;
    //             if (!$jurnal) continue;

    //             // ✅ Hanya proses jurnal yang sudah diposting ke buku besar
    //             if (!in_array($jurnal->id_jurnal_umum, $postedJurnalIds)) continue;

    //             if ($id_unit && $jurnal->id_unit != $id_unit) continue;
    //             if ($id_divisi && $jurnal->id_divisi != $id_divisi) continue;

    //             $tanggal = $jurnal->tanggal ?? null;
    //             if (!$tanggal) continue;

    //             $tahun_transaksi = Carbon::parse($tanggal)->year;

    //             if ($tanggal >= $start && $tanggal <= $end) {
    //                 if ($detail->debit_kredit === 'debit') {
    //                     $mutasi_debit += $detail->nominal;
    //                 } elseif ($detail->debit_kredit === 'kredit') {
    //                     $mutasi_kredit += $detail->nominal;
    //                 }
    //             }

    //             if ($tahun_transaksi == $tahun_lalu) {
    //                 if ($detail->debit_kredit === 'debit') {
    //                     $debit_lalu += $detail->nominal;
    //                 } elseif ($detail->debit_kredit === 'kredit') {
    //                     $kredit_lalu += $detail->nominal;
    //                 }
    //             }
    //         }

    //         $kategori = $akun->sub_kategori_akun->kategori_akun->kategori_akun;
    //         $sub_kategori = $akun->sub_kategori_akun->sub_kategori_akun;

    //         // ✅ KHUSUS: Untuk akun "Dengan Pembatasan" dan "Tanpa Pembatasan", gunakan perhitungan khusus
    //         if ($kategori === 'ASET NETO' && $sub_kategori === 'Dengan Pembatasan') {
    //             $saldo = $data_aset_neto['dengan_pembatasan']['saldo_akhir'];
    //         } elseif ($kategori === 'ASET NETO' && $sub_kategori === 'Tanpa Pembatasan') {
    //             $saldo = $data_aset_neto['tanpa_pembatasan']['saldo_akhir'];
    //         } else {
    //             // Perhitungan normal untuk akun lainnya
    //             if (in_array($kategori, ['AKTIVA'])) {
    //                 $saldo = ($saldo_awal_debit + $mutasi_debit) - ($saldo_awal_kredit + $mutasi_kredit);
    //             } else {
    //                 // KEWAJIBAN dan ASET NETO lainnya
    //                 $saldo = ($saldo_awal_kredit + $mutasi_kredit) - ($saldo_awal_debit + $mutasi_debit);
    //             }
    //         }

    //         if ($kategori === 'ASET NETO' && $sub_kategori === 'Dengan Pembatasan') {
    //             $periode_lalu = $data_aset_neto['dengan_pembatasan']['saldo_awal'];
    //         } elseif ($kategori === 'ASET NETO' && $sub_kategori === 'Tanpa Pembatasan') {
    //             $periode_lalu = $data_aset_neto['tanpa_pembatasan']['saldo_awal'];
    //         } else {
    //             if ($kategori === 'AKTIVA') {
    //                 $periode_lalu = $saldo_awal_debit - $saldo_awal_kredit;
    //             } else {
    //                 $periode_lalu = $saldo_awal_kredit - $saldo_awal_debit;
    //             }
    //         }

    //         $saldo_akun[$akun->id_akun] = (object)[
    //             'saldo' => $saldo,
    //             'periode_lalu' => $periode_lalu,
    //         ];

    //         // Hitung total hanya jika KEWAJIBAN dan ASET NETO
    //         if (in_array($kategori, ['KEWAJIBAN', 'ASET NETO'])) {
    //             $totalKewajibanAsetNeto += $saldo;
    //             $totalPeriodeLaluKewajibanAsetNeto += $periode_lalu;
    //         }
    //     }

    //     if ($request->has('export_excel')) {
    //         return $this->exportExcel($semua_akun, $saldo_akun, $start, $end);
    //     }

    //     $units = Unit::all();
    //     $divisis = Divisi::all();

    //     return view('neraca-saldo', compact(
    //         'semua_akun', 'saldo_akun', 'units', 'divisis',
    //         'id_unit', 'id_divisi',
    //         'totalKewajibanAsetNeto', 'totalPeriodeLaluKewajibanAsetNeto'
    //     ));
    // }




    //ORM
    // public function index(Request $request)
    // {
    //     $user = Auth::user();

    //     $id_unit = $request->unit;
    //     $id_divisi = $request->divisi;

    //     // Otomatis set unit/divisi dari role jika tidak dipilih
    //     if (!$id_unit && $user->role === 'akuntan_unit') {
    //         $id_unit = AkuntanUnit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
    //     }
    //     if (!$id_divisi && $user->role === 'akuntan_divisi') {
    //         $id_divisi = AkuntanDivisi::where('id_akuntan_divisi', $user->id_user)->value('id_divisi');
    //     }

    //     $start = $request->start_date ?? now()->startOfYear()->toDateString();
    //     $end = $request->end_date ?? now()->toDateString();
    //     $tahun_lalu = Carbon::parse($end)->year - 1;

    //     // ========== LOGIKA KHUSUS UNTUK ASET NETO DENGAN/TANPA PEMBATASAN ==========
    //     $akunDengan = Akun::whereHas('sub_kategori_akun', function ($query) {
    //         $query->where('sub_kategori_akun', 'Dengan Pembatasan')
    //             ->whereHas('kategori_akun', function ($q) {
    //                 $q->where('kategori_akun', 'ASET NETO');
    //             });
    //     })->first();

    //     $akunTanpa = Akun::whereHas('sub_kategori_akun', function ($query) {
    //         $query->where('sub_kategori_akun', 'Tanpa Pembatasan')
    //             ->whereHas('kategori_akun', function ($q) {
    //                 $q->where('kategori_akun', 'ASET NETO');
    //             });
    //     })->first();

    //     $getKenaikan = function ($id_akun, $start, $end) use ($id_unit, $id_divisi) {
    //         $query = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($start, $end, $id_unit, $id_divisi) {
    //             $q->whereHas('buku_besar') // Hanya jurnal yang sudah diposting
    //             ->whereBetween('tanggal', [$start, $end]);
                
    //             if ($id_unit) $q->where('id_unit', $id_unit);
    //             if ($id_divisi) $q->where('id_divisi', $id_divisi);
    //         })->where('id_akun', $id_akun);

    //         return $query->selectRaw("
    //             SUM(CASE WHEN debit_kredit = 'debit' THEN nominal ELSE 0 END) as total_debit,
    //             SUM(CASE WHEN debit_kredit = 'kredit' THEN nominal ELSE 0 END) as total_kredit
    //         ")->first();
    //     };

    //     $data_aset_neto = [
    //         'dengan_pembatasan' => [
    //             'saldo_awal' => $akunDengan ? $akunDengan->saldo_awal_kredit - $akunDengan->saldo_awal_debit : 0,
    //             'kenaikan_periode_lalu' => 0,
    //             'kenaikan_periode_berjalan' => 0,
    //             'saldo_akhir' => 0,
    //         ],
    //         'tanpa_pembatasan' => [
    //             'saldo_awal' => $akunTanpa ? $akunTanpa->saldo_awal_kredit - $akunTanpa->saldo_awal_debit : 0,
    //             'kenaikan_periode_lalu' => 0,
    //             'kenaikan_periode_berjalan' => 0,
    //             'saldo_akhir' => 0,
    //         ],
    //     ];

    //     // Hitung kenaikan periode lalu
    //     if ($akunDengan) {
    //         $lalu = $getKenaikan($akunDengan->id_akun, '1900-01-01', date('Y-m-d', strtotime($start . ' -1 day')));
    //         $data_aset_neto['dengan_pembatasan']['kenaikan_periode_lalu'] = ($lalu->total_kredit ?? 0) - ($lalu->total_debit ?? 0);
    //     }

    //     if ($akunTanpa) {
    //         $lalu = $getKenaikan($akunTanpa->id_akun, '1900-01-01', date('Y-m-d', strtotime($start . ' -1 day')));
    //         $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_lalu'] = ($lalu->total_kredit ?? 0) - ($lalu->total_debit ?? 0);
    //     }

    //     // Hitung kenaikan periode berjalan
    //     $getTotalManual = function ($isPendapatan, $jenis_transaksi, $start, $end) use ($id_unit, $id_divisi) {
    //         $kategori = $isPendapatan ? 'PENERIMAAN DAN SUMBANGAN' : 'BEBAN';
    //         $debit_kredit = $isPendapatan ? 'kredit' : 'debit';

    //         return Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($jenis_transaksi, $start, $end, $id_unit, $id_divisi) {
    //             $q->whereHas('buku_besar') // Hanya jurnal yang sudah diposting
    //             ->where('jenis_transaksi', $jenis_transaksi)
    //             ->whereBetween('tanggal', [$start, $end]);
                
    //             if ($id_unit) $q->where('id_unit', $id_unit);
    //             if ($id_divisi) $q->where('id_divisi', $id_divisi);
    //         })
    //         ->whereHas('akun.sub_kategori_akun.kategori_akun', function ($q) use ($kategori) {
    //             $q->where('kategori_akun', $kategori);
    //         })
    //         ->where('debit_kredit', $debit_kredit)
    //         ->sum('nominal');
    //     };

    //     $pendapatan_terikat = $getTotalManual(true, 'Terikat', $start, $end);
    //     $beban_terikat = $getTotalManual(false, 'Terikat', $start, $end);
    //     $pendapatan_tidak_terikat = $getTotalManual(true, 'Tidak Terikat', $start, $end);
    //     $beban_tidak_terikat = $getTotalManual(false, 'Tidak Terikat', $start, $end);

    //     // Saldo awal pendapatan dan beban menggunakan relationships
    //     $saldoAwalPendapatan = Akun::whereHas('sub_kategori_akun.kategori_akun', function ($q) {
    //         $q->where('kategori_akun', 'PENERIMAAN DAN SUMBANGAN');
    //     })->sum('saldo_awal_kredit');

    //     $saldoAwalBeban = Akun::whereHas('sub_kategori_akun.kategori_akun', function ($q) {
    //         $q->where('kategori_akun', 'BEBAN');
    //     })->sum('saldo_awal_debit');

    //     $total_raw = $pendapatan_terikat + $pendapatan_tidak_terikat;
    //     $kenaikan_terikat = $pendapatan_terikat - $beban_terikat;
    //     $kenaikan_tidak_terikat = $pendapatan_tidak_terikat - $beban_tidak_terikat;

    //     if ($total_raw > 0) {
    //         $proporsi_terikat = $pendapatan_terikat / $total_raw;
    //         $proporsi_tidak_terikat = $pendapatan_tidak_terikat / $total_raw;

    //         $kenaikan_terikat += $saldoAwalPendapatan * $proporsi_terikat - $saldoAwalBeban * $proporsi_terikat;
    //         $kenaikan_tidak_terikat += $saldoAwalPendapatan * $proporsi_tidak_terikat - $saldoAwalBeban * $proporsi_tidak_terikat;
    //     }

    //     $data_aset_neto['dengan_pembatasan']['kenaikan_periode_berjalan'] = $kenaikan_terikat;
    //     $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_berjalan'] = $kenaikan_tidak_terikat;

    //     $data_aset_neto['dengan_pembatasan']['saldo_akhir'] =
    //         $data_aset_neto['dengan_pembatasan']['saldo_awal'] +
    //         $data_aset_neto['dengan_pembatasan']['kenaikan_periode_lalu'] +
    //         $data_aset_neto['dengan_pembatasan']['kenaikan_periode_berjalan'];

    //     $data_aset_neto['tanpa_pembatasan']['saldo_akhir'] =
    //         $data_aset_neto['tanpa_pembatasan']['saldo_awal'] +
    //         $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_lalu'] +
    //         $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_berjalan'];

    //     // ========== LOGIKA UMUM UNTUK AKUN LAINNYA ==========
    //     // Menggunakan eager loading untuk menghindari N+1 problem
    //     $semua_akun = Akun::with([
    //         'sub_kategori_akun.kategori_akun',
    //         'detail_jurnal_umum' => function ($query) use ($start, $end, $id_unit, $id_divisi) {
    //             $query->whereHas('jurnal_umum', function ($q) use ($start, $end, $id_unit, $id_divisi) {
    //                 $q->whereHas('buku_besar'); // Hanya jurnal yang sudah diposting
                    
    //                 if ($id_unit) $q->where('id_unit', $id_unit);
    //                 if ($id_divisi) $q->where('id_divisi', $id_divisi);
    //             });
    //         },
    //         'detail_jurnal_umum.jurnal_umum'
    //     ])
    //     ->whereHas('sub_kategori_akun.kategori_akun', function ($query) {
    //         $query->whereIn('kategori_akun', ['AKTIVA', 'KEWAJIBAN', 'ASET NETO']);
    //     })
    //     ->get();

    //     $saldo_akun = collect();
    //     $totalKewajibanAsetNeto = 0;
    //     $totalPeriodeLaluKewajibanAsetNeto = 0;

    //     foreach ($semua_akun as $akun) {
    //         $saldo_awal_debit = $akun->saldo_awal_debit ?? 0;
    //         $saldo_awal_kredit = $akun->saldo_awal_kredit ?? 0;

    //         $mutasi_debit = 0;
    //         $mutasi_kredit = 0;
    //         $debit_lalu = 0;
    //         $kredit_lalu = 0;

    //         // Gunakan collection methods untuk lebih efisien
    //         $detail_periode_berjalan = $akun->detail_jurnal_umum->filter(function ($detail) use ($start, $end) {
    //             $jurnal = $detail->jurnal_umum;
    //             return $jurnal && $jurnal->tanggal >= $start && $jurnal->tanggal <= $end;
    //         });

    //         $detail_periode_lalu = $akun->detail_jurnal_umum->filter(function ($detail) use ($tahun_lalu) {
    //             $jurnal = $detail->jurnal_umum;
    //             return $jurnal && Carbon::parse($jurnal->tanggal)->year == $tahun_lalu;
    //         });

    //         // Hitung mutasi periode berjalan
    //         $mutasi_debit = $detail_periode_berjalan->where('debit_kredit', 'debit')->sum('nominal');
    //         $mutasi_kredit = $detail_periode_berjalan->where('debit_kredit', 'kredit')->sum('nominal');

    //         // Hitung mutasi periode lalu
    //         $debit_lalu = $detail_periode_lalu->where('debit_kredit', 'debit')->sum('nominal');
    //         $kredit_lalu = $detail_periode_lalu->where('debit_kredit', 'kredit')->sum('nominal');

    //         $kategori = $akun->sub_kategori_akun->kategori_akun->kategori_akun;
    //         $sub_kategori = $akun->sub_kategori_akun->sub_kategori_akun;

    //         // ✅ KHUSUS: Untuk akun "Dengan Pembatasan" dan "Tanpa Pembatasan", gunakan perhitungan khusus
    //         if ($kategori === 'ASET NETO' && $sub_kategori === 'Dengan Pembatasan') {
    //             $saldo = $data_aset_neto['dengan_pembatasan']['saldo_akhir'];
    //             $periode_lalu = $data_aset_neto['dengan_pembatasan']['saldo_awal'];
    //         } elseif ($kategori === 'ASET NETO' && $sub_kategori === 'Tanpa Pembatasan') {
    //             $saldo = $data_aset_neto['tanpa_pembatasan']['saldo_akhir'];
    //             $periode_lalu = $data_aset_neto['tanpa_pembatasan']['saldo_awal'];
    //         } else {
    //             // Perhitungan normal untuk akun lainnya
    //             if ($kategori === 'AKTIVA') {
    //                 $saldo = ($saldo_awal_debit + $mutasi_debit) - ($saldo_awal_kredit + $mutasi_kredit);
    //                 $periode_lalu = $saldo_awal_debit - $saldo_awal_kredit;
    //             } else {
    //                 // KEWAJIBAN dan ASET NETO lainnya
    //                 $saldo = ($saldo_awal_kredit + $mutasi_kredit) - ($saldo_awal_debit + $mutasi_debit);
    //                 $periode_lalu = $saldo_awal_kredit - $saldo_awal_debit;
    //             }
    //         }

    //         $saldo_akun[$akun->id_akun] = (object)[
    //             'saldo' => $saldo,
    //             'periode_lalu' => $periode_lalu,
    //         ];

    //         // Hitung total hanya jika KEWAJIBAN dan ASET NETO
    //         if (in_array($kategori, ['KEWAJIBAN', 'ASET NETO'])) {
    //             $totalKewajibanAsetNeto += $saldo;
    //             $totalPeriodeLaluKewajibanAsetNeto += $periode_lalu;
    //         }
    //     }

    //     if ($request->has('export_excel')) {
    //         return $this->exportExcel($semua_akun, $saldo_akun, $start, $end);
    //     }

    //     $units = Unit::all();
    //     $divisis = Divisi::all();

    //     return view('neraca-saldo', compact(
    //         'semua_akun', 'saldo_akun', 'units', 'divisis',
    //         'id_unit', 'id_divisi',
    //         'totalKewajibanAsetNeto', 'totalPeriodeLaluKewajibanAsetNeto'
    //     ));
    // }


    public function index(Request $request)
    {
        $user = Auth::user();

        $id_unit = $request->unit;
        $id_divisi = $request->divisi;

        // Otomatis set unit/divisi dari role jika tidak dipilih
        if (!$id_unit && $user->role === 'akuntan_unit') {
            $id_unit = AkuntanUnit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
        }
        if (!$id_divisi && $user->role === 'akuntan_divisi') {
            $id_divisi = AkuntanDivisi::where('id_akuntan_divisi', $user->id_user)->value('id_divisi');
        }

        $start = $request->start_date ?? now()->startOfYear()->toDateString();
        $end = $request->end_date ?? now()->toDateString();
        $tahun_lalu = Carbon::parse($end)->year - 1;

        // ========== MENGGUNAKAN STORED PROCEDURE UNTUK NERACA SALDO ==========
        try {
            // Panggil stored procedure utama
            $neracaResults = DB::select('CALL get_neraca_saldo(?, ?, ?, ?)', [
                $start, 
                $end, 
                $id_unit, 
                $id_divisi
            ]);

            // Convert hasil procedure ke collection untuk kemudahan manipulasi
            $neracaCollection = collect($neracaResults);
            
        } catch (\Exception $e) {
            // Fallback ke method lama jika stored procedure gagal
            \Log::error('Stored procedure failed: ' . $e->getMessage());
            return $this->indexFallback($request);
        }

        // ========== LOGIKA KHUSUS UNTUK ASET NETO DENGAN/TANPA PEMBATASAN ==========
        $akunDengan = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Dengan Pembatasan')
                ->whereHas('kategori_akun', function ($q) {
                    $q->where('kategori_akun', 'ASET NETO');
                });
        })->first();

        $akunTanpa = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Tanpa Pembatasan')
                ->whereHas('kategori_akun', function ($q) {
                    $q->where('kategori_akun', 'ASET NETO');
                });
        })->first();

        // Gunakan stored function untuk perhitungan kenaikan aset neto
        $data_aset_neto = [
            'dengan_pembatasan' => [
                'saldo_awal' => $akunDengan ? $akunDengan->saldo_awal_kredit - $akunDengan->saldo_awal_debit : 0,
                'kenaikan_periode_lalu' => 0,
                'kenaikan_periode_berjalan' => 0,
                'saldo_akhir' => 0,
            ],
            'tanpa_pembatasan' => [
                'saldo_awal' => $akunTanpa ? $akunTanpa->saldo_awal_kredit - $akunTanpa->saldo_awal_debit : 0,
                'kenaikan_periode_lalu' => 0,
                'kenaikan_periode_berjalan' => 0,
                'saldo_akhir' => 0,
            ],
        ];

        // Hitung kenaikan periode lalu menggunakan stored function
        if ($akunDengan) {
            $kenaikanDengan = DB::select('SELECT get_kenaikan_aset_neto(?, ?, ?, ?, ?) as result', [
                $akunDengan->id_akun,
                '1900-01-01',
                date('Y-m-d', strtotime($start . ' -1 day')),
                $id_unit,
                $id_divisi
            ]);
            
            if (!empty($kenaikanDengan)) {
                $resultDengan = json_decode($kenaikanDengan[0]->result, true);
                $data_aset_neto['dengan_pembatasan']['kenaikan_periode_lalu'] = 
                    ($resultDengan['total_kredit'] ?? 0) - ($resultDengan['total_debit'] ?? 0);
            }
        }

        if ($akunTanpa) {
            $kenaikanTanpa = DB::select('SELECT get_kenaikan_aset_neto(?, ?, ?, ?, ?) as result', [
                $akunTanpa->id_akun,
                '1900-01-01',
                date('Y-m-d', strtotime($start . ' -1 day')),
                $id_unit,
                $id_divisi
            ]);
            
            if (!empty($kenaikanTanpa)) {
                $resultTanpa = json_decode($kenaikanTanpa[0]->result, true);
                $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_lalu'] = 
                    ($resultTanpa['total_kredit'] ?? 0) - ($resultTanpa['total_debit'] ?? 0);
            }
        }

        // Hitung kenaikan periode berjalan untuk aset neto
        $kenaikanBerjalan = $this->calculateAsetNetoKenaikanBerjalan($start, $end, $id_unit, $id_divisi);
        
        $data_aset_neto['dengan_pembatasan']['kenaikan_periode_berjalan'] = $kenaikanBerjalan['terikat'];
        $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_berjalan'] = $kenaikanBerjalan['tidak_terikat'];

        // Hitung saldo akhir
        $data_aset_neto['dengan_pembatasan']['saldo_akhir'] =
            $data_aset_neto['dengan_pembatasan']['saldo_awal'] +
            $data_aset_neto['dengan_pembatasan']['kenaikan_periode_lalu'] +
            $data_aset_neto['dengan_pembatasan']['kenaikan_periode_berjalan'];

        $data_aset_neto['tanpa_pembatasan']['saldo_akhir'] =
            $data_aset_neto['tanpa_pembatasan']['saldo_awal'] +
            $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_lalu'] +
            $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_berjalan'];

        // ========== PROSES HASIL STORED PROCEDURE ==========
        // Ambil semua akun untuk keperluan view (hanya metadata, bukan perhitungan)
        $semua_akun = Akun::with(['sub_kategori_akun.kategori_akun'])
            ->whereHas('sub_kategori_akun.kategori_akun', function ($query) {
                $query->whereIn('kategori_akun', ['AKTIVA', 'KEWAJIBAN', 'ASET NETO']);
            })
            ->get();

        // Convert hasil stored procedure ke format yang dibutuhkan view
        $saldo_akun = collect();
        $totalKewajibanAsetNeto = 0;
        $totalPeriodeLaluKewajibanAsetNeto = 0;

        foreach ($neracaCollection as $hasil) {
            $akun = $semua_akun->firstWhere('id_akun', $hasil->id_akun);
            
            if ($akun) {
                $kategori = $akun->sub_kategori_akun->kategori_akun->kategori_akun;
                $sub_kategori = $akun->sub_kategori_akun->sub_kategori_akun;
                
                // Override untuk aset neto dengan/tanpa pembatasan
                if ($kategori === 'ASET NETO' && $sub_kategori === 'Dengan Pembatasan') {
                    $saldo = $data_aset_neto['dengan_pembatasan']['saldo_akhir'];
                    $periode_lalu = $data_aset_neto['dengan_pembatasan']['saldo_awal'];
                } elseif ($kategori === 'ASET NETO' && $sub_kategori === 'Tanpa Pembatasan') {
                    $saldo = $data_aset_neto['tanpa_pembatasan']['saldo_akhir'];
                    $periode_lalu = $data_aset_neto['tanpa_pembatasan']['saldo_awal'];
                } else {
                    // Gunakan hasil dari stored procedure
                    $saldo = $hasil->saldo_akhir ?? 0;
                    $periode_lalu = $hasil->periode_lalu ?? 0;
                }

                $saldo_akun[$hasil->id_akun] = (object)[
                    'saldo' => $saldo,
                    'periode_lalu' => $periode_lalu,
                ];

                // Hitung total untuk KEWAJIBAN dan ASET NETO
                if (in_array($kategori, ['KEWAJIBAN', 'ASET NETO'])) {
                    $totalKewajibanAsetNeto += $saldo;
                    $totalPeriodeLaluKewajibanAsetNeto += $periode_lalu;
                }
            }
        }

        // Handle export excel
        if ($request->has('export_excel')) {
            return $this->exportExcel($semua_akun, $saldo_akun, $start, $end);
        }

        $units = Unit::all();
        $divisis = Divisi::all();

        return view('neraca-saldo', compact(
            'semua_akun', 'saldo_akun', 'units', 'divisis',
            'id_unit', 'id_divisi',
            'totalKewajibanAsetNeto', 'totalPeriodeLaluKewajibanAsetNeto'
        ));
    }



    /**
     * Method helper untuk menghitung kenaikan aset neto periode berjalan
     */
    private function calculateAsetNetoKenaikanBerjalan($start, $end, $id_unit, $id_divisi)
    {
        // Bisa dioptimasi lebih lanjut dengan stored procedure jika diperlukan
        $getTotalManual = function ($isPendapatan, $jenis_transaksi) use ($start, $end, $id_unit, $id_divisi) {
            $kategori = $isPendapatan ? 'PENERIMAAN DAN SUMBANGAN' : 'BEBAN';
            $debit_kredit = $isPendapatan ? 'kredit' : 'debit';

            return Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($jenis_transaksi, $start, $end, $id_unit, $id_divisi) {
                $q->whereHas('buku_besar')
                ->where('jenis_transaksi', $jenis_transaksi)
                ->whereBetween('tanggal', [$start, $end]);
                
                if ($id_unit) $q->where('id_unit', $id_unit);
                if ($id_divisi) $q->where('id_divisi', $id_divisi);
            })
            ->whereHas('akun.sub_kategori_akun.kategori_akun', function ($q) use ($kategori) {
                $q->where('kategori_akun', $kategori);
            })
            ->where('debit_kredit', $debit_kredit)
            ->sum('nominal');
        };

        $pendapatan_terikat = $getTotalManual(true, 'Terikat');
        $beban_terikat = $getTotalManual(false, 'Terikat');
        $pendapatan_tidak_terikat = $getTotalManual(true, 'Tidak Terikat');
        $beban_tidak_terikat = $getTotalManual(false, 'Tidak Terikat');

        // Saldo awal pendapatan dan beban
        $saldoAwalPendapatan = Akun::whereHas('sub_kategori_akun.kategori_akun', function ($q) {
            $q->where('kategori_akun', 'PENERIMAAN DAN SUMBANGAN');
        })->sum('saldo_awal_kredit');

        $saldoAwalBeban = Akun::whereHas('sub_kategori_akun.kategori_akun', function ($q) {
            $q->where('kategori_akun', 'BEBAN');
        })->sum('saldo_awal_debit');

        $total_raw = $pendapatan_terikat + $pendapatan_tidak_terikat;
        $kenaikan_terikat = $pendapatan_terikat - $beban_terikat;
        $kenaikan_tidak_terikat = $pendapatan_tidak_terikat - $beban_tidak_terikat;

        if ($total_raw > 0) {
            $proporsi_terikat = $pendapatan_terikat / $total_raw;
            $proporsi_tidak_terikat = $pendapatan_tidak_terikat / $total_raw;

            $kenaikan_terikat += $saldoAwalPendapatan * $proporsi_terikat - $saldoAwalBeban * $proporsi_terikat;
            $kenaikan_tidak_terikat += $saldoAwalPendapatan * $proporsi_tidak_terikat - $saldoAwalBeban * $proporsi_tidak_terikat;
        }

        return [
            'terikat' => $kenaikan_terikat,
            'tidak_terikat' => $kenaikan_tidak_terikat
        ];
    }




    private function indexFallback(Request $request)
    {
        $user = Auth::user();

        $id_unit = $request->unit;
        $id_divisi = $request->divisi;

        // Otomatis set unit/divisi dari role jika tidak dipilih
        if (!$id_unit && $user->role === 'akuntan_unit') {
            $id_unit = AkuntanUnit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
        }
        if (!$id_divisi && $user->role === 'akuntan_divisi') {
            $id_divisi = AkuntanDivisi::where('id_akuntan_divisi', $user->id_user)->value('id_divisi');
        }

        $start = $request->start_date ?? now()->startOfYear()->toDateString();
        $end = $request->end_date ?? now()->toDateString();
        $tahun_lalu = Carbon::parse($end)->year - 1;

        // ========== LOGIKA KHUSUS UNTUK ASET NETO DENGAN/TANPA PEMBATASAN ==========
        $akunDengan = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Dengan Pembatasan')
                ->whereHas('kategori_akun', function ($q) {
                    $q->where('kategori_akun', 'ASET NETO');
                });
        })->first();

        $akunTanpa = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Tanpa Pembatasan')
                ->whereHas('kategori_akun', function ($q) {
                    $q->where('kategori_akun', 'ASET NETO');
                });
        })->first();

        $getKenaikan = function ($id_akun, $start, $end) use ($id_unit, $id_divisi) {
            $query = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($start, $end, $id_unit, $id_divisi) {
                $q->whereHas('buku_besar') // Hanya jurnal yang sudah diposting
                ->whereBetween('tanggal', [$start, $end]);
                
                if ($id_unit) $q->where('id_unit', $id_unit);
                if ($id_divisi) $q->where('id_divisi', $id_divisi);
            })->where('id_akun', $id_akun);

            return $query->selectRaw("
                SUM(CASE WHEN debit_kredit = 'debit' THEN nominal ELSE 0 END) as total_debit,
                SUM(CASE WHEN debit_kredit = 'kredit' THEN nominal ELSE 0 END) as total_kredit
            ")->first();
        };

        $data_aset_neto = [
            'dengan_pembatasan' => [
                'saldo_awal' => $akunDengan ? $akunDengan->saldo_awal_kredit - $akunDengan->saldo_awal_debit : 0,
                'kenaikan_periode_lalu' => 0,
                'kenaikan_periode_berjalan' => 0,
                'saldo_akhir' => 0,
            ],
            'tanpa_pembatasan' => [
                'saldo_awal' => $akunTanpa ? $akunTanpa->saldo_awal_kredit - $akunTanpa->saldo_awal_debit : 0,
                'kenaikan_periode_lalu' => 0,
                'kenaikan_periode_berjalan' => 0,
                'saldo_akhir' => 0,
            ],
        ];

        // Hitung kenaikan periode lalu
        if ($akunDengan) {
            $lalu = $getKenaikan($akunDengan->id_akun, '1900-01-01', date('Y-m-d', strtotime($start . ' -1 day')));
            $data_aset_neto['dengan_pembatasan']['kenaikan_periode_lalu'] = ($lalu->total_kredit ?? 0) - ($lalu->total_debit ?? 0);
        }

        if ($akunTanpa) {
            $lalu = $getKenaikan($akunTanpa->id_akun, '1900-01-01', date('Y-m-d', strtotime($start . ' -1 day')));
            $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_lalu'] = ($lalu->total_kredit ?? 0) - ($lalu->total_debit ?? 0);
        }

        // Hitung kenaikan periode berjalan
        $getTotalManual = function ($isPendapatan, $jenis_transaksi, $start, $end) use ($id_unit, $id_divisi) {
            $kategori = $isPendapatan ? 'PENERIMAAN DAN SUMBANGAN' : 'BEBAN';
            $debit_kredit = $isPendapatan ? 'kredit' : 'debit';

            return Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($jenis_transaksi, $start, $end, $id_unit, $id_divisi) {
                $q->whereHas('buku_besar') // Hanya jurnal yang sudah diposting
                ->where('jenis_transaksi', $jenis_transaksi)
                ->whereBetween('tanggal', [$start, $end]);
                
                if ($id_unit) $q->where('id_unit', $id_unit);
                if ($id_divisi) $q->where('id_divisi', $id_divisi);
            })
            ->whereHas('akun.sub_kategori_akun.kategori_akun', function ($q) use ($kategori) {
                $q->where('kategori_akun', $kategori);
            })
            ->where('debit_kredit', $debit_kredit)
            ->sum('nominal');
        };

        $pendapatan_terikat = $getTotalManual(true, 'Terikat', $start, $end);
        $beban_terikat = $getTotalManual(false, 'Terikat', $start, $end);
        $pendapatan_tidak_terikat = $getTotalManual(true, 'Tidak Terikat', $start, $end);
        $beban_tidak_terikat = $getTotalManual(false, 'Tidak Terikat', $start, $end);

        // Saldo awal pendapatan dan beban menggunakan relationships
        $saldoAwalPendapatan = Akun::whereHas('sub_kategori_akun.kategori_akun', function ($q) {
            $q->where('kategori_akun', 'PENERIMAAN DAN SUMBANGAN');
        })->sum('saldo_awal_kredit');

        $saldoAwalBeban = Akun::whereHas('sub_kategori_akun.kategori_akun', function ($q) {
            $q->where('kategori_akun', 'BEBAN');
        })->sum('saldo_awal_debit');

        $total_raw = $pendapatan_terikat + $pendapatan_tidak_terikat;
        $kenaikan_terikat = $pendapatan_terikat - $beban_terikat;
        $kenaikan_tidak_terikat = $pendapatan_tidak_terikat - $beban_tidak_terikat;

        if ($total_raw > 0) {
            $proporsi_terikat = $pendapatan_terikat / $total_raw;
            $proporsi_tidak_terikat = $pendapatan_tidak_terikat / $total_raw;

            $kenaikan_terikat += $saldoAwalPendapatan * $proporsi_terikat - $saldoAwalBeban * $proporsi_terikat;
            $kenaikan_tidak_terikat += $saldoAwalPendapatan * $proporsi_tidak_terikat - $saldoAwalBeban * $proporsi_tidak_terikat;
        }

        $data_aset_neto['dengan_pembatasan']['kenaikan_periode_berjalan'] = $kenaikan_terikat;
        $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_berjalan'] = $kenaikan_tidak_terikat;

        $data_aset_neto['dengan_pembatasan']['saldo_akhir'] =
            $data_aset_neto['dengan_pembatasan']['saldo_awal'] +
            $data_aset_neto['dengan_pembatasan']['kenaikan_periode_lalu'] +
            $data_aset_neto['dengan_pembatasan']['kenaikan_periode_berjalan'];

        $data_aset_neto['tanpa_pembatasan']['saldo_akhir'] =
            $data_aset_neto['tanpa_pembatasan']['saldo_awal'] +
            $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_lalu'] +
            $data_aset_neto['tanpa_pembatasan']['kenaikan_periode_berjalan'];

        // ========== LOGIKA UMUM UNTUK AKUN LAINNYA ==========
        // Menggunakan eager loading untuk menghindari N+1 problem
        $semua_akun = Akun::with([
            'sub_kategori_akun.kategori_akun',
            'detail_jurnal_umum' => function ($query) use ($start, $end, $id_unit, $id_divisi) {
                $query->whereHas('jurnal_umum', function ($q) use ($start, $end, $id_unit, $id_divisi) {
                    $q->whereHas('buku_besar'); // Hanya jurnal yang sudah diposting
                    
                    if ($id_unit) $q->where('id_unit', $id_unit);
                    if ($id_divisi) $q->where('id_divisi', $id_divisi);
                });
            },
            'detail_jurnal_umum.jurnal_umum'
        ])
        ->whereHas('sub_kategori_akun.kategori_akun', function ($query) {
            $query->whereIn('kategori_akun', ['AKTIVA', 'KEWAJIBAN', 'ASET NETO']);
        })
        ->get();

        $saldo_akun = collect();
        $totalKewajibanAsetNeto = 0;
        $totalPeriodeLaluKewajibanAsetNeto = 0;

        foreach ($semua_akun as $akun) {
            $saldo_awal_debit = $akun->saldo_awal_debit ?? 0;
            $saldo_awal_kredit = $akun->saldo_awal_kredit ?? 0;

            $mutasi_debit = 0;
            $mutasi_kredit = 0;
            $debit_lalu = 0;
            $kredit_lalu = 0;

            // Gunakan collection methods untuk lebih efisien
            $detail_periode_berjalan = $akun->detail_jurnal_umum->filter(function ($detail) use ($start, $end) {
                $jurnal = $detail->jurnal_umum;
                return $jurnal && $jurnal->tanggal >= $start && $jurnal->tanggal <= $end;
            });

            $detail_periode_lalu = $akun->detail_jurnal_umum->filter(function ($detail) use ($tahun_lalu) {
                $jurnal = $detail->jurnal_umum;
                return $jurnal && Carbon::parse($jurnal->tanggal)->year == $tahun_lalu;
            });

            // Hitung mutasi periode berjalan
            $mutasi_debit = $detail_periode_berjalan->where('debit_kredit', 'debit')->sum('nominal');
            $mutasi_kredit = $detail_periode_berjalan->where('debit_kredit', 'kredit')->sum('nominal');

            // Hitung mutasi periode lalu
            $debit_lalu = $detail_periode_lalu->where('debit_kredit', 'debit')->sum('nominal');
            $kredit_lalu = $detail_periode_lalu->where('debit_kredit', 'kredit')->sum('nominal');

            $kategori = $akun->sub_kategori_akun->kategori_akun->kategori_akun;
            $sub_kategori = $akun->sub_kategori_akun->sub_kategori_akun;

            // ✅ KHUSUS: Untuk akun "Dengan Pembatasan" dan "Tanpa Pembatasan", gunakan perhitungan khusus
            if ($kategori === 'ASET NETO' && $sub_kategori === 'Dengan Pembatasan') {
                $saldo = $data_aset_neto['dengan_pembatasan']['saldo_akhir'];
                $periode_lalu = $data_aset_neto['dengan_pembatasan']['saldo_awal'];
            } elseif ($kategori === 'ASET NETO' && $sub_kategori === 'Tanpa Pembatasan') {
                $saldo = $data_aset_neto['tanpa_pembatasan']['saldo_akhir'];
                $periode_lalu = $data_aset_neto['tanpa_pembatasan']['saldo_awal'];
            } else {
                // Perhitungan normal untuk akun lainnya
                if ($kategori === 'AKTIVA') {
                    $saldo = ($saldo_awal_debit + $mutasi_debit) - ($saldo_awal_kredit + $mutasi_kredit);
                    $periode_lalu = $saldo_awal_debit - $saldo_awal_kredit;
                } else {
                    // KEWAJIBAN dan ASET NETO lainnya
                    $saldo = ($saldo_awal_kredit + $mutasi_kredit) - ($saldo_awal_debit + $mutasi_debit);
                    $periode_lalu = $saldo_awal_kredit - $saldo_awal_debit;
                }
            }

            $saldo_akun[$akun->id_akun] = (object)[
                'saldo' => $saldo,
                'periode_lalu' => $periode_lalu,
            ];

            // Hitung total hanya jika KEWAJIBAN dan ASET NETO
            if (in_array($kategori, ['KEWAJIBAN', 'ASET NETO'])) {
                $totalKewajibanAsetNeto += $saldo;
                $totalPeriodeLaluKewajibanAsetNeto += $periode_lalu;
            }
        }

        if ($request->has('export_excel')) {
            return $this->exportExcel($semua_akun, $saldo_akun, $start, $end);
        }

        $units = Unit::all();
        $divisis = Divisi::all();

        return view('neraca-saldo', compact(
            'semua_akun', 'saldo_akun', 'units', 'divisis',
            'id_unit', 'id_divisi',
            'totalKewajibanAsetNeto', 'totalPeriodeLaluKewajibanAsetNeto'
        ));
    }


    private function exportExcel($semua_akun, $saldo_akun, $tanggal_mulai, $tanggal_selesai)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 🖼️ Sisipkan gambar/logo di pojok kiri atas
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Yayasan');
        $drawing->setPath(public_path('assets/images/logos/YDB_PNG.png')); // ✅ Pastikan path benar
        $drawing->setHeight(100); // Tinggi gambar (px)
        $drawing->setCoordinates('A1'); // Mulai dari A1
        $drawing->setOffsetX(5); // Optional padding dari kiri
        $drawing->setWorksheet($sheet);

        // 📝 Judul dan periode
        $judul = "POSISI KEUANGAN YAYASAN DARUSSALAM BATAM\nPeriode: " .
                date('d/m/Y', strtotime($tanggal_mulai)) . " - " . date('d/m/Y', strtotime($tanggal_selesai));
        $sheet->setCellValue('A1', $judul);

        // 📐 Merge cell biar tampak sebagai header blok
        $sheet->mergeCells('A1:C4');

        // 💅 Style header teks
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1')->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);


        $sheet->setCellValue('A4', 'AKUN');
        $sheet->setCellValue('B4', 'SALDO PERIODE LALU');
        $sheet->setCellValue('C4', 'SALDO');

        $sheet->getStyle('A4:C4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
            'font' => ['color' => ['rgb' => 'FFFFFF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle('A4:C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 5;
        $totalPeriodeLalu = 0;
        $totalSaldo = 0;

        foreach ($semua_akun->groupBy('sub_kategori_akun.kategori_akun.kategori_akun') as $kategori => $sub_kategoris) {
            $sheet->setCellValue("A{$row}", strtoupper($kategori));
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
            $row++;

            foreach ($sub_kategoris->groupBy('sub_kategori_akun.sub_kategori_akun') as $sub_kategori => $akuns) {
                $sheet->setCellValue("A{$row}", "   {$sub_kategori}");
                $sheet->mergeCells("A{$row}:C{$row}");
                $sheet->getStyle("A{$row}:C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
                $row++;

                $subPeriodeLalu = 0;
                $subSaldo = 0;

                foreach ($akuns as $akun) {
                    $periode_lalu = $saldo_akun[$akun->id_akun]->periode_lalu ?? 0;
                    $saldo = $saldo_akun[$akun->id_akun]->saldo ?? 0;

                    $subPeriodeLalu += $periode_lalu;
                    $subSaldo += $saldo;
                    $totalPeriodeLalu += $periode_lalu;
                    $totalSaldo += $saldo;

                    $sheet->setCellValue("A{$row}", "      {$akun->akun}");
                    $sheet->setCellValue("B{$row}", $periode_lalu);
                    $sheet->setCellValue("C{$row}", $saldo);

                    $sheet->getStyle("B{$row}:C{$row}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("B{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $row++;
                }

                // Subtotal Subkategori
                $sheet->setCellValue("A{$row}", "Subtotal {$sub_kategori}");
                $sheet->setCellValue("B{$row}", $subPeriodeLalu);
                $sheet->setCellValue("C{$row}", $subSaldo);
                $sheet->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);
                $row++;
            }

            // Subtotal Kategori
            $sheet->setCellValue("A{$row}", "Subtotal " . strtoupper($kategori));
            $sheet->setCellValue("B{$row}", ''); // Opsional: bisa dihitung ulang kalau mau
            $sheet->setCellValue("C{$row}", '');
            $sheet->getStyle("A{$row}:C{$row}")->getFont()->setBold(true)->getColor()->setRGB('000000');
            $row++;
        }

        // Total
        $sheet->setCellValue("A{$row}", 'Total');
        $sheet->setCellValue("B{$row}", $totalPeriodeLalu);
        $sheet->setCellValue("C{$row}", $totalSaldo);
        $sheet->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);
        $sheet->getStyle("B{$row}:C{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $row++;

        // Border seluruh tabel
        $sheet->getStyle("A4:C" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto width
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Footer
        $row += 2;
        $sheet->setCellValue("A{$row}", 'Sistem Informasi Akuntansi Yayasan Darussalam Batam | ' . date('Y'));
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Output
        $fileName = 'Neraca_Saldo_' . date('d-m-Y', strtotime($tanggal_mulai)) . '_' . date('d-m-Y', strtotime($tanggal_selesai)) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


}





