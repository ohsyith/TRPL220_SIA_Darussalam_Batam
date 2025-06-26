<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Akuntan_Unit;
use App\Models\Unit;
use App\Models\Divisi;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PRRAController extends Controller
{
    
    // public function index(Request $request)
    // {

    //     $berdasarkan = $request->get('berdasarkan', 'akun');
    //     $startDate = $request->input('start_date') ?? date('Y') . '-01-01';
    //     $endDate = $request->input('end_date') ?? date('Y-m-d');
    //     $unitId = $request->unit;
    //     $divisiId = $request->divisi;

    //     $groupedData = [];

    //     if ($berdasarkan === 'kegiatan') {
    //         $kegiatanList = Kegiatan::with([
    //                 'jurnal_umum' => function ($query) use ($startDate, $endDate, $unitId, $divisiId) {
    //                     $query->whereHas('buku_besar');

    //                     if ($startDate && $endDate) {
    //                         $query->whereBetween('tanggal', [$startDate, $endDate]);
    //                     }

    //                     if ($unitId) {
    //                         $query->where('id_unit', $unitId);
    //                     }

    //                     if ($divisiId) {
    //                         $query->where('id_divisi', $divisiId);
    //                     }
    //                 },
    //                 'jurnal_umum.detail_jurnal_umum'
    //             ])
    //             ->get();

    //         $groupedData['KEGIATAN']['Semua Kegiatan'] = [];

    //         foreach ($kegiatanList as $kegiatan) {
    //             $totalRealisasi = 0;

    //             foreach ($kegiatan->jurnal_umum as $jurnal) {
    //                 foreach ($jurnal->detail_jurnal_umum as $detail) {
    //                     if ($detail->debit_kredit === 'debit') {
    //                         $totalRealisasi += $detail->nominal;
    //                     }
    //                 }
    //             }

    //             $budget = DB::table('budget_rapbs_kegiatan')
    //                 ->where('id_kegiatan', $kegiatan->id_kegiatan)
    //                 ->when($unitId, fn($q) => $q->where('id_unit', $unitId))
    //                 ->sum('budget_rapbs_kegiatan');

    //             $groupedData['KEGIATAN']['Semua Kegiatan'][] = (object)[
    //                 'nama_kegiatan' => $kegiatan->kegiatan,
    //                 'budget_rapbs' => $budget,
    //                 'realisasi' => $totalRealisasi,
    //                 'selisih' => $budget - $totalRealisasi,
    //             ];
    //         }

    //     } else {
    //         $akunList = Akun::with([
    //             'sub_kategori_akun.kategori_akun',
    //             'detail_jurnal_umum.jurnal_umum' => function ($query) use ($unitId, $divisiId) {
    //                 if ($unitId) {
    //                     $query->where('id_unit', $unitId);
    //                 }
    //                 if ($divisiId) {
    //                     $query->where('id_divisi', $divisiId);
    //                 }
    //                 $query->whereHas('buku_besar');
    //             }
    //         ])
    //         ->whereHas('sub_kategori_akun.kategori_akun', function ($query) {
    //             $query->whereIn('kategori_akun', ['PENERIMAAN DAN SUMBANGAN', 'BEBAN']);
    //         })
    //         ->get();

    //         foreach ($akunList as $akun) {
    //             $kategori = $akun->sub_kategori_akun->kategori_akun->kategori_akun ?? 'Lainnya';
    //             $subKategori = $akun->sub_kategori_akun->sub_kategori_akun ?? 'Lainnya';

    //             $filteredDetails = $akun->detail_jurnal_umum->filter(function ($detail) {
    //                 return $detail->jurnal_umum && $detail->jurnal_umum->buku_besar;
    //             });

    //             $debit = $filteredDetails->where('debit_kredit', 'debit')->sum('nominal');
    //             $kredit = $filteredDetails->where('debit_kredit', 'kredit')->sum('nominal');
    //             // $saldo = ($akun->saldo_awal_debit - $akun->saldo_awal_kredit) + ($debit - $kredit);
    //             $saldo = $debit - $kredit;

    //             $budget = DB::table('budget_rapbs_akun')
    //                 ->where('id_akun', $akun->id_akun)
    //                 ->when($unitId, fn($q) => $q->where('id_unit', $unitId))
    //                 ->sum('budget_rapbs_akun');

    //             $groupedData[$kategori][$subKategori][] = (object)[
    //                 'nama_akun' => $akun->akun,
    //                 'budget_rapbs' => $budget,
    //                 'realisasi' => $saldo,
    //                 'selisih' => $budget - $saldo,
    //             ];
    //         }
    //     }

    //     $units = Unit::all();
    //     $divisis = Divisi::all();


    //     return view('prra', compact('groupedData', 'berdasarkan', 'units', 'divisis'));
    // }

    public function index(Request $request)
    {
        $user = Auth::user();

        $berdasarkan = $request->get('berdasarkan', 'akun');
        $startDate = $request->input('start_date') ?? date('Y') . '-01-01';
        $endDate = $request->input('end_date') ?? date('Y-m-d');
        $unitId = $request->unit;
        $divisiId = $request->divisi;

         // 🔐 Paksa unit jika role akuntan_unit
        if (!$unitId && $user->role === 'akuntan_unit') {
            $unitId = Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
        }

        $groupedData = [];

        if ($berdasarkan === 'kegiatan') {
            $kegiatanList = Kegiatan::with([
                'jurnal_umum' => function ($query) use ($startDate, $endDate, $unitId, $divisiId) {
                    $query->whereHas('buku_besar');
                    if ($startDate && $endDate) {
                        $query->whereBetween('tanggal', [$startDate, $endDate]);
                    }
                    if ($unitId) {
                        $query->where('id_unit', $unitId);
                    }
                    if ($divisiId) {
                        $query->where('id_divisi', $divisiId);
                    }
                },
                'jurnal_umum.detail_jurnal_umum'
            ])->get();

            $groupedData['KEGIATAN']['Semua Kegiatan'] = [];

            foreach ($kegiatanList as $kegiatan) {
                $totalRealisasi = 0;

                foreach ($kegiatan->jurnal_umum as $jurnal) {
                    foreach ($jurnal->detail_jurnal_umum as $detail) {
                        if ($detail->debit_kredit === 'debit') {
                            $totalRealisasi += $detail->nominal;
                        }
                    }
                }

                $budget = DB::table('budget_rapbs_kegiatan')
                    ->where('id_kegiatan', $kegiatan->id_kegiatan)
                    ->when($unitId, fn($q) => $q->where('id_unit', $unitId))
                    ->sum('budget_rapbs_kegiatan');

                $groupedData['KEGIATAN']['Semua Kegiatan'][] = (object)[
                    'nama_kegiatan' => $kegiatan->kegiatan,
                    'budget_rapbs' => $budget,
                    'realisasi' => $totalRealisasi,
                    'selisih' => $budget - $totalRealisasi,
                ];
            }

        } else {
            $akunList = Akun::with([
                'sub_kategori_akun.kategori_akun',
                'detail_jurnal_umum.jurnal_umum' => function ($query) use ($unitId, $divisiId) {
                    if ($unitId) {
                        $query->where('id_unit', $unitId);
                    }
                    if ($divisiId) {
                        $query->where('id_divisi', $divisiId);
                    }
                    $query->whereHas('buku_besar');
                }
            ])
            ->whereHas('sub_kategori_akun.kategori_akun', function ($query) {
                $query->whereIn('kategori_akun', ['PENERIMAAN DAN SUMBANGAN', 'BEBAN']);
            })
            ->get();

            foreach ($akunList as $akun) {
                $kategori = $akun->sub_kategori_akun->kategori_akun->kategori_akun ?? 'Lainnya';
                $subKategori = $akun->sub_kategori_akun->sub_kategori_akun ?? 'Lainnya';

                $filteredDetails = $akun->detail_jurnal_umum->filter(fn($d) => $d->jurnal_umum && $d->jurnal_umum->buku_besar);
                $debit = $filteredDetails->where('debit_kredit', 'debit')->sum('nominal');
                $kredit = $filteredDetails->where('debit_kredit', 'kredit')->sum('nominal');
                $kategoriAkun = $akun->sub_kategori_akun->kategori_akun->kategori_akun ?? 'Lainnya';

                if ($kategoriAkun === 'PENERIMAAN DAN SUMBANGAN') {
                    $saldo = $kredit; // pendapatan diambil dari sisi kredit
                } elseif ($kategoriAkun === 'BEBAN') {
                    $saldo = $debit; // beban diambil dari sisi debit
                } else {
                    $saldo = 0; // atau dibiarkan kosong
                }


                $budget = DB::table('budget_rapbs_akun')
                    ->where('id_akun', $akun->id_akun)
                    ->when($unitId, fn($q) => $q->where('id_unit', $unitId))
                    ->sum('budget_rapbs_akun');

                $groupedData[$kategori][$subKategori][] = (object)[
                    'nama_akun' => $akun->akun,
                    'budget_rapbs' => $budget,
                    'realisasi' => $saldo,
                    'selisih' => $budget - $saldo,
                ];
            }
        }

        // Jika ada permintaan export
        if ($request->has('export_excel')) {
            return $this->exportExcel($groupedData, $berdasarkan, $startDate, $endDate);
        }

        $units = Unit::all();
        $divisis = Divisi::all();

        return view('prra', compact('groupedData', 'berdasarkan', 'units', 'divisis', 'unitId', 'divisiId'));
    }




    private function exportExcel($groupedData, $berdasarkan, $start, $end)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Yayasan');
        $drawing->setPath(public_path('assets/images/logos/YDB_PNG.png'));
        $drawing->setHeight(100);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(10);
        $drawing->setWorksheet($sheet);

        $judul = "LAPORAN PROYEKSI RENCANA REALISASI ANGGARAN YAYASAN DARUSSALAM BATAM\nPeriode: " . date('d/m/Y', strtotime($start)) . " - " . date('d/m/Y', strtotime($end));
        $sheet->setCellValue('A1', $judul);
        $sheet->mergeCells('A1:E5');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 7;
        $headers = ['Nama', 'Budget RAPBS', 'Realisasi', 'Selisih', 'Persentase Capaian'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}{$row}", $header);
        }

        $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        foreach ($groupedData as $kategori => $sub) {
            $sheet->setCellValue("A{$row}", strtoupper($kategori));
            $sheet->mergeCells("A{$row}:E{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle("A{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
            $row++;

            foreach ($sub as $subKategori => $items) {
                foreach ($items as $item) {
                    $budget = $item->budget_rapbs ?? 0;
                    $realisasi = $item->realisasi ?? 0;
                    $selisih = $item->selisih ?? 0;
                    $persen = $budget != 0 ? ($realisasi / $budget) * 100 : 0;

                    $sheet->setCellValue("A{$row}", $item->nama_akun ?? $item->nama_kegiatan);
                    $sheet->setCellValue("B{$row}", $budget);
                    $sheet->setCellValue("C{$row}", $realisasi);
                    $sheet->setCellValue("D{$row}", $selisih);
                    $sheet->setCellValue("E{$row}", $persen / 100);

                    $sheet->getStyle("B{$row}:D{$row}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('0.00%');
                    $sheet->getStyle("B{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $row++;
                }
            }
        }

        $sheet->getStyle("A7:E" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->setCellValue("A{$row}", 'Sistem Informasi Akuntansi | ' . date('Y'));
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $fileName = 'Laporan_PRRA_' . date('d-m-Y', strtotime($start)) . '_sd_' . date('d-m-Y', strtotime($end)) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }




}
