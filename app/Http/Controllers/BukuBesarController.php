<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Akun;
use App\Models\Divisi;
use App\Models\Buku_Besar;
use App\Models\Akuntan_Unit;
use App\Models\Jurnal_Umum ;
use Illuminate\Http\Request;
use App\Models\Akuntan_Divisi;
use App\Models\Detail_Jurnal_Umum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Pagination\LengthAwarePaginator;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class BukuBesarController extends Controller
{
    


    // public function index(Request $request)
    // {
    //     $akun_id = $request->filled('akun') ? $request->akun : 1;
    //     $start_date = $request->filled('start_date') ? $request->start_date : null;
    //     $end_date = $request->filled('end_date') ? $request->end_date : null;

    //     $user = Auth::user();

    //     // Gunakan inputan jika tersedia
    //     $id_unit = $request->filled('id_unit') ? $request->id_unit : null;
    //     $id_divisi = $request->filled('id_divisi') ? $request->id_divisi : null;

    //     // Jika input tidak diberikan, fallback ke role user
    //     if (!$id_unit && !$id_divisi) {
    //         if ($user->role === 'akuntan_unit') {
    //             $id_unit = Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
    //         } elseif ($user->role === 'akuntan_divisi') {
    //             $id_divisi = Akuntan_Divisi::where('id_akuntan_divisi', $user->id_user)->value('id_divisi');
    //         }
    //     }

    //     // Ambil data dari stored procedure
    //     $detail_jurnal = DB::select(
    //         'CALL laporan_buku_besar(?, ?, ?, ?, ?)', 
    //         [$akun_id, $start_date, $end_date, $id_unit, $id_divisi]
    //     );

    //     // Ubah ke collection agar bisa pakai filter
    //     $detail_jurnal = collect($detail_jurnal);

    //     // Filter pencarian
    //     if ($request->filled('search')) {
    //         $search = strtolower($request->search);
    //         $detail_jurnal = $detail_jurnal->filter(function ($item) use ($search) {
    //             return str_contains(strtolower($item->no_bukti), $search)
    //                 || str_contains(strtolower($item->keterangan), $search)
    //                 || str_contains(strtolower($item->akun), $search)
    //                 || str_contains(strtolower($item->unit ?? ''), $search)
    //                 || str_contains(strtolower($item->divisi ?? ''), $search)
    //                 || str_contains(strtolower($item->kode_sumbangan ?? ''), $search)
    //                 || str_contains(strtolower($item->kode_ph ?? ''), $search);
    //         });
    //     }

    //     // Hitung total
    //     $total_debit = $detail_jurnal->where('debit_kredit', 'debit')->sum('nominal');
    //     $total_kredit = $detail_jurnal->where('debit_kredit', 'kredit')->sum('nominal');

    //     $akunList = Akun::all();

    //     if ($request->has('export_excel')) {
    //         return $this->exportExcel($detail_jurnal, $akun_id, $start_date, $end_date);
    //     }

    //     // Ambil akun yang dipilih
    //     $akun = Akun::find($akun_id);

    //     // Hitung saldo awal
    //     $saldo_awal = 0;
    //     if ($akun) {
    //         $saldo_awal = ($akun->saldo_awal_debit ?? 0) - ($akun->saldo_awal_kredit ?? 0);
    //     }

    //     return view('buku-besar', compact(
    //         'detail_jurnal', 
    //         'akunList', 
    //         'total_debit', 
    //         'total_kredit', 
    //         'saldo_awal',
    //         'id_unit',
    //         'id_divisi'
    //     ));
    // }


    public function index(Request $request)
    {
        $akun_id = $request->filled('akun') ? $request->akun : 1;
        $start_date = $request->filled('start_date') ? $request->start_date : date('Y-01-01');
        $end_date = $request->filled('end_date') ? $request->end_date : date('Y-m-d');

        $user = Auth::user();

        $id_unit = $request->filled('id_unit') ? $request->id_unit : null;
        $id_divisi = $request->filled('id_divisi') ? $request->id_divisi : null;

        if (!$id_unit && !$id_divisi) {
            if ($user->role === 'akuntan_unit') {
                $id_unit = Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
            } elseif ($user->role === 'akuntan_divisi') {
                $id_divisi = Akuntan_Divisi::where('id_akuntan_divisi', $user->id_user)->value('id_divisi');
            }
        }

        // Panggil prosedur dan jadikan koleksi
        $detail_jurnal = collect(DB::select(
            'CALL laporan_buku_besar(?, ?, ?, ?, ?)', 
            [$akun_id, $start_date, $end_date, $id_unit, $id_divisi]
        ));

        // Filter pencarian
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $detail_jurnal = $detail_jurnal->filter(function ($item) use ($search) {
                return str_contains(strtolower($item->no_bukti), $search)
                    || str_contains(strtolower($item->keterangan), $search)
                    || str_contains(strtolower($item->akun), $search)
                    || str_contains(strtolower($item->unit ?? ''), $search)
                    || str_contains(strtolower($item->divisi ?? ''), $search)
                    || str_contains(strtolower($item->kode_sumbangan ?? ''), $search)
                    || str_contains(strtolower($item->kode_ph ?? ''), $search);
            });
        }

        // Hitung total debit dan kredit sebelum paginasi
        $total_debit = $detail_jurnal->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $detail_jurnal->where('debit_kredit', 'kredit')->sum('nominal');

        // ✅ Manual paginasi
        $perPage = $request->input('per_page', 20);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedData = $detail_jurnal->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedData = new LengthAwarePaginator(
            $pagedData,
            $detail_jurnal->count(),
            $perPage,   
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $akunList = Akun::all();
        $akun = Akun::find($akun_id);

        $saldo_awal = 0;
        $saldo_akhir = 0;
        $kategori = null;

        if ($akun) {
            $kategori = $akun->sub_kategori_akun->kategori_akun->kategori_akun ?? null;

            if (in_array($kategori, ['KEWAJIBAN', 'ASET NETO', 'PENERIMAAN DAN SUMBANGAN'])) {
                // Saldo normal kredit
                $saldo_awal = ($akun->saldo_awal_kredit ?? 0) - ($akun->saldo_awal_debit ?? 0);
                $saldo_akhir = $saldo_awal - $total_debit + $total_kredit;
            } else {
                // Saldo normal debit
                $saldo_awal = ($akun->saldo_awal_debit ?? 0) - ($akun->saldo_awal_kredit ?? 0);
                $saldo_akhir = $saldo_awal + $total_debit - $total_kredit;
            }
        }

        if ($request->has('export_excel')) {
            return $this->exportExcelBukuBesar($akun, $detail_jurnal, $saldo_awal, $saldo_akhir, $start_date, $end_date);
        }


        return view('buku-besar', compact(
            'paginatedData',
            'akunList',
            'total_debit',
            'total_kredit',
            'saldo_awal',
            'saldo_akhir',
            'id_unit',
            'id_divisi'
        ));
    }


    // private function exportExcel($detail_jurnal, $akun_id, $start_date, $end_date)
    // {
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     // Header utama
    //     $sheet->setCellValue('A1', 'BUKU BESAR YAYASAN DARUSSALAM BATAM');
    //     $sheet->mergeCells('A1:G1');
    //     $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    //     $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     // Periode
    //     $periodeText = 'Periode: ';
    //     if ($start_date && $end_date) {
    //         $periodeText .= date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date));
    //     } else {
    //         $periodeText .= 'Semua Periode';
    //     }
    //     $sheet->setCellValue('A2', $periodeText);
    //     $sheet->mergeCells('A2:G2');
    //     $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     // Header tabel
    //     $sheet->setCellValue('A4', 'Tanggal');
    //     $sheet->setCellValue('B4', 'No Bukti');
    //     $sheet->setCellValue('C4', 'Keterangan');
    //     $sheet->setCellValue('D4', 'Unit');
    //     $sheet->setCellValue('E4', 'Divisi');
    //     $sheet->setCellValue('F4', 'Debit (Rp)');
    //     $sheet->setCellValue('G4', 'Kredit (Rp)');

    //     $sheet->getStyle('A4:G4')->applyFromArray([
    //         'font' => ['bold' => true],
    //         'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
    //         'font' => ['color' => ['rgb' => 'FFFFFF']],
    //         'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    //     ]);
    //     $sheet->getStyle('A4:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     $row = 5;
    //     $running_saldo = 0;

    //     foreach ($detail_jurnal as $item) {
    //         $debit = ($item->debit_kredit === 'debit') ? $item->nominal : 0;
    //         $kredit = ($item->debit_kredit === 'kredit') ? $item->nominal : 0;
    //         $running_saldo += $debit - $kredit;

    //         $sheet->setCellValue('A' . $row, date('d/m/Y', strtotime($item->tanggal)));
    //         $sheet->setCellValue('B' . $row, $item->no_bukti);
    //         $sheet->setCellValue('C' . $row, $item->keterangan);
    //         $sheet->setCellValue('D' . $row, $item->unit ?? '-');
    //         $sheet->setCellValue('E' . $row, $item->divisi ?? '-');
    //         $sheet->setCellValue('F' . $row, $debit);
    //         $sheet->setCellValue('G' . $row, $kredit);

    //         // Format angka dan alignment
    //         $sheet->getStyle("F{$row}:G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
    //         $sheet->getStyle("F{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    //         $row++;
    //     }

    //     // Total debit dan kredit
    //     $total_debit = $detail_jurnal->where('debit_kredit', 'debit')->sum('nominal');
    //     $total_kredit = $detail_jurnal->where('debit_kredit', 'kredit')->sum('nominal');

    //     $sheet->setCellValue('A' . $row, 'Total');
    //     $sheet->mergeCells("A{$row}:E{$row}");
    //     $sheet->setCellValue('F' . $row, $total_debit);
    //     $sheet->setCellValue('G' . $row, $total_kredit);

    //     $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
    //     $sheet->getStyle("F{$row}:G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
    //     $sheet->getStyle("F{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    //     // Border seluruh tabel
    //     $sheet->getStyle("A4:G{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    //     // Auto size kolom
    //     foreach (range('A', 'G') as $col) {
    //         $sheet->getColumnDimension($col)->setAutoSize(true);
    //     }

    //     // Footer
    //     $row += 2;
    //     $sheet->setCellValue('A' . $row, 'Sistem Informasi Akuntansi Yayasan Darussalam Batam | ' . date('Y'));
    //     $sheet->mergeCells("A{$row}:G{$row}");
    //     $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     // Output file
    //     $fileName = 'Buku_Besar_Akun_' . $akun_id . '_' . ($start_date ? date('d-m-Y', strtotime($start_date)) : 'awal') . '_' . ($end_date ? date('d-m-Y', strtotime($end_date)) : 'akhir') . '.xlsx';
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header("Content-Disposition: attachment;filename=\"{$fileName}\"");
    //     header('Cache-Control: max-age=0');

    //     $writer = new Xlsx($spreadsheet);
    //     $writer->save('php://output');
    //     exit;
    // }


    private function exportExcelBukuBesar($akun, $data, $saldo_awal, $saldo_akhir, $start_date, $end_date)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 🔁 Merge cell A1:G4
        $sheet->mergeCells('A1:G4');

        // 🖼️ Logo
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path('assets/images/logos/YDB_PNG.png'));
        $drawing->setHeight(150);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);

        // 📝 RichText Judul
        $richText = new RichText();
        $judulText = $richText->createTextRun("LAPORAN BUKU BESAR YAYASAN DARUSSALAM BATAM\n");
        $judulText->getFont()->setBold(true)->setSize(14);

        $akunText = $richText->createTextRun("Akun: {$akun->kode_akun} | {$akun->akun}\n");
        $akunText->getFont()->setBold(true)->setSize(12);

        $periodeText = $richText->createTextRun(
            "Periode " . Carbon::parse($start_date)->translatedFormat('d F Y') .
            " s.d. " . Carbon::parse($end_date)->translatedFormat('d F Y')
        );
        $periodeText->getFont()->setSize(10);

        $sheet->setCellValue('A1', $richText);
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension('1')->setRowHeight(80);

        // 🧮 Saldo Awal (Baris 6)
        $sheet->mergeCells('A6:E6');
        $sheet->setCellValue('A6', 'Saldo Awal');
        $sheet->setCellValue('F6', $saldo_awal);
        $sheet->getStyle('A6:F6')->getFont()->setBold(true);
        $sheet->getStyle('F6')->getNumberFormat()->setFormatCode('#,##0');

        // 🧮 Saldo Akhir (Baris 7)
        $sheet->mergeCells('A7:E7');
        $sheet->setCellValue('A7', 'Saldo Akhir');
        $sheet->setCellValue('F7', $saldo_akhir);
        $sheet->getStyle('A7:F7')->getFont()->setBold(true);
        $sheet->getStyle('F7')->getNumberFormat()->setFormatCode('#,##0');

        // 📋 Header Tabel (Baris 9)
        $header = ['Tanggal', 'No Bukti', 'Keterangan', 'Unit', 'Divisi', 'Debit', 'Kredit'];
        $sheet->fromArray($header, null, 'A9');
        $sheet->getStyle('A9:G9')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
            'font' => ['color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // 📄 Isi Data (Mulai baris 10)
        $row = 10;
        foreach ($data as $item) {
            $sheet->setCellValue("A{$row}", $item->tanggal ?? '')
                ->setCellValue("B{$row}", $item->no_bukti ?? '')
                ->setCellValue("C{$row}", $item->keterangan ?? '')
                ->setCellValue("D{$row}", $item->unit ?? '')
                ->setCellValue("E{$row}", $item->divisi ?? '')
                ->setCellValue("F{$row}", $item->debit_kredit === 'debit' ? $item->nominal : null)
                ->setCellValue("G{$row}", $item->debit_kredit === 'kredit' ? $item->nominal : null);

            $sheet->getStyle("F{$row}:G{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');
            $row++;
        }

        // 🖋️ Border dan Autosize
        $sheet->getStyle("A9:G" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ⬇️ Output
        $filename = 'Buku_Besar_' . $akun->kode_akun . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }





    public function create()
    {
        //
    }


    public function store(Request $request)
    {

        $id_user_login = Auth::user()->id_user;
        DB::statement("SET @current_user_id = $id_user_login");
    
        $request->validate([
            'id_jurnal_umum' => 'required|exists:jurnal_umum,id_jurnal_umum',
        ]);
    
        if (Buku_Besar::where('id_jurnal_umum', $request->id_jurnal_umum)->exists()) {
            return redirect()->back()->with('error', 'Jurnal sudah diposting.');
        }
    
        Buku_Besar::create([
            'id_jurnal_umum' => $request->id_jurnal_umum,
        ]);
    
        return redirect()->back()->with('success', 'Berhasil diposting ke Buku Besar.');
    }
    

    public function postingSemua(Request $request)
    {
        $id_user_login = Auth::user()->id_user;
        DB::statement("SET @current_user_id = $id_user_login");

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = Jurnal_Umum::query();

        // Jika ada filter tanggal, tambahkan kondisi whereBetween
        if ($start_date && $end_date) {
            $query->whereBetween('tanggal', [$start_date, $end_date]);
        }

        // Ambil ID jurnal yang belum diposting dan sesuai rentang tanggal
        $jurnalBelumDiposting = $query->whereNotIn('id_jurnal_umum', function ($query) {
            $query->select('id_jurnal_umum')->from('buku_besar')->whereNotNull('id_jurnal_umum');
        })->pluck('id_jurnal_umum');

        foreach ($jurnalBelumDiposting as $id_jurnal) {
            Buku_Besar::create([
                'id_jurnal_umum' => $id_jurnal,
            ]);
        }

        return redirect()->back()->with('success', 'Semua jurnal dalam rentang tanggal berhasil diposting ke Buku Besar.');
    }



    public function show(Buku_Besar $buku_Besar)
    {
        //
    }

    
    public function edit(Buku_Besar $buku_Besar)
    {
        //
    }

    
    public function update(Request $request, Buku_Besar $buku_Besar)
    {
        //
    }

    
    public function destroy(Buku_Besar $buku_Besar)
    {
        //
    }

}
