<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Detail_Jurnal_Umum;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanKomprehensifController extends Controller
{

    // public function index(Request $request)
    // {
    //     $tanggal_mulai = $request->input('tanggal_mulai');
    //     $tanggal_selesai = $request->input('tanggal_selesai');

    //     // Ambil semua akun Pendapatan dan Beban
    //     $akunList = DB::table('akun')
    //         ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
    //         ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
    //         ->whereIn('kategori_akun.kategori_akun', ['PENERIMAAN DAN SUMBANGAN', 'Beban'])
    //         ->select('akun.id_akun', 'akun.akun AS nama_akun', 'kategori_akun.kategori_akun')
    //         ->get();

    //     $pendapatan_all = [];
    //     $beban_all = [];

    //     foreach ($akunList as $akun) {
    //         $isPendapatan = $akun->kategori_akun == 'PENERIMAAN DAN SUMBANGAN';

    //         // Query total tidak terikat
    //         $query_tanpa = DB::table('detail_jurnal_umum')
    //             ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
    //             ->where('detail_jurnal_umum.id_akun', $akun->id_akun)
    //             ->where('jurnal_umum.jenis_transaksi', 'tidak terikat');

    //         if ($tanggal_mulai && $tanggal_selesai) {
    //             $query_tanpa->whereBetween('jurnal_umum.tanggal', [$tanggal_mulai, $tanggal_selesai]);
    //         }

    //         $total_tanpa = $query_tanpa
    //             ->where('detail_jurnal_umum.debit_kredit', $isPendapatan ? 'kredit' : 'debit')
    //             ->sum('detail_jurnal_umum.nominal');

    //         // Query total terikat
    //         $query_dengan = DB::table('detail_jurnal_umum')
    //             ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
    //             ->where('detail_jurnal_umum.id_akun', $akun->id_akun)
    //             ->where('jurnal_umum.jenis_transaksi', 'terikat');

    //         if ($tanggal_mulai && $tanggal_selesai) {
    //             $query_dengan->whereBetween('jurnal_umum.tanggal', [$tanggal_mulai, $tanggal_selesai]);
    //         }

    //         $total_dengan = $query_dengan
    //             ->where('detail_jurnal_umum.debit_kredit', $isPendapatan ? 'kredit' : 'debit')
    //             ->sum('detail_jurnal_umum.nominal');

    //         $data = [
    //             'nama_akun' => $akun->nama_akun,
    //             'total_tanpa' => $total_tanpa,
    //             'total_dengan' => $total_dengan,
    //             'total' => $total_tanpa + $total_dengan,
    //         ];

    //         if ($isPendapatan) {
    //             $pendapatan_all[] = (object) $data;
    //         } else {
    //             $beban_all[] = (object) $data;
    //         }
    //     }

    //     // Total kalkulasi
    //     $total_pendapatan = array_sum(array_column($pendapatan_all, 'total_tanpa'));
    //     $total_pendapatan_terikat = array_sum(array_column($pendapatan_all, 'total_dengan'));
    //     $total_pendapatan_all = $total_pendapatan + $total_pendapatan_terikat;

    //     $total_beban = array_sum(array_column($beban_all, 'total_tanpa'));
    //     $total_beban_terikat = array_sum(array_column($beban_all, 'total_dengan'));
    //     $total_beban_all = $total_beban + $total_beban_terikat;

    //     $kenaikan_penghasilan_komprehensif = $total_pendapatan_all - $total_beban_all;

    //     return view('laporan-komprehensif', compact(
    //         'pendapatan_all', 'beban_all',
    //         'total_pendapatan', 'total_pendapatan_terikat', 'total_pendapatan_all',
    //         'total_beban', 'total_beban_terikat', 'total_beban_all',
    //         'kenaikan_penghasilan_komprehensif',
    //         'tanggal_mulai', 'tanggal_selesai'
    //     ));
    // }



    public function index(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai') ?? date('Y-m-01');
        $tanggal_selesai = $request->input('tanggal_selesai') ?? date('Y-m-d');

        $akunList = DB::table('akun')
            ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
            ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
            ->whereIn('kategori_akun.kategori_akun', ['PENERIMAAN DAN SUMBANGAN', 'Beban'])
            ->select(
                'akun.id_akun',
                'akun.akun AS nama_akun',
                'kategori_akun.kategori_akun',
                'akun.saldo_awal_debit',
                'akun.saldo_awal_kredit'
            )
            ->get();

        $pendapatan_all = [];
        $beban_all = [];

        // Helper function
        $getTotal = function ($akun_id, $jenis_transaksi, $tanggal_mulai, $tanggal_selesai, $isPendapatan) {
            DB::select("CALL get_total_pendapatan_beban(?, ?, ?, ?, ?, @total)", [
                $akun_id, $jenis_transaksi, $tanggal_mulai, $tanggal_selesai, $isPendapatan ? 1 : 0
            ]);
            return DB::select('SELECT @total AS total')[0]->total ?? 0;
        };

        foreach ($akunList as $akun) {
            $isPendapatan = $akun->kategori_akun === 'PENERIMAAN DAN SUMBANGAN';

            $total_tanpa  = $getTotal($akun->id_akun, 'tidak terikat', $tanggal_mulai, $tanggal_selesai, $isPendapatan);
            $total_dengan = $getTotal($akun->id_akun, 'terikat', $tanggal_mulai, $tanggal_selesai, $isPendapatan);

            // Tambahkan saldo awal
            $saldo_awal = $isPendapatan
                ? ($akun->saldo_awal_kredit ?? 0)
                : ($akun->saldo_awal_debit ?? 0);

            $data = (object) [
                'nama_akun' => $akun->nama_akun,
                'total_tanpa' => $total_tanpa,
                'total_dengan' => $total_dengan,
                'total' => $total_tanpa + $total_dengan + $saldo_awal,
            ];

            if ($isPendapatan) {
                $pendapatan_all[] = $data;
            } else {
                $beban_all[] = $data;
            }
        }

        $total_pendapatan = array_sum(array_column($pendapatan_all, 'total_tanpa'));
        $total_pendapatan_terikat = array_sum(array_column($pendapatan_all, 'total_dengan'));
        $total_pendapatan_all = array_sum(array_column($pendapatan_all, 'total'));

        $total_beban = array_sum(array_column($beban_all, 'total_tanpa'));
        $total_beban_terikat = array_sum(array_column($beban_all, 'total_dengan'));
        $total_beban_all = array_sum(array_column($beban_all, 'total'));

        $kenaikan_penghasilan_komprehensif = $total_pendapatan_all - $total_beban_all;

        // Jika request adalah untuk export excel
        if ($request->has('export_excel')) {
            return $this->exportExcel(
                $pendapatan_all, 
                $beban_all,
                $total_pendapatan, 
                $total_pendapatan_terikat, 
                $total_pendapatan_all,
                $total_beban, 
                $total_beban_terikat, 
                $total_beban_all,
                $kenaikan_penghasilan_komprehensif,
                $tanggal_mulai, 
                $tanggal_selesai
            );
        }

        return view('laporan-komprehensif', compact(
            'pendapatan_all', 'beban_all',
            'total_pendapatan', 'total_pendapatan_terikat', 'total_pendapatan_all',
            'total_beban', 'total_beban_terikat', 'total_beban_all',
            'kenaikan_penghasilan_komprehensif',
            'tanggal_mulai', 'tanggal_selesai'
        ));
    }

    private function exportExcel($pendapatan_all, $beban_all, $total_pendapatan, $total_pendapatan_terikat, 
                                $total_pendapatan_all, $total_beban, $total_beban_terikat, $total_beban_all, 
                                $kenaikan_penghasilan_komprehensif, $tanggal_mulai, $tanggal_selesai)
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Sistem Informasi Akuntansi Yayasan Darussalam Batam')
            ->setLastModifiedBy('Sistem Informasi Akuntansi')
            ->setTitle('Laporan Komprehensif')
            ->setSubject('Laporan Komprehensif')
            ->setDescription('Laporan Komprehensif Yayasan Darussalam Batam')
            ->setKeywords('laporan komprehensif')
            ->setCategory('Laporan Keuangan');

        // Add header
        $sheet->setCellValue('A1', 'LAPORAN KOMPREHENSIF YAYASAN DARUSSALAM BATAM');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add period
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($tanggal_mulai)) . ' - ' . date('d/m/Y', strtotime($tanggal_selesai)));
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add some space
        $sheet->setCellValue('A4', 'Akun');
        $sheet->setCellValue('B4', 'Tanpa Pembatasan');
        $sheet->setCellValue('C4', 'Dengan Pembatasan');
        $sheet->setCellValue('D4', 'Jumlah (Rp)');
        
        // Style the headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '000000'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A4:D4')->applyFromArray($headerStyle);
        
        // Add Pendapatan section
        $row = 5;
        $sheet->setCellValue('A' . $row, 'Pendapatan');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');
        
        $row++;
        foreach ($pendapatan_all as $item) {
            $sheet->setCellValue('A' . $row, '   ' . $item->nama_akun);
            $sheet->setCellValue('B' . $row, number_format($item->total_tanpa, 2, ',', '.'));
            $sheet->setCellValue('C' . $row, number_format($item->total_dengan, 2, ',', '.'));
            $sheet->setCellValue('D' . $row, number_format($item->total, 2, ',', '.'));
            
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('Rp #,##0.00');
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('Rp #,##0.00');
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('Rp #,##0.00');
            $sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            $row++;
        }
        
        // Add Total Pendapatan
        $sheet->setCellValue('A' . $row, 'Total Pendapatan');
        $sheet->setCellValue('B' . $row, number_format($total_pendapatan, 2, ',', '.'));
        $sheet->setCellValue('C' . $row, number_format($total_pendapatan_terikat, 2, ',', '.'));
        $sheet->setCellValue('D' . $row, number_format($total_pendapatan_all, 2, ',', '.'));
        
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('Rp #,##0.00');
        $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('Rp #,##0.00');
        $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('Rp #,##0.00');
        $sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Add Beban section
        $row++;
        $sheet->setCellValue('A' . $row, 'Beban');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');
        
        $row++;
        foreach ($beban_all as $item) {
            $sheet->setCellValue('A' . $row, '   ' . $item->nama_akun);
            $sheet->setCellValue('B' . $row, '(Rp ' . number_format($item->total_tanpa, 2, ',', '.') . ')');
            $sheet->setCellValue('C' . $row, '(Rp ' . number_format($item->total_dengan, 2, ',', '.') . ')');
            $sheet->setCellValue('D' . $row, '(Rp ' . number_format($item->total, 2, ',', '.') . ')');
            
            $sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            $row++;
        }
        
        // Add Total Beban
        $sheet->setCellValue('A' . $row, 'Total Beban');
        $sheet->setCellValue('B' . $row, '(Rp ' . number_format($total_beban, 2, ',', '.') . ')');
        $sheet->setCellValue('C' . $row, '(Rp ' . number_format($total_beban_terikat, 2, ',', '.') . ')');
        $sheet->setCellValue('D' . $row, '(Rp ' . number_format($total_beban_all, 2, ',', '.') . ')');
        
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Add Kenaikan Penghasilan
        $row++;
        $sheet->setCellValue('A' . $row, 'KENAIKAN (PENURUNAN) PENGHASILAN KOMPREHENSIF');
        $sheet->setCellValue('D' . $row, 'Rp ' . number_format($kenaikan_penghasilan_komprehensif, 2, ',', '.'));
        
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C6EFCE');
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('D' . $row)->getFont()->setBold(true);
        
        // Add borders to all data
        $sheet->getStyle('A4:D' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Auto size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Add footer
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Sistem Informasi Akuntansi Yayasan Darussalam Batam | ' . date('Y'));
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Create file name
        $fileName = 'Laporan_Komprehensif_' . date('d-m-Y', strtotime($tanggal_mulai)) . '_' . date('d-m-Y', strtotime($tanggal_selesai)) . '.xlsx';
        
        // Redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }





// public function index(Request $request)
//     {
//         $tanggal_mulai = $request->input('tanggal_mulai') ?? date('Y-m-01');
//         $tanggal_selesai = $request->input('tanggal_selesai') ?? date('Y-m-d');

//         $akunList = DB::table('akun')
//             ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
//             ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
//             ->whereIn('kategori_akun.kategori_akun', ['PENERIMAAN DAN SUMBANGAN', 'Beban'])
//             ->select('akun.id_akun', 'akun.akun AS nama_akun', 'kategori_akun.kategori_akun')
//             ->get();

//         $pendapatan_all = [];
//         $beban_all = [];

//         foreach ($akunList as $akun) {
//             $isPendapatan = $akun->kategori_akun === 'PENERIMAAN DAN SUMBANGAN';

//             // Panggil stored procedure untuk total tanpa pembatasan
//             $result_tanpa = DB::select("CALL get_total_pendapatan_beban(?, 'tidak terikat', ?, ?, ?, @total)", [
//                 $akun->id_akun, $tanggal_mulai, $tanggal_selesai, $isPendapatan ? 1 : 0
//             ]);
//             $total_tanpa = DB::select('SELECT @total AS total')[0]->total ?? 0;

//             // Panggil stored procedure untuk total dengan pembatasan
//             $result_dengan = DB::select("CALL get_total_pendapatan_beban(?, 'terikat', ?, ?, ?, @total)", [
//                 $akun->id_akun, $tanggal_mulai, $tanggal_selesai, $isPendapatan ? 1 : 0
//             ]);
//             $total_dengan = DB::select('SELECT @total AS total')[0]->total ?? 0;

//             $data = [
//                 'nama_akun' => $akun->nama_akun,
//                 'total_tanpa' => $total_tanpa,
//                 'total_dengan' => $total_dengan,
//                 'total' => $total_tanpa + $total_dengan,
//             ];

//             if ($isPendapatan) {
//                 $pendapatan_all[] = (object) $data;
//             } else {
//                 $beban_all[] = (object) $data;
//             }
//         }

//         $total_pendapatan = array_sum(array_column($pendapatan_all, 'total_tanpa'));
//         $total_pendapatan_terikat = array_sum(array_column($pendapatan_all, 'total_dengan'));
//         $total_pendapatan_all = $total_pendapatan + $total_pendapatan_terikat;

//         $total_beban = array_sum(array_column($beban_all, 'total_tanpa'));
//         $total_beban_terikat = array_sum(array_column($beban_all, 'total_dengan'));
//         $total_beban_all = $total_beban + $total_beban_terikat;

//         $kenaikan_penghasilan_komprehensif = $total_pendapatan_all - $total_beban_all;

//         return view('laporan-komprehensif', compact(
//             'pendapatan_all', 'beban_all',
//             'total_pendapatan', 'total_pendapatan_terikat', 'total_pendapatan_all',
//             'total_beban', 'total_beban_terikat', 'total_beban_all',
//             'kenaikan_penghasilan_komprehensif',
//             'tanggal_mulai', 'tanggal_selesai'
//         ));
//     }









    // public function index(Request $request)
    // {
    //     $tanggal_mulai = $request->input('tanggal_mulai');
    //     $tanggal_selesai = $request->input('tanggal_selesai');

    //     // Ambil akun kategori Pendapatan dan Beban
    //     $akunPendapatan = DB::table('akun')
    //         ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
    //         ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
    //         ->where('kategori_akun.kategori_akun', 'PENERIMAAN DAN SUMBANGAN')
    //         ->select('akun.id_akun', 'akun.akun AS nama_akun')
    //         ->get();

    //     $akunBeban = DB::table('akun')
    //         ->join('sub_kategori_akun', 'akun.id_sub_kategori_akun', '=', 'sub_kategori_akun.id_sub_kategori_akun')
    //         ->join('kategori_akun', 'sub_kategori_akun.id_kategori_akun', '=', 'kategori_akun.id_kategori_akun')
    //         ->where('kategori_akun.kategori_akun', 'BEBAN')
    //         ->select('akun.id_akun', 'akun.akun AS nama_akun')
    //         ->get();

    //     // Ambil data transaksi tidak terikat
    //     $query = DB::table('detail_jurnal_umum')
    //         ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
    //         ->where('jurnal_umum.jenis_transaksi', 'tidak terikat');

    //     if ($tanggal_mulai && $tanggal_selesai) {
    //         $query->whereBetween('jurnal_umum.tanggal', [$tanggal_mulai, $tanggal_selesai]);
    //     }

    //     $transaksiTidakTerikat = $query
    //         ->select('detail_jurnal_umum.id_akun', 'detail_jurnal_umum.debit_kredit', DB::raw('SUM(nominal) as total'))
    //         ->groupBy('detail_jurnal_umum.id_akun', 'detail_jurnal_umum.debit_kredit')
    //         ->get()
    //         ->groupBy('debit_kredit');

    //     // Ambil data transaksi terikat
    //     $queryTerikat = DB::table('detail_jurnal_umum')
    //         ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
    //         ->where('jurnal_umum.jenis_transaksi', 'terikat');

    //     if ($tanggal_mulai && $tanggal_selesai) {
    //         $queryTerikat->whereBetween('jurnal_umum.tanggal', [$tanggal_mulai, $tanggal_selesai]);
    //     }

    //     $transaksiTerikat = $queryTerikat
    //         ->select('detail_jurnal_umum.id_akun', 'detail_jurnal_umum.debit_kredit', DB::raw('SUM(nominal) as total'))
    //         ->groupBy('detail_jurnal_umum.id_akun', 'detail_jurnal_umum.debit_kredit')
    //         ->get()
    //         ->groupBy('debit_kredit');

    //     // Gabungkan akun pendapatan
    //     $pendapatan_all = $akunPendapatan->map(function ($akun) use ($transaksiTidakTerikat, $transaksiTerikat) {
    //         $total_tanpa = $transaksiTidakTerikat->get('kredit')?->firstWhere('id_akun', $akun->id_akun)?->total ?? 0;
    //         $total_dengan = $transaksiTerikat->get('kredit')?->firstWhere('id_akun', $akun->id_akun)?->total ?? 0;
    //         return (object)[
    //             'nama_akun' => $akun->nama_akun,
    //             'total_tanpa' => $total_tanpa,
    //             'total_dengan' => $total_dengan,
    //             'total' => $total_tanpa + $total_dengan,
    //         ];
    //     });

    //     // Gabungkan akun beban
    //     $beban_all = $akunBeban->map(function ($akun) use ($transaksiTidakTerikat, $transaksiTerikat) {
    //         $total_tanpa = $transaksiTidakTerikat->get('debit')?->firstWhere('id_akun', $akun->id_akun)?->total ?? 0;
    //         $total_dengan = $transaksiTerikat->get('debit')?->firstWhere('id_akun', $akun->id_akun)?->total ?? 0;
    //         return (object)[
    //             'nama_akun' => $akun->nama_akun,
    //             'total_tanpa' => $total_tanpa,
    //             'total_dengan' => $total_dengan,
    //             'total' => $total_tanpa + $total_dengan,
    //         ];
    //     });

    //     // Total ringkasan
    //     $total_pendapatan = $pendapatan_all->sum('total_tanpa');
    //     $total_pendapatan_terikat = $pendapatan_all->sum('total_dengan');
    //     $total_beban = $beban_all->sum('total_tanpa');
    //     $total_beban_terikat = $beban_all->sum('total_dengan');
    //     // $total_penghasilan_komprehensif = $total_pendapatan + $total_pendapatan_terikat + $total_beban + $total_beban_terikat;
    //     $total_penghasilan_komprehensif = ($total_pendapatan + $total_pendapatan_terikat) - ($total_beban + $total_beban_terikat);

    //     $kenaikan_penghasilan_komprehensif = ($total_pendapatan + $total_pendapatan_terikat) - ($total_beban + $total_beban_terikat);

    //     dd($transaksiTerikat);

    //     return view('laporan-komprehensif', compact(
    //         'pendapatan_all', 'beban_all',
    //         'total_pendapatan', 'total_pendapatan_terikat',
    //         'total_beban', 'total_beban_terikat',
    //         'total_penghasilan_komprehensif', 'kenaikan_penghasilan_komprehensif',
    //         'tanggal_mulai', 'tanggal_selesai'
    //     ));
    // }











}
