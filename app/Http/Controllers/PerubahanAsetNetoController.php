<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Perubahan_Aset_Neto;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PerubahanAsetNetoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start_date ?? date('Y-01-01'); // Default to start of current year
        $end = $request->end_date ?? now()->t
        oDateString();
        $prev_year_start = date('Y-01-01', strtotime('-1 year', strtotime($start)));
        $prev_year_end = date('Y-12-31', strtotime('-1 year', strtotime($end)));

        // Get all asset accounts
        $aset_neto_accounts = Akun::whereHas('sub_kategori_akun.kategori_akun', function($query) {
            $query->where('nama_kategori', 'like', '%Aset Neto%');
        })->with(['sub_kategori_akun.kategori_akun'])->get();

        // Group accounts by restriction type (With or Without Restriction)
        $dengan_pembatasan = $aset_neto_accounts->filter(function($akun) {
            return stripos($akun->nama_akun, 'dengan pembatasan') !== false || 
                   stripos($akun->sub_kategori_akun->nama_sub_kategori, 'dengan pembatasan') !== false;
        });

        $tanpa_pembatasan = $aset_neto_accounts->filter(function($akun) {
            return stripos($akun->nama_akun, 'tanpa pembatasan') !== false || 
                   stripos($akun->sub_kategori_akun->nama_sub_kategori, 'tanpa pembatasan') !== false ||
                   (stripos($akun->nama_akun, 'dengan pembatasan') === false && 
                    stripos($akun->sub_kategori_akun->nama_sub_kategori, 'dengan pembatasan') === false);
        });

        // Get opening balances (from previous period)
        $saldo_awal_dengan_pembatasan = $this->getSaldoAwal($dengan_pembatasan, $prev_year_start, $prev_year_end);
        $saldo_awal_tanpa_pembatasan = $this->getSaldoAwal($tanpa_pembatasan, $prev_year_start, $prev_year_end);

        // Get changes from previous period (if any special adjustments)
        $perubahan_periode_lalu_dengan_pembatasan = 0;
        $perubahan_periode_lalu_tanpa_pembatasan = 0;

        // Get current period changes
        $perubahan_periode_dengan_pembatasan = $this->getPerubahanPeriode($dengan_pembatasan, $start, $end);
        $perubahan_periode_tanpa_pembatasan = $this->getPerubahanPeriode($tanpa_pembatasan, $start, $end);

        // Calculate ending balances
        $saldo_akhir_dengan_pembatasan = $saldo_awal_dengan_pembatasan + $perubahan_periode_lalu_dengan_pembatasan + $perubahan_periode_dengan_pembatasan;
        $saldo_akhir_tanpa_pembatasan = $saldo_awal_tanpa_pembatasan + $perubahan_periode_lalu_tanpa_pembatasan + $perubahan_periode_tanpa_pembatasan;
        $total_saldo_akhir = $saldo_akhir_dengan_pembatasan + $saldo_akhir_tanpa_pembatasan;

        $data = [
            'start_date' => $start,
            'end_date' => $end,
            'dengan_pembatasan' => [
                'saldo_awal' => $saldo_awal_dengan_pembatasan,
                'perubahan_periode_lalu' => $perubahan_periode_lalu_dengan_pembatasan,
                'perubahan_periode_berjalan' => $perubahan_periode_dengan_pembatasan,
                'saldo_akhir' => $saldo_akhir_dengan_pembatasan
            ],
            'tanpa_pembatasan' => [
                'saldo_awal' => $saldo_awal_tanpa_pembatasan,
                'perubahan_periode_lalu' => $perubahan_periode_lalu_tanpa_pembatasan,
                'perubahan_periode_berjalan' => $perubahan_periode_tanpa_pembatasan,
                'saldo_akhir' => $saldo_akhir_tanpa_pembatasan
            ],
            'total_saldo_akhir' => $total_saldo_akhir
        ];

        if ($request->has('export_excel')) {
            return $this->exportExcel($data, $start, $end);
        }

        return view('perubahan-aset-neto', compact('data'));
    }

    private function getSaldoAwal($akun_list, $start_date, $end_date)
    {
        $total = 0;

        foreach ($akun_list as $akun) {
            $saldo_awal_debit = $akun->saldo_awal_debit ?? 0;
            $saldo_awal_kredit = $akun->saldo_awal_kredit ?? 0;

            $mutasi = DB::table('detail_jurnal_umum as dju')
                ->join('jurnal_umum as ju', 'dju.id_jurnal_umum', '=', 'ju.id_jurnal_umum')
                ->selectRaw("
                    SUM(CASE WHEN dju.debit_kredit = 'debit' THEN dju.nominal ELSE 0 END) AS total_debit,
                    SUM(CASE WHEN dju.debit_kredit = 'kredit' THEN dju.nominal ELSE 0 END) AS total_kredit
                ")
                ->where('dju.id_akun', $akun->id_akun)
                ->whereBetween('ju.tanggal', [$start_date, $end_date])
                ->first();

            $mutasi_debit = $mutasi->total_debit ?? 0;
            $mutasi_kredit = $mutasi->total_kredit ?? 0;

            // For asset accounts, typically: Debit (increase) - Credit (decrease)
            $total += ($saldo_awal_debit + $mutasi_debit) - ($saldo_awal_kredit + $mutasi_kredit);
        }

        return $total;
    }

    private function getPerubahanPeriode($akun_list, $start_date, $end_date)
    {
        $total = 0;

        foreach ($akun_list as $akun) {
            $mutasi = DB::table('detail_jurnal_umum as dju')
                ->join('jurnal_umum as ju', 'dju.id_jurnal_umum', '=', 'ju.id_jurnal_umum')
                ->selectRaw("
                    SUM(CASE WHEN dju.debit_kredit = 'debit' THEN dju.nominal ELSE 0 END) AS total_debit,
                    SUM(CASE WHEN dju.debit_kredit = 'kredit' THEN dju.nominal ELSE 0 END) AS total_kredit
                ")
                ->where('dju.id_akun', $akun->id_akun)
                ->whereBetween('ju.tanggal', [$start_date, $end_date])
                ->first();

            $total_debit = $mutasi->total_debit ?? 0;
            $total_kredit = $mutasi->total_kredit ?? 0;

            // For asset accounts, typically: Debit (increase) - Credit (decrease)
            $total += $total_debit - $total_kredit;
        }

        return $total;
    }

    private function exportExcel($data, $start_date, $end_date)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set spreadsheet title
        $sheet->setTitle('Perubahan Aset Neto');

        // Headers
        $sheet->setCellValue('A1', 'YAYASAN DARUSSALAM BATAM');
        $sheet->setCellValue('A2', 'LAPORAN PERUBAHAN ASET NETO');
        $sheet->setCellValue('A3', 'Periode Laporan ' . date('d M Y', strtotime($start_date)) . ' - ' . date('d M Y', strtotime($end_date)));
        
        // Style for headers
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        $sheet->mergeCells('A3:C3');
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Table headers and data for "Dengan Pembatasan"
        $sheet->setCellValue('A5', 'Aset Neto Dengan Pembatasan Sumber Daya');
        $sheet->setCellValue('A6', 'Saldo Awal');
        $sheet->setCellValue('C6', $data['dengan_pembatasan']['saldo_awal']);
        $sheet->setCellValue('A7', 'Kenaikan (Penurunan) Aset Neto Periode Lalu');
        $sheet->setCellValue('C7', $data['dengan_pembatasan']['perubahan_periode_lalu']);
        $sheet->setCellValue('A8', 'Kenaikan (Penurunan) Aset Neto Periode Berjalan');
        $sheet->setCellValue('C8', $data['dengan_pembatasan']['perubahan_periode_berjalan']);
        $sheet->setCellValue('A9', 'Saldo Akhir Aset Neto Dengan Pembatasan');
        $sheet->setCellValue('C9', $data['dengan_pembatasan']['saldo_akhir']);

        // Table headers and data for "Tanpa Pembatasan"
        $sheet->setCellValue('A11', 'Aset Neto Tanpa Pembatasan Dengan Sumber Daya');
        $sheet->setCellValue('A12', 'Saldo Awal');
        $sheet->setCellValue('C12', $data['tanpa_pembatasan']['saldo_awal']);
        $sheet->setCellValue('A13', 'Kenaikan (Penurunan) Aset Neto Periode Lalu');
        $sheet->setCellValue('C13', $data['tanpa_pembatasan']['perubahan_periode_lalu']);
        $sheet->setCellValue('A14', 'Kenaikan (Penurunan) Aset Neto Periode Berjalan');
        $sheet->setCellValue('C14', $data['tanpa_pembatasan']['perubahan_periode_berjalan']);
        $sheet->setCellValue('A15', 'Saldo Akhir Aset Neto Tanpa Pembatasan');
        $sheet->setCellValue('C15', $data['tanpa_pembatasan']['saldo_akhir']);

        // Total
        $sheet->setCellValue('A17', 'Total Saldo Akhir Aset Neto');
        $sheet->setCellValue('C17', $data['total_saldo_akhir']);
        $sheet->getStyle('A17:C17')->getFont()->setBold(true);

        // Signatures section
        $sheet->setCellValue('A19', 'Batam, ' . date('d M Y', strtotime($end_date)));
        $sheet->setCellValue('A20', 'mengetahui,');
        $sheet->setCellValue('C20', 'Dibuat Oleh :');
        
        $sheet->setCellValue('A21', 'Ketua Yayasan');
        $sheet->setCellValue('B21', 'Bendahara yayasan');
        $sheet->setCellValue('C21', 'Kepala Dep. Finance');
        $sheet->setCellValue('D21', 'Bagian Keuangan');
        
        $sheet->setCellValue('A24', 'H. Asep Rohmat, ST');
        $sheet->setCellValue('B24', 'Marno, S.Pd');
        $sheet->setCellValue('C24', 'Kurnia Candrawati, SE');
        $sheet->setCellValue('D24', 'Umi rahayu, SHI');
        
        $sheet->setCellValue('B26', 'Diperiksa Oleh :');

        // Format number cells
        $sheet->getStyle('C6:C17')->getNumberFormat()->setFormatCode('#,##0');

        // Auto size columns
        foreach(range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create response
        $fileName = 'Perubahan_Aset_Neto_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Perubahan_Aset_Neto $perubahan_Aset_Neto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Perubahan_Aset_Neto $perubahan_Aset_Neto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Perubahan_Aset_Neto $perubahan_Aset_Neto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perubahan_Aset_Neto $perubahan_Aset_Neto)
    {
        //
    }
}
