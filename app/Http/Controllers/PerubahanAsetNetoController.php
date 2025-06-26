<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Unit;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Perubahan_Aset_Neto;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PerubahanAsetNetoController extends Controller
{
    
    public function index(Request $request)
    {
        $user = Auth::user();

        $start = $request->input('tanggal_mulai') ?? date('Y') . '-01-01';
        $end = $request->input('tanggal_selesai') ?? date('Y-m-d');

        // Cek dan tetapkan unit & divisi sesuai role
        $id_unit = $request->unit;
        $id_divisi = $request->divisi;

        if (!$id_unit && $user->role === 'akuntan_unit') {
            $id_unit = \App\Models\Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
        }
        if (!$id_divisi && $user->role === 'akuntan_divisi') {
            $id_divisi = \App\Models\Akuntan_Divisi::where('id_akuntan_divisi', $user->id_user)->value('id_divisi');
        }

        // Ambil akun ASET NETO
        $akunDengan = DB::table('akun')
            ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
            ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
            ->where('kategori_akun.kategori_akun', 'ASET NETO')
            ->where('sub_kategori_akun.sub_kategori_akun', 'Dengan Pembatasan')
            ->select('akun.*')
            ->first();

        $akunTanpa = DB::table('akun')
            ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
            ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
            ->where('kategori_akun.kategori_akun', 'ASET NETO')
            ->where('sub_kategori_akun.sub_kategori_akun', 'Tanpa Pembatasan')
            ->select('akun.*')
            ->first();

        $getKenaikan = function ($id_akun, $start, $end) use ($id_unit, $id_divisi) {
            $query = DB::table('detail_jurnal_umum')
                ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
                ->where('detail_jurnal_umum.id_akun', $id_akun)
                ->whereBetween('jurnal_umum.tanggal', [$start, $end]);

            if ($id_unit) $query->where('jurnal_umum.id_unit', $id_unit);
            if ($id_divisi) $query->where('jurnal_umum.id_divisi', $id_divisi);

            return $query->select(
                DB::raw("SUM(CASE WHEN debit_kredit = 'debit' THEN nominal ELSE 0 END) as total_debit"),
                DB::raw("SUM(CASE WHEN debit_kredit = 'kredit' THEN nominal ELSE 0 END) as total_kredit")
            )->first();
        };

        $data = [
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

        if ($akunDengan) {
            $lalu = $getKenaikan($akunDengan->id_akun, '1900-01-01', date('Y-m-d', strtotime($start . ' -1 day')));
            $data['dengan_pembatasan']['kenaikan_periode_lalu'] = ($lalu->total_kredit ?? 0) - ($lalu->total_debit ?? 0);
        }

        if ($akunTanpa) {
            $lalu = $getKenaikan($akunTanpa->id_akun, '1900-01-01', date('Y-m-d', strtotime($start . ' -1 day')));
            $data['tanpa_pembatasan']['kenaikan_periode_lalu'] = ($lalu->total_kredit ?? 0) - ($lalu->total_debit ?? 0);
        }

        $getTotalManual = function ($isPendapatan, $jenis_transaksi, $start, $end) use ($id_unit, $id_divisi) {
            $kategori = $isPendapatan ? 'PENERIMAAN DAN SUMBANGAN' : 'BEBAN';
            $debit_kredit = $isPendapatan ? 'kredit' : 'debit';

            return DB::table('detail_jurnal_umum as dju')
                ->join('jurnal_umum as ju', 'dju.id_jurnal_umum', '=', 'ju.id_jurnal_umum')
                ->join('akun as a', 'dju.id_akun', '=', 'a.id_akun')
                ->join('sub_kategori_akun as ska', 'a.id_sub_kategori_akun', '=', 'ska.id_sub_kategori_akun')
                ->join('kategori_akun as ka', 'ska.id_kategori_akun', '=', 'ka.id_kategori_akun')
                ->where('ka.kategori_akun', $kategori)
                ->where('ju.jenis_transaksi', $jenis_transaksi)
                ->whereBetween('ju.tanggal', [$start, $end])
                ->where('dju.debit_kredit', $debit_kredit)
                ->when($id_unit, fn($q) => $q->where('ju.id_unit', $id_unit))
                ->when($id_divisi, fn($q) => $q->where('ju.id_divisi', $id_divisi))
                ->sum('dju.nominal');
        };

        $pendapatan_terikat = $getTotalManual(true, 'Terikat', $start, $end);
        $beban_terikat = $getTotalManual(false, 'Terikat', $start, $end);
        $pendapatan_tidak_terikat = $getTotalManual(true, 'Tidak Terikat', $start, $end);
        $beban_tidak_terikat = $getTotalManual(false, 'Tidak Terikat', $start, $end);

        $saldoAwalPendapatan = DB::table('akun')
            ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
            ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
            ->where('kategori_akun.kategori_akun', 'PENERIMAAN DAN SUMBANGAN')
            ->sum('akun.saldo_awal_kredit');

        $saldoAwalBeban = DB::table('akun')
            ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
            ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
            ->where('kategori_akun.kategori_akun', 'BEBAN')
            ->sum('akun.saldo_awal_debit');

        $total_raw = $pendapatan_terikat + $pendapatan_tidak_terikat;
        $kenaikan_terikat = $pendapatan_terikat - $beban_terikat;
        $kenaikan_tidak_terikat = $pendapatan_tidak_terikat - $beban_tidak_terikat;

        if ($total_raw > 0) {
            $proporsi_terikat = $pendapatan_terikat / $total_raw;
            $proporsi_tidak_terikat = $pendapatan_tidak_terikat / $total_raw;

            $kenaikan_terikat += $saldoAwalPendapatan * $proporsi_terikat - $saldoAwalBeban * $proporsi_terikat;
            $kenaikan_tidak_terikat += $saldoAwalPendapatan * $proporsi_tidak_terikat - $saldoAwalBeban * $proporsi_tidak_terikat;
        }

        $data['dengan_pembatasan']['kenaikan_periode_berjalan'] = $kenaikan_terikat;
        $data['tanpa_pembatasan']['kenaikan_periode_berjalan'] = $kenaikan_tidak_terikat;

        $data['dengan_pembatasan']['saldo_akhir'] =
            $data['dengan_pembatasan']['saldo_awal'] +
            $data['dengan_pembatasan']['kenaikan_periode_lalu'] +
            $data['dengan_pembatasan']['kenaikan_periode_berjalan'];

        $data['tanpa_pembatasan']['saldo_akhir'] =
            $data['tanpa_pembatasan']['saldo_awal'] +
            $data['tanpa_pembatasan']['kenaikan_periode_lalu'] +
            $data['tanpa_pembatasan']['kenaikan_periode_berjalan'];

        $total_saldo_akhir = $data['dengan_pembatasan']['saldo_akhir'] + $data['tanpa_pembatasan']['saldo_akhir'];

        if ($request->has('export_excel')) {
            return $this->export_excel($data, $total_saldo_akhir, $start, $end);
        }

        $units = Unit::all();
        $divisis = Divisi::all();

        return view('perubahan_aset_neto', compact(
            'data',
            'total_saldo_akhir',
            'start',
            'end',
            'units',
            'divisis',
            'id_unit',
            'id_divisi'
        ));
    }



    // public function index(Request $request)
    // {
    //     $start = $request->input('tanggal_mulai') ?? date('Y') . '-01-01';
    //     $end = $request->input('tanggal_selesai') ?? date('Y-m-d');

    //     $unitId = $request->unit;
    //     $divisiId = $request->divisi;

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

    //     $getKenaikan = function ($id_akun, $start, $end) use ($unitId, $divisiId) {
    //         $query = DB::table('detail_jurnal_umum')
    //             ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
    //             ->where('detail_jurnal_umum.id_akun', $id_akun)
    //             ->whereBetween('jurnal_umum.tanggal', [$start, $end]);

    //         if ($unitId) $query->where('jurnal_umum.id_unit', $unitId);
    //         if ($divisiId) $query->where('jurnal_umum.id_divisi', $divisiId);

    //         return $query->select(
    //             DB::raw("SUM(CASE WHEN debit_kredit = 'debit' THEN nominal ELSE 0 END) as total_debit"),
    //             DB::raw("SUM(CASE WHEN debit_kredit = 'kredit' THEN nominal ELSE 0 END) as total_kredit")
    //         )->first();
    //     };

    //     $data = [
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

    //     // Periode lalu dihitung dari awal data sampai sebelum $start
    //     if ($akunDengan) {
    //         $lalu = $getKenaikan($akunDengan->id_akun, '1900-01-01', date('Y-m-d', strtotime($start . ' -1 day')));
    //         $data['dengan_pembatasan']['kenaikan_periode_lalu'] = ($lalu->total_kredit ?? 0) - ($lalu->total_debit ?? 0);
    //     }
    //     if ($akunTanpa) {
    //         $lalu = $getKenaikan($akunTanpa->id_akun, '1900-01-01', date('Y-m-d', strtotime($start . ' -1 day')));
    //         $data['tanpa_pembatasan']['kenaikan_periode_lalu'] = ($lalu->total_kredit ?? 0) - ($lalu->total_debit ?? 0);
    //     }

    //     $getTotalManual = function ($isPendapatan, $jenis_transaksi, $start, $end) use ($unitId, $divisiId) {
    //         $kategori = $isPendapatan ? 'PENERIMAAN DAN SUMBANGAN' : 'BEBAN';
    //         $debit_kredit = $isPendapatan ? 'kredit' : 'debit';

    //         return DB::table('detail_jurnal_umum as dju')
    //             ->join('jurnal_umum as ju', 'dju.id_jurnal_umum', '=', 'ju.id_jurnal_umum')
    //             ->join('akun as a', 'dju.id_akun', '=', 'a.id_akun')
    //             ->join('sub_kategori_akun as ska', 'a.id_sub_kategori_akun', '=', 'ska.id_sub_kategori_akun')
    //             ->join('kategori_akun as ka', 'ska.id_kategori_akun', '=', 'ka.id_kategori_akun')
    //             ->where('ka.kategori_akun', $kategori)
    //             ->where('ju.jenis_transaksi', $jenis_transaksi)
    //             ->whereBetween('ju.tanggal', [$start, $end])
    //             ->where('dju.debit_kredit', $debit_kredit)
    //             ->when($unitId, fn($q) => $q->where('ju.id_unit', $unitId))
    //             ->when($divisiId, fn($q) => $q->where('ju.id_divisi', $divisiId))
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

    //     $data['dengan_pembatasan']['kenaikan_periode_berjalan'] = $kenaikan_terikat;
    //     $data['tanpa_pembatasan']['kenaikan_periode_berjalan'] = $kenaikan_tidak_terikat;

    //     $data['dengan_pembatasan']['saldo_akhir'] =
    //         $data['dengan_pembatasan']['saldo_awal'] +
    //         $data['dengan_pembatasan']['kenaikan_periode_lalu'] +
    //         $data['dengan_pembatasan']['kenaikan_periode_berjalan'];

    //     $data['tanpa_pembatasan']['saldo_akhir'] =
    //         $data['tanpa_pembatasan']['saldo_awal'] +
    //         $data['tanpa_pembatasan']['kenaikan_periode_lalu'] +
    //         $data['tanpa_pembatasan']['kenaikan_periode_berjalan'];

    //     $total_saldo_akhir = $data['dengan_pembatasan']['saldo_akhir'] + $data['tanpa_pembatasan']['saldo_akhir'];

    //     if ($request->has('export_excel')) {
    //         return $this->export_excel($data, $total_saldo_akhir, $start, $end);
    //     }

    //     $units = Unit::all();
    //     $divisis = Divisi::all();

    //     return view('perubahan_aset_neto', compact('data', 'total_saldo_akhir', 'start', 'end', 'units', 'divisis'));
    // }











    // public function export_excel($data, $total_saldo_akhir, $start, $end)
    // {
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     $sheet->setCellValue('A1', 'LAPORAN PERUBAHAN ASET NETO');
    //     $sheet->mergeCells('A1:E1');
    //     $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    //     $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($start)) . ' - ' . date('d/m/Y', strtotime($end)));
    //     $sheet->mergeCells('A2:E2');
    //     $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     $sheet->setCellValue('A4', 'Keterangan');
    //     $sheet->setCellValue('B4', 'Dengan Pembatasan');
    //     $sheet->setCellValue('C4', 'Tanpa Pembatasan');
    //     $sheet->setCellValue('D4', 'Jumlah');

    //     $sheet->getStyle('A4:D4')->applyFromArray([
    //         'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    //         'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
    //         'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    //         'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    //     ]);

    //     $row = 5;
    //     $items = [
    //         'Saldo Awal' => 'saldo_awal',
    //         'Kenaikan/Penurunan Periode Lalu' => 'kenaikan_periode_lalu',
    //         'Kenaikan/Penurunan Periode Berjalan' => 'kenaikan_periode_berjalan',
    //         'Saldo Akhir' => 'saldo_akhir',
    //     ];

    //     foreach ($items as $label => $key) {
    //         $dengan = $data['dengan_pembatasan'][$key];
    //         $tanpa = $data['tanpa_pembatasan'][$key];
    //         $jumlah = $dengan + $tanpa;

    //         $sheet->setCellValue("A{$row}", $label);
    //         $sheet->setCellValue("B{$row}", $dengan);
    //         $sheet->setCellValue("C{$row}", $tanpa);
    //         $sheet->setCellValue("D{$row}", $jumlah);

    //         $sheet->getStyle("B{$row}:D{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
    //         $sheet->getStyle("B{$row}:D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //         $row++;
    //     }

    //     $sheet->getStyle("A4:D" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    //     foreach (range('A', 'D') as $col) {
    //         $sheet->getColumnDimension($col)->setAutoSize(true);
    //     }

    //     $row += 2;
    //     $sheet->setCellValue("A{$row}", 'Sistem Informasi Akuntansi | ' . date('Y'));
    //     $sheet->mergeCells("A{$row}:D{$row}");
    //     $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     $fileName = 'Perubahan_Aset_Neto_' . date('d-m-Y', strtotime($start)) . '_' . date('d-m-Y', strtotime($end)) . '.xlsx';

    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header("Content-Disposition: attachment;filename=\"{$fileName}\"");
    //     header('Cache-Control: max-age=0');

    //     $writer = new Xlsx($spreadsheet);
    //     $writer->save('php://output');
    //     exit;
    // }

    public function export_excel($data, $total_saldo_akhir, $tanggal_mulai, $tanggal_selesai)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 🖼️ Sisipkan gambar/logo
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Yayasan');
        $drawing->setPath(public_path('assets/images/logos/YDB_PNG.png'));
        $drawing->setHeight(100);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setWorksheet($sheet);

        // 📝 Tulis teks judul
        $judul = "LAPORAN PERUBAHAN ASET NETO YAYASAN DARUSSALAM BATAM\nPeriode: " .
            date('d/m/Y', strtotime($tanggal_mulai)) . " - " . date('d/m/Y', strtotime($tanggal_selesai));
        $sheet->setCellValue('A1', $judul);

        // 📐 Merge dan style header
        $sheet->mergeCells('A1:B4');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1')->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 6;

        // 🟩 Header bagian 1
        $sheet->setCellValue("A{$row}", 'Aset Neto Dengan Pembatasan Sumber Daya');
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C6EFCE']],
        ]);
        $row++;

        // 📊 Data bagian 1
        $items = [
            'Saldo Awal' => 'saldo_awal',
            'Kenaikan (Penurunan) Aset Neto Periode Lalu' => 'kenaikan_periode_lalu',
            'Kenaikan (Penurunan) Aset Neto Periode Berjalan' => 'kenaikan_periode_berjalan',
            'Saldo Akhir Aset Neto Dengan Pembatasan' => 'saldo_akhir',
        ];

        foreach ($items as $label => $key) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $data['dengan_pembatasan'][$key]);

            if (str_contains(strtolower($label), 'saldo akhir')) {
                $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true);
            }

            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $row++;
        }

        // 🟨 Header bagian 2
        $sheet->setCellValue("A{$row}", 'Aset Neto Tanpa Pembatasan Sumber Daya');
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF2CC']],
        ]);
        $row++;

        // 📊 Data bagian 2
        $items = [
            'Saldo Awal' => 'saldo_awal',
            'Kenaikan (Penurunan) Aset Neto Periode Lalu' => 'kenaikan_periode_lalu',
            'Kenaikan (Penurunan) Aset Neto Periode Berjalan' => 'kenaikan_periode_berjalan',
            'Saldo Akhir Aset Neto Tanpa Pembatasan' => 'saldo_akhir',
        ];

        foreach ($items as $label => $key) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $data['tanpa_pembatasan'][$key]);

            if (str_contains(strtolower($label), 'saldo akhir')) {
                $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true);
            }

            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $row++;
        }

        // ✅ Total
        $sheet->setCellValue("A{$row}", 'Total Saldo Akhir Aset Neto');
        $sheet->setCellValue("B{$row}", $total_saldo_akhir);
        $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 📏 Auto Size
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 📄 Footer info
        $row += 2;
        $sheet->setCellValue("A{$row}", 'Sistem Informasi Akuntansi | ' . date('Y'));
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 📥 Output
        $fileName = 'Perubahan_Aset_Neto_' . date('d-m-Y', strtotime($tanggal_mulai)) . '_' . date('d-m-Y', strtotime($tanggal_selesai)) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


}
