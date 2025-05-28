<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;
use App\Models\Kategori_Akun;
use App\Models\Detail_Jurnal_Umum;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NeracaSaldoController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Ambil semua akun dari database (agar semua akun muncul)
    //     $semua_akun = Akun::with(['sub_kategori_akun.kategori_akun'])->get();

    //     // Ambil ID jurnal umum yang sudah diposting ke buku besar
    //     $postedJurnalIds = DB::table('buku_besar')->pluck('id_jurnal_umum')->toArray();

    //     // Ambil saldo transaksi berdasarkan filter tanggal jika ada
    //     $query = Detail_Jurnal_Umum::whereIn('id_jurnal_umum', $postedJurnalIds)
    //         ->selectRaw('id_akun, 
    //                      SUM(CASE WHEN debit_kredit = "debit" THEN nominal ELSE 0 END) as total_debit, 
    //                      SUM(CASE WHEN debit_kredit = "kredit" THEN nominal ELSE 0 END) as total_kredit')
    //         ->groupBy('id_akun');

    //     // Jika ada filter tanggal, tambahkan kondisi ke query
    //     if ($request->filled('start_date') && $request->filled('end_date')) {
    //         $query->whereHas('jurnal_umum', function ($q) use ($request) {
    //             $q->whereBetween('tanggal', [$request->start_date, $request->end_date]);
    //         });
    //     }

    //     // Ambil hasil query dan ubah menjadi associative array agar mudah diakses di Blade
    //     $saldo_akun = $query->get()->keyBy('id_akun');

    //     return view('neraca-saldo', compact('semua_akun', 'saldo_akun'));
    // }

    // public function index(Request $request)
    // {
    //     $start = $request->start_date ?? '1900-01-01';
    //     $end = $request->end_date ?? now()->toDateString();

    //     $semua_akun = Akun::with(['sub_kategori_akun.kategori_akun'])->get();

    //     // Loop akun dan panggil function MySQL
    //     $saldo_akun = collect();
    //     foreach ($semua_akun as $akun) {
    //         $saldo = DB::selectOne("SELECT get_saldo_akun2(?, ?, ?) AS saldo", [
    //             $akun->id_akun,
    //             $start,
    //             $end
    //         ]);

    //         $debit = $saldo->saldo >= 0 ? $saldo->saldo : 0;
    //         $kredit = $saldo->saldo < 0 ? abs($saldo->saldo) : 0;

    //         $saldo_akun[$akun->id_akun] = (object)[
    //             'total_debit' => $debit,
    //             'total_kredit' => $kredit
    //         ];
    //     }

    //     return view('neraca-saldo', compact('semua_akun', 'saldo_akun'));
    // }

    // public function index(Request $request)
    // {
    //     $start = $request->start_date ?? '1900-01-01';
    //     $end = $request->end_date ?? now()->toDateString();

    //     $semua_akun = Akun::with(['sub_kategori_akun.kategori_akun'])->get();

    //     $saldo_akun = collect();

    //     foreach ($semua_akun as $akun) {
    //         $saldo_awal_debit = $akun->saldo_awal_debit ?? 0;
    //         $saldo_awal_kredit = $akun->saldo_awal_kredit ?? 0;

    //         $mutasi = DB::table('detail_jurnal_umum as dju')
    //             ->join('jurnal_umum as ju', 'dju.id_jurnal_umum', '=', 'ju.id_jurnal_umum')
    //             ->join('buku_besar as bb', 'ju.id_jurnal_umum', '=', 'bb.id_jurnal_umum')
    //             ->selectRaw("
    //                 SUM(CASE WHEN dju.debit_kredit = 'debit' THEN dju.nominal ELSE 0 END) AS total_debit,
    //                 SUM(CASE WHEN dju.debit_kredit = 'kredit' THEN dju.nominal ELSE 0 END) AS total_kredit
    //             ")
    //             ->where('dju.id_akun', $akun->id_akun)
    //             ->whereBetween('ju.tanggal', [$start, $end])
    //             ->first();

    //         $mutasi_debit = $mutasi->total_debit ?? 0;
    //         $mutasi_kredit = $mutasi->total_kredit ?? 0;

    //         $total_debit = $saldo_awal_debit + $mutasi_debit;
    //         $total_kredit = $saldo_awal_kredit + $mutasi_kredit;

    //         $saldo_akun[$akun->id_akun] = (object)[
    //             'total_debit' => $total_debit,
    //             'total_kredit' => $total_kredit,
    //         ];
    //     }

    //     if ($request->has('export_excel')) {
    //         return $this->exportExcel($semua_akun, $saldo_akun, $start, $end);
    //     }

    //     return view('neraca-saldo', compact('semua_akun', 'saldo_akun'));
    // }

    public function index(Request $request)
    {
        $start = $request->start_date ?? '1900-01-01';
        $end = $request->end_date ?? now()->toDateString();

        $semua_akun = Akun::with([
            'sub_kategori_akun.kategori_akun',
            'detail_jurnal_umum.jurnal_umum'
        ])->get();

        $saldo_akun = collect();

        foreach ($semua_akun as $akun) {
            $saldo_awal_debit = $akun->saldo_awal_debit ?? 0;
            $saldo_awal_kredit = $akun->saldo_awal_kredit ?? 0;

            $mutasi_debit = 0;
            $mutasi_kredit = 0;

            foreach ($akun->detail_jurnal_umum as $detail) {
                $tanggal = optional($detail->jurnal_umum)->tanggal;
                if ($tanggal && $tanggal >= $start && $tanggal <= $end) {
                    if ($detail->debit_kredit === 'debit') {
                        $mutasi_debit += $detail->nominal;
                    } elseif ($detail->debit_kredit === 'kredit') {
                        $mutasi_kredit += $detail->nominal;
                    }
                }
            }

            $total_debit = $saldo_awal_debit + $mutasi_debit;
            $total_kredit = $saldo_awal_kredit + $mutasi_kredit;

            $saldo_akun[$akun->id_akun] = (object)[
                'total_debit' => $total_debit,
                'total_kredit' => $total_kredit,
            ];
        }

        if ($request->has('export_excel')) {
            return $this->exportExcel($semua_akun, $saldo_akun, $start, $end);
        }

        return view('neraca-saldo', compact('semua_akun', 'saldo_akun'));
    }


    private function exportExcel($semua_akun, $saldo_akun, $tanggal_mulai, $tanggal_selesai)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'NERACA SALDO YAYASAN DARUSSALAM BATAM');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($tanggal_mulai)) . ' - ' . date('d/m/Y', strtotime($tanggal_selesai)));
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Kategori / Subkategori / Akun');
        $sheet->setCellValue('B4', 'Debit (Rp)');
        $sheet->setCellValue('C4', 'Kredit (Rp)');

        $sheet->getStyle('A4:C4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
            'font' => ['color' => ['rgb' => 'FFFFFF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle('A4:C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 5;
        $totalDebit = 0;
        $totalKredit = 0;

        foreach ($semua_akun->groupBy('sub_kategori_akun.kategori_akun.kategori_akun') as $kategori => $sub_kategoris) {
            // Kategori Akun
            $sheet->setCellValue('A' . $row, strtoupper($kategori));
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
            $row++;

            foreach ($sub_kategoris->groupBy('sub_kategori_akun.sub_kategori_akun') as $sub_kategori => $akuns) {
                // Sub Kategori
                $sheet->setCellValue('A' . $row, "   {$sub_kategori}");
                $sheet->mergeCells("A{$row}:C{$row}");
                $sheet->getStyle("A{$row}:C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
                $row++;

                foreach ($akuns as $akun) {
                    $debit = $saldo_akun[$akun->id_akun]->total_debit ?? 0;
                    $kredit = $saldo_akun[$akun->id_akun]->total_kredit ?? 0;
                    $totalDebit += $debit;
                    $totalKredit += $kredit;

                    $sheet->setCellValue('A' . $row, "      {$akun->akun}");
                    $sheet->setCellValue('B' . $row, $debit);
                    $sheet->setCellValue('C' . $row, $kredit);

                    $sheet->getStyle("B{$row}:C{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("B{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $row++;
                }
            }
        }

        // Total
        $sheet->setCellValue('A' . $row, 'Total');
        $sheet->setCellValue('B' . $row, $totalDebit);
        $sheet->setCellValue('C' . $row, $totalKredit);
        $sheet->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);
        $sheet->getStyle("B{$row}:C{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

        // Border
        $sheet->getStyle("A4:C{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto-size kolom
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Footer
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Sistem Informasi Akuntansi Yayasan Darussalam Batam | ' . date('Y'));
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Output file
        $fileName = 'Neraca_Saldo_' . date('d-m-Y', strtotime($tanggal_mulai)) . '_' . date('d-m-Y', strtotime($tanggal_selesai)) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }



}





