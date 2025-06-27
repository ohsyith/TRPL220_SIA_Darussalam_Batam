<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Akun;
use App\Models\Unit;
use App\Models\Divisi;
use App\Models\Jurnal_Umum;
use Illuminate\Http\Request;
use App\Models\Kategori_Akun;
use App\Models\Sub_Kategori_Akun;
use App\Models\Detail_Jurnal_Umum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Komprehensif_Akhir_Tahun;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ArusKasController extends Controller
{



    public function index3(Request $request)
    {
        $user = Auth::user();

        $id_unit = $request->unit;
        $id_divisi = $request->divisi;

        // Jika user akuntan_unit dan tidak memilih unit, pakai unit dari akuntan_unit
        if (!$id_unit && $user->role === 'akuntan_unit') {
            $id_unit = \App\Models\Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
        }

        $filters = [
            'id_unit' => $id_unit,
            'id_divisi' => $id_divisi,
            'start_date' => $request->input('start_date', date('Y-01-01')),
            'end_date' => $request->input('end_date', date('Y-m-d')),
        ];


        $units = Unit::all();
        $divisis = Divisi::all();
        $tahun = $request->input('tahun', date('Y'));

        //kompre
        $laba_bersih = $this->hitungLabaBersih($tahun, $filters);


        //aktivitas operasional
        $persediaan_perlengkapan_kantor = $this->hitung_persediaan_perlengkapan_kantor($tahun, $filters);
        $persediaan_perlengkapan_asrama = $this->hitung_persediaan_perlengkapan_asrama($tahun, $filters);
        $persediaan_atk = $this->hitung_persediaan_atk($tahun, $filters);
        $persediaan_lainnya = $this->hitung_persediaan_lainnya($tahun, $filters);

        $piutang_rekanan = $this->hitung_piutang_rekanan($tahun, $filters);
        $piutang_kegiatan = $this->hitung_piutang_kegiatan($tahun, $filters);
        $piutang_karyawan = $this->hitung_piutang_karyawan($tahun, $filters);
        $piutang_sumbangan = $this->hitung_piutang_sumbangan($tahun, $filters);
        $piutang_lainnya = $this->hitung_piutang_lainnya($tahun, $filters);

        $sewa_dibayar_dimuka = $this->hitung_sewa_dibayar_dimuka($tahun, $filters);
        $tabungan_pensiun_karyawan = $this->hitung_tabungan_pensiun_karyawan($tahun, $filters);
        $pajak_dibayar_dimuka = $this->hitung_pajak_dibayar_dimuka($tahun, $filters);
        $hutang_jangka_pendek = $this->hitung_hutang_jangka_pendek($tahun, $filters);


        //aktivitas investasi
        $aset_tetap = $this->hitung_aset_tetap($tahun, $filters);


        //aktivitas pendanaan
        $kewajiban_jangka_panjang = $this->hitung_kewajiban_jangka_panjang($tahun, $filters);
        $aset_neto = $this->hitung_aset_neto($tahun, $filters);


        //saldo akun
        $saldo_kas = $this->hitung_saldo_kas($tahun, $filters);



        if ($request->has('export_excel')) {
            return $this->exportExcelArusKas($request, [
                'tahun' => $tahun,
                'filters' => $filters,

                'laba_bersih' => $laba_bersih,

                'persediaan_perlengkapan_kantor' => $persediaan_perlengkapan_kantor['tahun_ini'],
                'persediaan_perlengkapan_kantor_lalu' => $persediaan_perlengkapan_kantor['tahun_lalu'],

                'persediaan_perlengkapan_asrama' => $persediaan_perlengkapan_asrama['tahun_ini'],
                'persediaan_perlengkapan_asrama_lalu' => $persediaan_perlengkapan_asrama['tahun_lalu'],

                'persediaan_atk' => $persediaan_atk['tahun_ini'],
                'persediaan_atk_lalu' => $persediaan_atk['tahun_lalu'],

                'persediaan_lainnya' => $persediaan_lainnya['tahun_ini'],
                'persediaan_lainnya_lalu' => $persediaan_lainnya['tahun_lalu'],

                'piutang_rekanan' => $piutang_rekanan['tahun_ini'],
                'piutang_rekanan_lalu' => $piutang_rekanan['tahun_lalu'],

                'piutang_kegiatan' => $piutang_kegiatan['tahun_ini'],
                'piutang_kegiatan_lalu' => $piutang_kegiatan['tahun_lalu'],

                'piutang_karyawan' => $piutang_karyawan['tahun_ini'],
                'piutang_karyawan_lalu' => $piutang_karyawan['tahun_lalu'],

                'piutang_sumbangan' => $piutang_sumbangan['tahun_ini'],
                'piutang_sumbangan_lalu' => $piutang_sumbangan['tahun_lalu'],

                'piutang_lainnya' => $piutang_lainnya['tahun_ini'],
                'piutang_lainnya_lalu' => $piutang_lainnya['tahun_lalu'],

                'sewa_dibayar_dimuka' => $sewa_dibayar_dimuka['tahun_ini'],
                'sewa_dibayar_dimuka_lalu' => $sewa_dibayar_dimuka['tahun_lalu'],

                'tabungan_pensiun_karyawan' => $tabungan_pensiun_karyawan['tahun_ini'],
                'tabungan_pensiun_karyawan_lalu' => $tabungan_pensiun_karyawan['tahun_lalu'],

                'pajak_dibayar_dimuka' => $pajak_dibayar_dimuka['tahun_ini'],
                'pajak_dibayar_dimuka_lalu' => $pajak_dibayar_dimuka['tahun_lalu'],

                'hutang_jangka_pendek' => $hutang_jangka_pendek['tahun_ini'],
                'hutang_jangka_pendek_lalu' => $hutang_jangka_pendek['tahun_lalu'],

                'aset_tetap' => $aset_tetap['tahun_ini'],
                'aset_tetap_lalu' => $aset_tetap['tahun_lalu'],

                'kewajiban_jangka_panjang' => $kewajiban_jangka_panjang['tahun_ini'],
                'kewajiban_jangka_panjang_lalu' => $kewajiban_jangka_panjang['tahun_lalu'],

                'aset_neto' => $aset_neto['tahun_ini'],
                'aset_neto_lalu' => $aset_neto['tahun_lalu'],

                'saldo_kas' => $saldo_kas['tahun_ini'],
                'saldo_kas_lalu' => $saldo_kas['tahun_lalu'],
            ]);
        }




        return view('arus-kas3', [
            'tahun' => $tahun,
            'units' => $units,
            'divisis' => $divisis,
            'id_unit' => $id_unit,
            'id_divisi' => $id_divisi,
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],


            //kompre
                'laba_bersih_tahun_ini' => $laba_bersih['tahun_ini'],
                'laba_bersih_tahun_lalu' => $laba_bersih['tahun_lalu'],


            //aktivitas operasional
                'persediaan_perlengkapan_kantor' => $persediaan_perlengkapan_kantor['tahun_ini'],
                'persediaan_perlengkapan_kantor_lalu' => $persediaan_perlengkapan_kantor['tahun_lalu'],

                'persediaan_perlengkapan_asrama' => $persediaan_perlengkapan_asrama['tahun_ini'],
                'persediaan_perlengkapan_asrama_lalu' => $persediaan_perlengkapan_asrama['tahun_lalu'],
                
                'persediaan_atk' => $persediaan_atk['tahun_ini'],
                'persediaan_atk_lalu' => $persediaan_atk['tahun_lalu'],
                
                'persediaan_lainnya' => $persediaan_lainnya['tahun_ini'],
                'persediaan_lainnya_lalu' => $persediaan_lainnya['tahun_lalu'],

                'piutang_rekanan' => $piutang_rekanan['tahun_ini'],
                'piutang_rekanan_lalu' => $piutang_rekanan['tahun_lalu'],

                'piutang_kegiatan' => $piutang_kegiatan['tahun_ini'],
                'piutang_kegiatan_lalu' => $piutang_kegiatan['tahun_lalu'],

                'piutang_karyawan' => $piutang_karyawan['tahun_ini'],
                'piutang_karyawan_lalu' => $piutang_karyawan['tahun_lalu'],

                'piutang_sumbangan' => $piutang_sumbangan['tahun_ini'],
                'piutang_sumbangan_lalu' => $piutang_sumbangan['tahun_lalu'],

                'piutang_lainnya' => $piutang_lainnya['tahun_ini'],
                'piutang_lainnya_lalu' => $piutang_lainnya['tahun_lalu'],

                'sewa_dibayar_dimuka' => $sewa_dibayar_dimuka['tahun_ini'],
                'sewa_dibayar_dimuka_lalu' => $sewa_dibayar_dimuka['tahun_lalu'],

                'tabungan_pensiun_karyawan' => $tabungan_pensiun_karyawan['tahun_ini'],
                'tabungan_pensiun_karyawan_lalu' => $tabungan_pensiun_karyawan['tahun_lalu'],

                'pajak_dibayar_dimuka' => $pajak_dibayar_dimuka['tahun_ini'],
                'pajak_dibayar_dimuka_lalu' => $pajak_dibayar_dimuka['tahun_lalu'],


                'hutang_jangka_pendek' => $hutang_jangka_pendek['tahun_ini'],
                'hutang_jangka_pendek_lalu' => $hutang_jangka_pendek['tahun_lalu'],



            //aktivitas investasi
                'aset_tetap' => $aset_tetap['tahun_ini'],
                'aset_tetap_lalu' => $aset_tetap['tahun_lalu'],




            //aktivitas pendanaan
                'kewajiban_jangka_panjang' => $kewajiban_jangka_panjang['tahun_ini'],
                'kewajiban_jangka_panjang_lalu' => $kewajiban_jangka_panjang['tahun_lalu'],


                'aset_neto' => $aset_neto['tahun_ini'],
                'aset_neto_lalu' => $aset_neto['tahun_lalu'],


            //saldo kas
                'saldo_kas' => $saldo_kas['tahun_ini'],
                'saldo_kas_lalu' => $saldo_kas['tahun_lalu'],
        ]);



        
    }





    private function exportExcelArusKas($request, $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Logo
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path('assets/images/logos/YDB_PNG.png'));
        $drawing->setHeight(150);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setWorksheet($sheet);

       // RichText untuk judul dan periode
        $richText = new RichText();
        $judulText = $richText->createTextRun("LAPORAN ARUS KAS YAYASAN DARUSSALAM BATAM\n");
        $judulText->getFont()->setBold(true)->setSize(14);

        $periodeText = $richText->createTextRun("Periode " . Carbon::parse($data['filters']['start_date'])->translatedFormat('d F Y') . " s.d. " . Carbon::parse($data['filters']['end_date'])->translatedFormat('d F Y'));
        $periodeText->getFont()->setSize(10);

        // Set ke cell
        $sheet->setCellValue('A1', $richText);
        $sheet->mergeCells('A1:E4');
        $sheet->getRowDimension('1')->setRowHeight(60); // opsional: atur tinggi baris
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);

        


        // Header
        $row = 6;
        $sheet->fromArray(['No', 'Komponen Laporan Arus Kas', 'Total', 'Tahun ' . ($data['tahun'] - 1), 'Tahun ' . $data['tahun']], null, "A{$row}");
        $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
            'font' => ['color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $row++;

        // Fungsi bantu format
        $formatRupiah = function ($value) {
            if ($value == 0) return '-';
            return ($value > 0 ? '' : '(') . 'Rp ' . number_format(abs($value), 0, ',', '.') . ($value > 0 ? '' : ')');
        };

        // A. Aktivitas Operasional
        $sheet->setCellValue("A{$row}", 'A');
        $sheet->setCellValue("B{$row}", 'Aktivitas Operasional');
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
        $row++;

        // Laba bersih
        $sheet->fromArray([
            '', 'Kenaikan/Penurunan Aset Bersih',
            $formatRupiah($data['laba_bersih']['tahun_ini']),
            $formatRupiah($data['laba_bersih']['tahun_lalu']),
            $formatRupiah($data['laba_bersih']['tahun_ini']),
        ], null, "A{$row}");
        $row++;

        // Komponen penurunan/kenaikan aset lancar (contoh beberapa)
        $items_operasional = [
            'Persediaan Perlengkapan Kantor' => [
                'lalu' => $data['persediaan_perlengkapan_kantor_lalu'],
                'sekarang' => $data['persediaan_perlengkapan_kantor']
            ],
            'Persediaan Perlengkapan Asrama' => [
                'lalu' => $data['persediaan_perlengkapan_asrama_lalu'],
                'sekarang' => $data['persediaan_perlengkapan_asrama']
            ],
            'Persediaan ATK' => [
                'lalu' => $data['persediaan_atk_lalu'],
                'sekarang' => $data['persediaan_atk']
            ],
            'Persediaan Lainnya' => [
                'lalu' => $data['persediaan_lainnya_lalu'],
                'sekarang' => $data['persediaan_lainnya']
            ],
            'Piutang Rekanan' => [
                'lalu' => $data['piutang_rekanan_lalu'],
                'sekarang' => $data['piutang_rekanan']
            ],
            'Piutang Kegiatan' => [
                'lalu' => $data['piutang_kegiatan_lalu'],
                'sekarang' => $data['piutang_kegiatan']
            ],
            'Piutang Karyawan' => [
                'lalu' => $data['piutang_karyawan_lalu'],
                'sekarang' => $data['piutang_karyawan']
            ],
            'Piutang Sumbangan' => [
                'lalu' => $data['piutang_sumbangan_lalu'],
                'sekarang' => $data['piutang_sumbangan']
            ],
            'Piutang Lainnya' => [
                'lalu' => $data['piutang_lainnya_lalu'],
                'sekarang' => $data['piutang_lainnya']
            ],
            'Sewa Dibayar Dimuka' => [
                'lalu' => $data['sewa_dibayar_dimuka_lalu'],
                'sekarang' => $data['sewa_dibayar_dimuka']
            ],
            'Tabungan Pensiun Karyawan' => [
                'lalu' => $data['tabungan_pensiun_karyawan_lalu'],
                'sekarang' => $data['tabungan_pensiun_karyawan']
            ],
            'Pajak Dibayar Dimuka' => [
                'lalu' => $data['pajak_dibayar_dimuka_lalu'],
                'sekarang' => $data['pajak_dibayar_dimuka']
            ],
            'Hutang Jangka Pendek' => [
                'lalu' => $data['hutang_jangka_pendek_lalu'],
                'sekarang' => $data['hutang_jangka_pendek']
            ]
        ];


        $total_operasional = $data['laba_bersih']['tahun_ini'];

        foreach ($items_operasional as $label => $val) {
            $selisih = $val['lalu'] - $val['sekarang'];
            $total_operasional += $selisih;
            $sheet->fromArray([
                '', $label,
                $formatRupiah($selisih),
                $formatRupiah($val['lalu']),
                $formatRupiah($val['sekarang']),
            ], null, "A{$row}");
            $row++;
        }

        // Total operasional
        $sheet->fromArray([
            '', 'Kas Bersih dari Aktivitas Operasional',
            $formatRupiah($total_operasional), '-', '-'
        ], null, "A{$row}");
        $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
        $row++;

        // B. Aktivitas Investasi
        $sheet->setCellValue("A{$row}", 'B');
        $sheet->setCellValue("B{$row}", 'Aktivitas Investasi');
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
        $row++;

        $selisih_aset_tetap = $data['aset_tetap_lalu'] - $data['aset_tetap'];
        $sheet->fromArray([
            '', 'Penambahan/Pengurangan Aset Tetap',
            $formatRupiah($selisih_aset_tetap),
            $formatRupiah($data['aset_tetap_lalu']),
            $formatRupiah($data['aset_tetap']),
        ], null, "A{$row}");
        $row++;

        $sheet->fromArray([
            '', 'Kas Bersih dari Aktivitas Investasi',
            $formatRupiah($selisih_aset_tetap), '-', '-'
        ], null, "A{$row}");
        $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
        $row++;

        // C. Aktivitas Pendanaan
        $sheet->setCellValue("A{$row}", 'C');
        $sheet->setCellValue("B{$row}", 'Aktivitas Pendanaan');
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
        $row++;

        $selisih_kewajiban = $data['kewajiban_jangka_panjang_lalu'] - $data['kewajiban_jangka_panjang'];
        $selisih_aset_neto = $data['aset_neto_lalu'] - $data['aset_neto'];
        $total_pendanaan = $selisih_kewajiban + $selisih_aset_neto;

        $sheet->fromArray([
            '', 'Kewajiban Jangka Panjang',
            $formatRupiah($selisih_kewajiban),
            $formatRupiah($data['kewajiban_jangka_panjang_lalu']),
            $formatRupiah($data['kewajiban_jangka_panjang']),
        ], null, "A{$row}");
        $row++;

        $sheet->fromArray([
            '', 'Aset Neto',
            $formatRupiah($selisih_aset_neto),
            $formatRupiah($data['aset_neto_lalu']),
            $formatRupiah($data['aset_neto']),
        ], null, "A{$row}");
        $row++;

        $sheet->fromArray([
            '', 'Kas Bersih dari Aktivitas Pendanaan',
            $formatRupiah($total_pendanaan), '-', '-'
        ], null, "A{$row}");
        $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
        $row++;

        // Akhir: total kenaikan penurunan kas, saldo awal, saldo akhir
        $kenaikan_penurunan_kas = $total_operasional + $selisih_aset_tetap + $total_pendanaan;

        $sheet->fromArray([
            '', 'Kenaikan (Penurunan) Kas (A+B+C)',
            $formatRupiah($kenaikan_penurunan_kas), '-', '-'
        ], null, "A{$row}"); $row++;

        $sheet->fromArray([
            '', 'Saldo Kas Awal',
            $formatRupiah($data['saldo_kas_lalu']), '-', '-'
        ], null, "A{$row}"); $row++;

        $sheet->fromArray([
            '', 'Saldo Kas Akhir',
            $formatRupiah($data['saldo_kas']), '-', '-'
        ], null, "A{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);

        // Border
        $sheet->getStyle("A6:E{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Autosize
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $fileName = 'Laporan_Arus_Kas_' . $data['tahun'] . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }



















    public function index2(Request $request)
    {
        $user = Auth::user();

        $id_unit = $request->unit;
        $id_divisi = $request->divisi;

        // Jika user akuntan_unit dan tidak memilih unit, pakai unit dari akuntan_unit
        if (!$id_unit && $user->role === 'akuntan_unit') {
            $id_unit = \App\Models\Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
        }

        $filters = [
            'id_unit' => $id_unit,
            'id_divisi' => $id_divisi,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $units = Unit::all();
        $divisis = Divisi::all();
        $tahun = $request->input('tahun', date('Y'));

        //kompre
        $laba_bersih = $this->hitungLabaBersih($tahun, $filters);


        //aktivitas operasional
        $persediaan_perlengkapan_kantor = $this->hitung_persediaan_perlengkapan_kantor($tahun, $filters);
        $persediaan_perlengkapan_asrama = $this->hitung_persediaan_perlengkapan_asrama($tahun, $filters);
        $persediaan_atk = $this->hitung_persediaan_atk($tahun, $filters);
        $persediaan_lainnya = $this->hitung_persediaan_lainnya($tahun, $filters);

        $piutang_rekanan = $this->hitung_piutang_rekanan($tahun, $filters);
        $piutang_kegiatan = $this->hitung_piutang_kegiatan($tahun, $filters);
        $piutang_karyawan = $this->hitung_piutang_karyawan($tahun, $filters);
        $piutang_sumbangan = $this->hitung_piutang_sumbangan($tahun, $filters);
        $piutang_lainnya = $this->hitung_piutang_lainnya($tahun, $filters);

        $sewa_dibayar_dimuka = $this->hitung_sewa_dibayar_dimuka($tahun, $filters);
        $tabungan_pensiun_karyawan = $this->hitung_tabungan_pensiun_karyawan($tahun, $filters);
        $pajak_dibayar_dimuka = $this->hitung_pajak_dibayar_dimuka($tahun, $filters);
        $hutang_jangka_pendek = $this->hitung_hutang_jangka_pendek($tahun, $filters);


        //aktivitas investasi
        $aset_tetap = $this->hitung_aset_tetap($tahun, $filters);


        //aktivitas pendanaan
        $kewajiban_jangka_panjang = $this->hitung_kewajiban_jangka_panjang($tahun, $filters);
        $aset_neto = $this->hitung_aset_neto($tahun, $filters);


        //saldo akun
        $saldo_kas = $this->hitung_saldo_kas($tahun, $filters);







        $depresiasi = $this->hitungDepresiasi($tahun, $filters);
        $penurunan_piutang = $this->hitungPenurunanPiutang($tahun, $filters);
        $penurunanPersediaan = $this->hitungPenurunanPersediaan($tahun, $filters);
        $penurunanBiayaDibayarDimuka = $this->hitungPenurunanBiayaDibayarDimuka($tahun, $filters);
        $penurunanAktivaLancar = $this->hitungPenurunanAktivaLancarLainnya($tahun, $filters);
        $kenaikan_hutang = $this->hitungKenaikanHutang($tahun, $filters);
        $kenaikanHutangBiaya = $this->hitungKenaikanHutangBiaya($tahun, $filters);
        $kenaikanPiutang = $this->hitungKenaikanPiutang($tahun, $filters);
        $kenaikanPersediaan = $this->hitungKenaikanPersediaan($tahun, $filters);
        $kenaikanBiayaDibayarDimuka = $this->hitungKenaikanBiayaDibayarDimuka($tahun, $filters);
        $kenaikanAktivaLancarLainnya = $this->hitungKenaikanAktivaLancarLainnya($tahun, $filters);
        $penurunan_hutang = $this->hitungPenurunanHutang($tahun, $filters);
        $penurunan_hutang_biaya = $this->hitungPenurunanHutangBiaya($tahun, $filters);
        $kas_masuk_penjualan_aset_tetap = $this->hitungKasMasukPenjualanAsetTetap($tahun, $filters);
        $kas_keluar_pembelian_aset_tetap = $this->hitungKasKeluarPembelianAsetTetap($tahun, $filters);
        $kas_keluar_inventaris = $this->hitungKasKeluarInventaris($tahun, $filters);
        $kenaikan_pinjaman_jangka_panjang = $this->hitungKenaikanPinjamanJangkaPanjang($tahun, $filters);
        $penurunan_pinjaman_jangka_panjang = $this->hitungPenurunanPinjamanJangkaPanjang($tahun, $filters);
        $saldoKasTunai = $this->getSaldoKasTunai($tahun, $filters);
        $saldoKasBank = $this->getSaldoBank($tahun, $filters);
        $saldoKasTunaiAwal = $this->getSaldoKasTunaiAwal($tahun, $filters);
        $saldoKasBankAwal = $this->getSaldoBankAwal($tahun, $filters);


        return view('arus-kas2', [
            'tahun' => $tahun,
            'units' => $units,
            'divisis' => $divisis,
            'id_unit' => $id_unit,
            'id_divisi' => $id_divisi,
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],


            //kompre
                'laba_bersih_tahun_ini' => $laba_bersih['tahun_ini'],
                'laba_bersih_tahun_lalu' => $laba_bersih['tahun_lalu'],


            //aktivitas operasional
                'persediaan_perlengkapan_kantor' => $persediaan_perlengkapan_kantor['tahun_ini'],
                'persediaan_perlengkapan_kantor_lalu' => $persediaan_perlengkapan_kantor['tahun_lalu'],

                'persediaan_perlengkapan_asrama' => $persediaan_perlengkapan_asrama['tahun_ini'],
                'persediaan_perlengkapan_asrama_lalu' => $persediaan_perlengkapan_asrama['tahun_lalu'],
                
                'persediaan_atk' => $persediaan_atk['tahun_ini'],
                'persediaan_atk_lalu' => $persediaan_atk['tahun_lalu'],
                
                'persediaan_lainnya' => $persediaan_lainnya['tahun_ini'],
                'persediaan_lainnya_lalu' => $persediaan_lainnya['tahun_lalu'],

                'piutang_rekanan' => $piutang_rekanan['tahun_ini'],
                'piutang_rekanan_lalu' => $piutang_rekanan['tahun_lalu'],

                'piutang_kegiatan' => $piutang_kegiatan['tahun_ini'],
                'piutang_kegiatan_lalu' => $piutang_kegiatan['tahun_lalu'],

                'piutang_karyawan' => $piutang_karyawan['tahun_ini'],
                'piutang_karyawan_lalu' => $piutang_karyawan['tahun_lalu'],

                'piutang_sumbangan' => $piutang_sumbangan['tahun_ini'],
                'piutang_sumbangan_lalu' => $piutang_sumbangan['tahun_lalu'],

                'piutang_lainnya' => $piutang_lainnya['tahun_ini'],
                'piutang_lainnya_lalu' => $piutang_lainnya['tahun_lalu'],

                'sewa_dibayar_dimuka' => $sewa_dibayar_dimuka['tahun_ini'],
                'sewa_dibayar_dimuka_lalu' => $sewa_dibayar_dimuka['tahun_lalu'],

                'tabungan_pensiun_karyawan' => $tabungan_pensiun_karyawan['tahun_ini'],
                'tabungan_pensiun_karyawan_lalu' => $tabungan_pensiun_karyawan['tahun_lalu'],

                'pajak_dibayar_dimuka' => $pajak_dibayar_dimuka['tahun_ini'],
                'pajak_dibayar_dimuka_lalu' => $pajak_dibayar_dimuka['tahun_lalu'],


                'hutang_jangka_pendek' => $hutang_jangka_pendek['tahun_ini'],
                'hutang_jangka_pendek_lalu' => $hutang_jangka_pendek['tahun_lalu'],



            //aktivitas investasi
                'aset_tetap' => $aset_tetap['tahun_ini'],
                'aset_tetap_lalu' => $aset_tetap['tahun_lalu'],




            //aktivitas pendanaan
                'kewajiban_jangka_panjang' => $kewajiban_jangka_panjang['tahun_ini'],
                'kewajiban_jangka_panjang_lalu' => $kewajiban_jangka_panjang['tahun_lalu'],


                'aset_neto' => $aset_neto['tahun_ini'],
                'aset_neto_lalu' => $aset_neto['tahun_lalu'],


            //saldo kas
                'saldo_kas' => $saldo_kas['tahun_ini'],
                'saldo_kas_lalu' => $saldo_kas['tahun_lalu'],











            'depresiasi_tahun_ini' => $depresiasi['tahun_ini'],
            'depresiasi_tahun_lalu' => $depresiasi['tahun_lalu'],

            'penurunan_piutang_tahun_ini' => $penurunan_piutang['tahun_ini'],
            'penurunan_piutang_tahun_lalu' => $penurunan_piutang['tahun_lalu'],

            'penurunan_persediaan_tahun_ini' => $penurunanPersediaan['tahun_ini'],
            'penurunan_persediaan_tahun_lalu' => $penurunanPersediaan['tahun_lalu'],

            'penurunan_bdd_tahun_ini' => $penurunanBiayaDibayarDimuka['tahun_ini'],
            'penurunan_bdd_tahun_lalu' => $penurunanBiayaDibayarDimuka['tahun_lalu'],

            'penurunan_aktiva_lancar_tahun_ini' => $penurunanAktivaLancar['tahun_ini'],
            'penurunan_aktiva_lancar_tahun_lalu' => $penurunanAktivaLancar['tahun_lalu'],

            'kenaikan_hutang_tahun_ini' => $kenaikan_hutang['tahun_ini'],
            'kenaikan_hutang_tahun_lalu' => $kenaikan_hutang['tahun_lalu'],

            'kenaikan_hutang_biaya_tahun_ini' => $kenaikanHutangBiaya['tahun_ini'],
            'kenaikan_hutang_biaya_tahun_lalu' => $kenaikanHutangBiaya['tahun_lalu'],

            'kenaikan_piutang_tahun_ini' => $kenaikanPiutang['tahun_ini'],
            'kenaikan_piutang_tahun_lalu' => $kenaikanPiutang['tahun_lalu'],

            'kenaikan_persediaan_tahun_ini' => $kenaikanPersediaan['tahun_ini'],
            'kenaikan_persediaan_tahun_lalu' => $kenaikanPersediaan['tahun_lalu'],

            'kenaikan_biaya_dibayar_dimuka_tahun_ini' => $kenaikanBiayaDibayarDimuka['tahun_ini'],
            'kenaikan_biaya_dibayar_dimuka_tahun_lalu' => $kenaikanBiayaDibayarDimuka['tahun_lalu'],

            'kenaikan_aktiva_lancar_lainnya_tahun_ini' => $kenaikanAktivaLancarLainnya['tahun_ini'],
            'kenaikan_aktiva_lancar_lainnya_tahun_lalu' => $kenaikanAktivaLancarLainnya['tahun_lalu'],

            'penurunan_hutang_tahun_ini' => $penurunan_hutang['tahun_ini'],
            'penurunan_hutang_tahun_lalu' => $penurunan_hutang['tahun_lalu'],

            'penurunan_hutang_biaya_tahun_ini' => $penurunan_hutang_biaya['tahun_ini'],
            'penurunan_hutang_biaya_tahun_lalu' => $penurunan_hutang_biaya['tahun_lalu'],

            'kas_masuk_penjualan_aset_tetap_tahun_ini' => $kas_masuk_penjualan_aset_tetap['tahun_ini'],
            'kas_masuk_penjualan_aset_tetap_tahun_lalu' => $kas_masuk_penjualan_aset_tetap['tahun_lalu'],

            'kas_keluar_pembelian_aset_tetap_tahun_ini' => $kas_keluar_pembelian_aset_tetap['tahun_ini'],
            'kas_keluar_pembelian_aset_tetap_tahun_lalu' => $kas_keluar_pembelian_aset_tetap['tahun_lalu'],

            'kas_keluar_inventaris_tahun_ini' => $kas_keluar_inventaris['tahun_ini'],
            'kas_keluar_inventaris_tahun_lalu' => $kas_keluar_inventaris['tahun_lalu'],

            'kenaikan_pinjaman_jangka_panjang_tahun_ini' => $kenaikan_pinjaman_jangka_panjang['tahun_ini'],
            'kenaikan_pinjaman_jangka_panjang_tahun_lalu' => $kenaikan_pinjaman_jangka_panjang['tahun_lalu'],

            'penurunan_pinjaman_jangka_panjang_tahun_ini' => $penurunan_pinjaman_jangka_panjang['tahun_ini'],
            'penurunan_pinjaman_jangka_panjang_tahun_lalu' => $penurunan_pinjaman_jangka_panjang['tahun_lalu'],

            'saldoKasTunai' => $saldoKasTunai, 
            'saldoKasBank' => $saldoKasBank,
            'saldoKasTunaiAwal' => $saldoKasTunaiAwal, 
            'saldoKasBankAwal' => $saldoKasBankAwal
        ]);
    }



    //operasional

    private function hitung_persediaan_perlengkapan_kantor($tahun, $filters)
    {
        // Ambil akun Persediaan Perlengkapan Kantor
        $akun = Akun::where('akun', 'Persediaan Perlengkapan Kantor')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }



    private function hitung_persediaan_perlengkapan_asrama($tahun, $filters)
    {
        // Ambil akun Persediaan Perlengkapan asrama
        $akun = Akun::where('akun', 'Persediaan Perlengkapan Asrama')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }



    private function hitung_persediaan_atk($tahun, $filters)
    {
        // Ambil akun Persediaan atk
        $akun = Akun::where('akun', 'Persediaan ATK')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }



    
    private function hitung_persediaan_lainnya($tahun, $filters)
    {
        // Ambil akun Persediaan lainnya
        $akun = Akun::where('akun', 'Persediaan Lainnya')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }



    
    private function hitung_piutang_rekanan($tahun, $filters)
    {
        // Ambil akun Piutang Rekanan
        $akun = Akun::where('akun', 'Piutang Rekanan')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }



    private function hitung_piutang_kegiatan($tahun, $filters)
    {
        // Ambil akun Piutang kegiatan
        $akun = Akun::where('akun', 'Piutang Kegiatan')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }



    private function hitung_piutang_karyawan($tahun, $filters)
    {
        // Ambil akun Piutang karyawan
        $akun = Akun::where('akun', 'Piutang Karyawan')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }



    private function hitung_piutang_sumbangan($tahun, $filters)
    {
        // Ambil akun Piutang sumbangan
        $akun = Akun::where('akun', 'Piutang Sumbangan')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }


    private function hitung_piutang_lainnya($tahun, $filters)
    {
        // Ambil akun Piutang lainnya
        $akun = Akun::where('akun', 'Piutang Lainnya')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }


    private function hitung_sewa_dibayar_dimuka($tahun, $filters)
    {
        // Ambil akun Sewa Dibayar Dimuka
        $akun = Akun::where('akun', 'Sewa Dibayar Dimuka')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }


    private function hitung_tabungan_pensiun_karyawan($tahun, $filters)
    {
        // Ambil akun Tabungan Pensiun Karyawan
        $akun = Akun::where('akun', 'Tabungan Pensiun Karyawan')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }

    private function hitung_pajak_dibayar_dimuka($tahun, $filters)
    {
        // Ambil akun Pajak Dibayar Dimuka
        $akun = Akun::where('akun', 'Pajak Dibayar Dimuka')
            ->first();

        if (!$akun) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun = $akun->id_akun;

        // Saldo awal tahun lalu
        $saldo_awal = $akun->saldo_awal_debit - $akun->saldo_awal_kredit;

        // Ambil semua transaksi jurnal umum tahun ini untuk akun tsb
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->where('detail_jurnal_umum.id_akun', $id_akun)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Total debit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');

        // Total kredit
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir tahun ini
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }

    
    private function hitung_hutang_jangka_pendek($tahun, $filters)
    {
        // Ambil semua akun dengan sub kategori "Kewajiban Jangka Pendek"
        $akunList = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Kewajiban Jangka Pendek');
        })->get();

        if ($akunList->isEmpty()) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun_list = $akunList->pluck('id_akun');

        // Total saldo awal = sum(debit - kredit) dari semua akun
        $saldo_awal = $akunList->sum(function ($akun) {
            return $akun->saldo_awal_debit - $akun->saldo_awal_kredit;
        });

        // Query dasar untuk ambil mutasi tahun ini
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->whereIn('detail_jurnal_umum.id_akun', $id_akun_list)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        // Filter berdasarkan unit (jika ada)
        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Filter berdasarkan divisi (jika ada)
        if (!empty($filters['id_divisi'])) {
            $query->where('jurnal_umum.id_divisi', $filters['id_divisi']);
        }

        // Filter tanggal mulai (jika ada)
        if (!empty($filters['start_date'])) {
            $query->whereDate('jurnal_umum.tanggal', '>=', $filters['start_date']);
        }

        // Filter tanggal akhir (jika ada)
        if (!empty($filters['end_date'])) {
            $query->whereDate('jurnal_umum.tanggal', '<=', $filters['end_date']);
        }

        // Hitung total debit dan kredit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir = saldo awal + (debit - kredit)
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }







    

    //investasi
    private function hitung_aset_tetap($tahun, $filters) 
    {

        // Ambil semua akun dengan sub kategori "Kewajiban Jangka Pendek"
        $akunList = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Aktiva Tetap');
        })
        ->where('akun', 'not like', 'Akumulasi Penyusutan%')
        ->get();

        if ($akunList->isEmpty()) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun_list = $akunList->pluck('id_akun');

        // Total saldo awal = sum(debit - kredit) dari semua akun
        $saldo_awal = $akunList->sum(function ($akun) {
            return $akun->saldo_awal_debit - $akun->saldo_awal_kredit;
        });

        // Query dasar untuk ambil mutasi tahun ini
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->whereIn('detail_jurnal_umum.id_akun', $id_akun_list)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        // Filter berdasarkan unit (jika ada)
        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Filter berdasarkan divisi (jika ada)
        if (!empty($filters['id_divisi'])) {
            $query->where('jurnal_umum.id_divisi', $filters['id_divisi']);
        }

        // Filter tanggal mulai (jika ada)
        if (!empty($filters['start_date'])) {
            $query->whereDate('jurnal_umum.tanggal', '>=', $filters['start_date']);
        }

        // Filter tanggal akhir (jika ada)
        if (!empty($filters['end_date'])) {
            $query->whereDate('jurnal_umum.tanggal', '<=', $filters['end_date']);
        }

        // Hitung total debit dan kredit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir = saldo awal + (debit - kredit)
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }




    //Pendanaan
    private function hitung_kewajiban_jangka_panjang($tahun, $filters) 
    {

        // Ambil semua akun dengan sub kategori "Kewajiban Jangka Pendek"
        $akunList = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Kewajiban Jangka Panjang');
        })
        ->get();

        if ($akunList->isEmpty()) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun_list = $akunList->pluck('id_akun');

        // Total saldo awal = sum(debit - kredit) dari semua akun
        $saldo_awal = $akunList->sum(function ($akun) {
            return $akun->saldo_awal_debit - $akun->saldo_awal_kredit;
        });

        // Query dasar untuk ambil mutasi tahun ini
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->whereIn('detail_jurnal_umum.id_akun', $id_akun_list)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        // Filter berdasarkan unit (jika ada)
        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Filter berdasarkan divisi (jika ada)
        if (!empty($filters['id_divisi'])) {
            $query->where('jurnal_umum.id_divisi', $filters['id_divisi']);
        }

        // Filter tanggal mulai (jika ada)
        if (!empty($filters['start_date'])) {
            $query->whereDate('jurnal_umum.tanggal', '>=', $filters['start_date']);
        }

        // Filter tanggal akhir (jika ada)
        if (!empty($filters['end_date'])) {
            $query->whereDate('jurnal_umum.tanggal', '<=', $filters['end_date']);
        }

        // Hitung total debit dan kredit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir = saldo awal + (debit - kredit)
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }


    private function hitung_aset_neto($tahun, $filters) 
    {

        $akunList = Akun::whereHas('sub_kategori_akun.kategori_akun', function ($query) {
            $query->where('kategori_akun', 'ASET NETO');
        })->get();


        if ($akunList->isEmpty()) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun_list = $akunList->pluck('id_akun');

        // Total saldo awal = sum(debit - kredit) dari semua akun
        $saldo_awal = $akunList->sum(function ($akun) {
            return $akun->saldo_awal_debit - $akun->saldo_awal_kredit;
        });

        // Query dasar untuk ambil mutasi tahun ini
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->whereIn('detail_jurnal_umum.id_akun', $id_akun_list)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        // Filter berdasarkan unit (jika ada)
        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Filter berdasarkan divisi (jika ada)
        if (!empty($filters['id_divisi'])) {
            $query->where('jurnal_umum.id_divisi', $filters['id_divisi']);
        }

        // Filter tanggal mulai (jika ada)
        if (!empty($filters['start_date'])) {
            $query->whereDate('jurnal_umum.tanggal', '>=', $filters['start_date']);
        }

        // Filter tanggal akhir (jika ada)
        if (!empty($filters['end_date'])) {
            $query->whereDate('jurnal_umum.tanggal', '<=', $filters['end_date']);
        }

        // Hitung total debit dan kredit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir = saldo awal + (debit - kredit)
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }




    //saldo kas
    private function hitung_saldo_kas($tahun, $filters) 
    {

        $akunList = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->whereIn('sub_kategori_akun', ['KAS', 'BANK']);
        })->get();




        if ($akunList->isEmpty()) {
            return ['tahun_ini' => 0, 'tahun_lalu' => 0];
        }

        $id_akun_list = $akunList->pluck('id_akun');

        // Total saldo awal = sum(debit - kredit) dari semua akun
        $saldo_awal = $akunList->sum(function ($akun) {
            return $akun->saldo_awal_debit - $akun->saldo_awal_kredit;
        });

        // Query dasar untuk ambil mutasi tahun ini
        $query = DB::table('detail_jurnal_umum')
            ->join('jurnal_umum', 'detail_jurnal_umum.id_jurnal_umum', '=', 'jurnal_umum.id_jurnal_umum')
            ->whereIn('detail_jurnal_umum.id_akun', $id_akun_list)
            ->whereYear('jurnal_umum.tanggal', $tahun);

        // Filter berdasarkan unit (jika ada)
        if (!empty($filters['id_unit'])) {
            $query->where('jurnal_umum.id_unit', $filters['id_unit']);
        }

        // Filter berdasarkan divisi (jika ada)
        if (!empty($filters['id_divisi'])) {
            $query->where('jurnal_umum.id_divisi', $filters['id_divisi']);
        }

        // Filter tanggal mulai (jika ada)
        if (!empty($filters['start_date'])) {
            $query->whereDate('jurnal_umum.tanggal', '>=', $filters['start_date']);
        }

        // Filter tanggal akhir (jika ada)
        if (!empty($filters['end_date'])) {
            $query->whereDate('jurnal_umum.tanggal', '<=', $filters['end_date']);
        }

        // Hitung total debit dan kredit
        $total_debet = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'debit')->sum('detail_jurnal_umum.nominal');
        $total_kredit = (clone $query)->where('detail_jurnal_umum.debit_kredit', 'kredit')->sum('detail_jurnal_umum.nominal');

        // Saldo akhir = saldo awal + (debit - kredit)
        $saldo_tahun_ini = $saldo_awal + ($total_debet - $total_kredit);

        return [
            'tahun_lalu' => $saldo_awal,
            'tahun_ini' => $saldo_tahun_ini,
        ];
    }





































    public function index(Request $request)
    {
        $user = Auth::user();

        $id_unit = $request->unit;
        $id_divisi = $request->divisi;

        // Jika user akuntan_unit dan tidak memilih unit, pakai unit dari akuntan_unit
        if (!$id_unit && $user->role === 'akuntan_unit') {
            $id_unit = \App\Models\Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->value('id_unit');
        }

        $filters = [
            'id_unit' => $id_unit,
            'id_divisi' => $id_divisi,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $units = Unit::all();
        $divisis = Divisi::all();
        $tahun = $request->input('tahun', date('Y'));

        $laba_bersih = $this->hitungLabaBersih($tahun, $filters);
        $depresiasi = $this->hitungDepresiasi($tahun, $filters);
        $penurunan_piutang = $this->hitungPenurunanPiutang($tahun, $filters);
        $penurunanPersediaan = $this->hitungPenurunanPersediaan($tahun, $filters);
        $penurunanBiayaDibayarDimuka = $this->hitungPenurunanBiayaDibayarDimuka($tahun, $filters);
        $penurunanAktivaLancar = $this->hitungPenurunanAktivaLancarLainnya($tahun, $filters);
        $kenaikan_hutang = $this->hitungKenaikanHutang($tahun, $filters);
        $kenaikanHutangBiaya = $this->hitungKenaikanHutangBiaya($tahun, $filters);
        $kenaikanPiutang = $this->hitungKenaikanPiutang($tahun, $filters);
        $kenaikanPersediaan = $this->hitungKenaikanPersediaan($tahun, $filters);
        $kenaikanBiayaDibayarDimuka = $this->hitungKenaikanBiayaDibayarDimuka($tahun, $filters);
        $kenaikanAktivaLancarLainnya = $this->hitungKenaikanAktivaLancarLainnya($tahun, $filters);
        $penurunan_hutang = $this->hitungPenurunanHutang($tahun, $filters);
        $penurunan_hutang_biaya = $this->hitungPenurunanHutangBiaya($tahun, $filters);
        $kas_masuk_penjualan_aset_tetap = $this->hitungKasMasukPenjualanAsetTetap($tahun, $filters);
        $kas_keluar_pembelian_aset_tetap = $this->hitungKasKeluarPembelianAsetTetap($tahun, $filters);
        $kas_keluar_inventaris = $this->hitungKasKeluarInventaris($tahun, $filters);
        $kenaikan_pinjaman_jangka_panjang = $this->hitungKenaikanPinjamanJangkaPanjang($tahun, $filters);
        $penurunan_pinjaman_jangka_panjang = $this->hitungPenurunanPinjamanJangkaPanjang($tahun, $filters);
        $saldoKasTunai = $this->getSaldoKasTunai($tahun, $filters);
        $saldoKasBank = $this->getSaldoBank($tahun, $filters);
        $saldoKasTunaiAwal = $this->getSaldoKasTunaiAwal($tahun, $filters);
        $saldoKasBankAwal = $this->getSaldoBankAwal($tahun, $filters);


        return view('arus-kas', [
            'tahun' => $tahun,
            'units' => $units,
            'divisis' => $divisis,
            'id_unit' => $id_unit,
            'id_divisi' => $id_divisi,
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],

            'laba_bersih_tahun_ini' => $laba_bersih['tahun_ini'],
            'laba_bersih_tahun_lalu' => $laba_bersih['tahun_lalu'],

            'depresiasi_tahun_ini' => $depresiasi['tahun_ini'],
            'depresiasi_tahun_lalu' => $depresiasi['tahun_lalu'],

            'penurunan_piutang_tahun_ini' => $penurunan_piutang['tahun_ini'],
            'penurunan_piutang_tahun_lalu' => $penurunan_piutang['tahun_lalu'],

            'penurunan_persediaan_tahun_ini' => $penurunanPersediaan['tahun_ini'],
            'penurunan_persediaan_tahun_lalu' => $penurunanPersediaan['tahun_lalu'],

            'penurunan_bdd_tahun_ini' => $penurunanBiayaDibayarDimuka['tahun_ini'],
            'penurunan_bdd_tahun_lalu' => $penurunanBiayaDibayarDimuka['tahun_lalu'],

            'penurunan_aktiva_lancar_tahun_ini' => $penurunanAktivaLancar['tahun_ini'],
            'penurunan_aktiva_lancar_tahun_lalu' => $penurunanAktivaLancar['tahun_lalu'],

            'kenaikan_hutang_tahun_ini' => $kenaikan_hutang['tahun_ini'],
            'kenaikan_hutang_tahun_lalu' => $kenaikan_hutang['tahun_lalu'],

            'kenaikan_hutang_biaya_tahun_ini' => $kenaikanHutangBiaya['tahun_ini'],
            'kenaikan_hutang_biaya_tahun_lalu' => $kenaikanHutangBiaya['tahun_lalu'],

            'kenaikan_piutang_tahun_ini' => $kenaikanPiutang['tahun_ini'],
            'kenaikan_piutang_tahun_lalu' => $kenaikanPiutang['tahun_lalu'],

            'kenaikan_persediaan_tahun_ini' => $kenaikanPersediaan['tahun_ini'],
            'kenaikan_persediaan_tahun_lalu' => $kenaikanPersediaan['tahun_lalu'],

            'kenaikan_biaya_dibayar_dimuka_tahun_ini' => $kenaikanBiayaDibayarDimuka['tahun_ini'],
            'kenaikan_biaya_dibayar_dimuka_tahun_lalu' => $kenaikanBiayaDibayarDimuka['tahun_lalu'],

            'kenaikan_aktiva_lancar_lainnya_tahun_ini' => $kenaikanAktivaLancarLainnya['tahun_ini'],
            'kenaikan_aktiva_lancar_lainnya_tahun_lalu' => $kenaikanAktivaLancarLainnya['tahun_lalu'],

            'penurunan_hutang_tahun_ini' => $penurunan_hutang['tahun_ini'],
            'penurunan_hutang_tahun_lalu' => $penurunan_hutang['tahun_lalu'],

            'penurunan_hutang_biaya_tahun_ini' => $penurunan_hutang_biaya['tahun_ini'],
            'penurunan_hutang_biaya_tahun_lalu' => $penurunan_hutang_biaya['tahun_lalu'],

            'kas_masuk_penjualan_aset_tetap_tahun_ini' => $kas_masuk_penjualan_aset_tetap['tahun_ini'],
            'kas_masuk_penjualan_aset_tetap_tahun_lalu' => $kas_masuk_penjualan_aset_tetap['tahun_lalu'],

            'kas_keluar_pembelian_aset_tetap_tahun_ini' => $kas_keluar_pembelian_aset_tetap['tahun_ini'],
            'kas_keluar_pembelian_aset_tetap_tahun_lalu' => $kas_keluar_pembelian_aset_tetap['tahun_lalu'],

            'kas_keluar_inventaris_tahun_ini' => $kas_keluar_inventaris['tahun_ini'],
            'kas_keluar_inventaris_tahun_lalu' => $kas_keluar_inventaris['tahun_lalu'],

            'kenaikan_pinjaman_jangka_panjang_tahun_ini' => $kenaikan_pinjaman_jangka_panjang['tahun_ini'],
            'kenaikan_pinjaman_jangka_panjang_tahun_lalu' => $kenaikan_pinjaman_jangka_panjang['tahun_lalu'],

            'penurunan_pinjaman_jangka_panjang_tahun_ini' => $penurunan_pinjaman_jangka_panjang['tahun_ini'],
            'penurunan_pinjaman_jangka_panjang_tahun_lalu' => $penurunan_pinjaman_jangka_panjang['tahun_lalu'],

            'saldoKasTunai' => $saldoKasTunai, 
            'saldoKasBank' => $saldoKasBank,
            'saldoKasTunaiAwal' => $saldoKasTunaiAwal, 
            'saldoKasBankAwal' => $saldoKasBankAwal
        ]);
    }



    private function hitungLabaBersih($tahun, $filters)
    {
        $akunPendapatan = Akun::whereHas('sub_kategori_akun.kategori_akun', function ($query) {
            $query->where('kategori_akun', 'PENERIMAAN DAN SUMBANGAN');
        })->pluck('id_akun');

        $akunBeban = Akun::whereHas('sub_kategori_akun.kategori_akun', function ($query) {
            $query->where('kategori_akun', 'BEBAN');
        })->pluck('id_akun');

        // Ambil jurnal yang sudah diposting ke buku besar
        $postedJurnal = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // === Periode berjalan ===
        $jurnalQuery = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
            $q->whereYear('tanggal', $tahun);

            if (!empty($filters['id_unit'])) {
                $q->where('id_unit', $filters['id_unit']);
            }

            if (!empty($filters['id_divisi'])) {
                $q->where('id_divisi', $filters['id_divisi']);
            }

            if (!empty($filters['start_date'])) {
                $q->whereDate('tanggal', '>=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $q->whereDate('tanggal', '<=', $filters['end_date']);
            }
        })
        ->whereIn('id_jurnal_umum', $postedJurnal); // ✅ hanya jurnal yang sudah diposting

        $pendapatan = (clone $jurnalQuery)
            ->whereIn('id_akun', $akunPendapatan)
            ->where('debit_kredit', 'kredit')
            ->sum('nominal');

        $beban = (clone $jurnalQuery)
            ->whereIn('id_akun', $akunBeban)
            ->where('debit_kredit', 'debit')
            ->sum('nominal');

        $laba_bersih_tahun_ini = $pendapatan - $beban;

        // === Tahun lalu (pakai saldo akhir tahunan dari tabel khusus) ===
        $tahun_lalu = $tahun - 1;

        $saldo_pendapatan_lalu = Komprehensif_Akhir_Tahun::where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $akunPendapatan)
            ->sum(DB::raw('saldo_akhir_tanpa_pembatasan + saldo_akhir_dengan_pembatasan'));

        $saldo_beban_lalu = Komprehensif_Akhir_Tahun::where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $akunBeban)
            ->sum(DB::raw('saldo_akhir_tanpa_pembatasan + saldo_akhir_dengan_pembatasan'));

        $laba_bersih_tahun_lalu = $saldo_pendapatan_lalu - $saldo_beban_lalu;

        return [
            'tahun_ini' => $laba_bersih_tahun_ini,
            'tahun_lalu' => $laba_bersih_tahun_lalu,
        ];
    }



    private function hitungDepresiasi($tahun, $filters)
    {
        // Ambil ID akun-akun beban depresiasi
        $akunDepresiasi = Akun::whereIn('akun', [
            'Beban Penyusutan Bangunan',
            'Beban Penyusutan Inventaris Kantor',
            'Beban Penyusutan Peralatan',
            'Beban Penyusutan Kendaraan'
        ])->pluck('id_akun');

        // Ambil ID jurnal umum yang sudah diposting ke buku besar
        $jurnalPosted = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // Tahun berjalan → total debit akun depresiasi (beban)
        $depresiasi_tahun_ini = Detail_Jurnal_Umum::whereIn('id_akun', $akunDepresiasi)
            ->whereIn('id_jurnal_umum', $jurnalPosted)
            ->where('debit_kredit', 'debit')
            ->whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);

                if ($filters['id_unit']) $q->where('id_unit', $filters['id_unit']);
                if ($filters['id_divisi']) $q->where('id_divisi', $filters['id_divisi']);
                if ($filters['start_date']) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if ($filters['end_date']) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->sum('nominal');

        // Tahun lalu → total debit akun depresiasi (beban)
        $tahun_lalu = $tahun - 1;
        $depresiasi_tahun_lalu = Detail_Jurnal_Umum::whereIn('id_akun', $akunDepresiasi)
            ->whereIn('id_jurnal_umum', $jurnalPosted)
            ->where('debit_kredit', 'debit')
            ->whereHas('jurnal_umum', function ($q) use ($tahun_lalu, $filters) {
                $q->whereYear('tanggal', $tahun_lalu);
                if ($filters['id_unit']) $q->where('id_unit', $filters['id_unit']);
                if ($filters['id_divisi']) $q->where('id_divisi', $filters['id_divisi']);
            })
            ->sum('nominal');

        return [
            'tahun_ini' => $depresiasi_tahun_ini,
            'tahun_lalu' => $depresiasi_tahun_lalu,
        ];
    }




    private function hitungPenurunanPiutang($tahun, $filters)
    {
        // Ambil akun-akun Piutang
        $akun = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'like', '%Piutang%');
        })->get();

        $id_akun = $akun->pluck('id_akun');

        // Ambil ID jurnal umum yang sudah diposting ke buku besar
        $jurnalPosted = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // 🔹 Penurunan tahun berjalan = total kredit akun piutang
        $penurunan_tahun_ini = Detail_Jurnal_Umum::whereIn('id_akun', $id_akun)
            ->whereIn('id_jurnal_umum', $jurnalPosted)
            ->where('debit_kredit', 'kredit')
            ->whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);

                if ($filters['id_unit']) $q->where('id_unit', $filters['id_unit']);
                if ($filters['id_divisi']) $q->where('id_divisi', $filters['id_divisi']);
                if ($filters['start_date']) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if ($filters['end_date']) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->sum('nominal');

        // 🔹 Penurunan tahun lalu = total kredit akun piutang tahun sebelumnya
        $tahun_lalu = $tahun - 1;

        $penurunan_tahun_lalu = Detail_Jurnal_Umum::whereIn('id_akun', $id_akun)
            ->whereIn('id_jurnal_umum', $jurnalPosted)
            ->where('debit_kredit', 'kredit')
            ->whereHas('jurnal_umum', function ($q) use ($tahun_lalu, $filters) {
                $q->whereYear('tanggal', $tahun_lalu);

                if ($filters['id_unit']) $q->where('id_unit', $filters['id_unit']);
                if ($filters['id_divisi']) $q->where('id_divisi', $filters['id_divisi']);
            })
            ->sum('nominal');

        return [
            'tahun_ini' => $penurunan_tahun_ini,
            'tahun_lalu' => $penurunan_tahun_lalu,
        ];
    }





    private function hitungPenurunanPersediaan($tahun, $filters)
    {
        // 🔍 Ambil semua ID akun dengan sub kategori "Persediaan"
        $id_akun_persediaan = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Persediaan');
        })->pluck('id_akun');

        // 🔍 Ambil jurnal umum yang sudah diposting
        $jurnalPosted = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // =========================
        // 🔹 PERIODE TAHUN BERJALAN
        // =========================

        // Hitung total saldo awal hanya dari akun yang terlibat transaksi dalam unit/divisi
        // Dengan mengambil dari jurnal_umum juga
        $transaksi = Detail_Jurnal_Umum::whereIn('id_jurnal_umum', $jurnalPosted)
            ->whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);

                if (!empty($filters['id_unit'])) {
                    $q->where('id_unit', $filters['id_unit']);
                }
                if (!empty($filters['id_divisi'])) {
                    $q->where('id_divisi', $filters['id_divisi']);
                }
                if (!empty($filters['start_date'])) {
                    $q->whereDate('tanggal', '>=', $filters['start_date']);
                }
                if (!empty($filters['end_date'])) {
                    $q->whereDate('tanggal', '<=', $filters['end_date']);
                }
            })
            ->whereIn('id_akun', $id_akun_persediaan)
            ->get();

        $id_akun_terpakai = $transaksi->pluck('id_akun')->unique();

        // 🔢 Saldo awal hanya untuk akun yang digunakan dalam transaksi tersebut
        $saldo_awal = Akun::whereIn('id_akun', $id_akun_terpakai)->sum('saldo_awal_debit');

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        $saldo_akhir = $saldo_awal + $total_debit - $total_kredit;
        $penurunan_tahun_ini = max(0, $saldo_awal - $saldo_akhir);

        // =========================
        // 🔹 PERIODE TAHUN LALU
        // =========================

        $tahun_lalu = $tahun - 1;
        $tahun_dua_lalu = $tahun - 2;

        $saldo_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun_persediaan)
            ->sum('saldo_akhir');

        $saldo_dua_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_dua_lalu)
            ->whereIn('id_akun', $id_akun_persediaan)
            ->sum('saldo_akhir');

        $penurunan_tahun_lalu = max(0, $saldo_dua_lalu - $saldo_lalu);

        return [
            'tahun_ini' => $penurunan_tahun_ini,
            'tahun_lalu' => $penurunan_tahun_lalu,
        ];
    }






    private function hitungPenurunanBiayaDibayarDimuka($tahun, $filters)
    {
        $id_akun_bdd = Akun::where('akun', 'Beban Dibayar Dimuka')->pluck('id_akun');

        $tahun_lalu = $tahun - 1;

        // 🔍 Ambil jurnal umum yang sudah diposting
        $jurnalPosted = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // ==========================
        // 🔹 SALDO TAHUN LALU (global)
        // ==========================
        $saldo_tahun_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun_bdd)
            ->sum('saldo_akhir');

        // ==========================
        // 🔹 PERIODE TAHUN BERJALAN
        // ==========================
        $transaksi = Detail_Jurnal_Umum::whereIn('id_jurnal_umum', $jurnalPosted)
            ->whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);

                if (!empty($filters['id_unit'])) {
                    $q->where('id_unit', $filters['id_unit']);
                }
                if (!empty($filters['id_divisi'])) {
                    $q->where('id_divisi', $filters['id_divisi']);
                }
                if (!empty($filters['start_date'])) {
                    $q->whereDate('tanggal', '>=', $filters['start_date']);
                }
                if (!empty($filters['end_date'])) {
                    $q->whereDate('tanggal', '<=', $filters['end_date']);
                }
            })
            ->whereIn('id_akun', $id_akun_bdd)
            ->get();

        $id_akun_terpakai = $transaksi->pluck('id_akun')->unique();

        // Hitung saldo awal hanya dari akun yang digunakan dalam transaksi
        $saldo_awal = Akun::whereIn('id_akun', $id_akun_terpakai)->sum('saldo_awal_debit');

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        $saldo_akhir_tahun_ini = $saldo_awal + $total_debit - $total_kredit;

        $penurunan_tahun_ini = ($total_debit == 0 && $total_kredit == 0)
            ? 0
            : max(0, $saldo_tahun_lalu - $saldo_akhir_tahun_ini);

        // Nilai ini sebenarnya tidak relevan, tapi untuk konsistensi tampilan
        $penurunan_tahun_lalu = $saldo_tahun_lalu;

        return [
            'tahun_ini' => $penurunan_tahun_ini,
            'tahun_lalu' => $penurunan_tahun_lalu,
        ];
    }






    private function hitungPenurunanAktivaLancarLainnya($tahun, $filters)
    {
        $akun = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Aset Lancar Lainnya');
        })->get();

        $id_akun = $akun->pluck('id_akun');

        $tahun_lalu = $tahun - 1;

        // 🧾 Saldo awal dari tahun lalu
        $saldo_awal = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        // 📌 Ambil jurnal yang sudah diposting
        $jurnalPosted = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // 🔍 Transaksi tahun ini (dengan filter)
        $transaksi = Detail_Jurnal_Umum::whereIn('id_akun', $id_akun)
            ->whereIn('id_jurnal_umum', $jurnalPosted)
            ->whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })->get();

        // 💰 Transaksi tahun ini
        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        // 📉 Hitung saldo akhir tahun ini
        $saldo_akhir = $saldo_awal + $total_debit - $total_kredit;

        $penurunan_tahun_ini = max(0, $saldo_awal - $saldo_akhir); // ✅ Bukan dari $saldo_tahun_lalu

        return [
            'tahun_ini' => $penurunan_tahun_ini,
            'tahun_lalu' => $saldo_awal, // atau 'saldo_awal'
        ];
    }




    private function hitungKenaikanHutang($tahun, $filters)
    {
        $akunHutang = Akun::whereIn('akun', [
            'Sumbangan Diterima Dimuka', 
            'Hutang Rekanan', 
            'Hutang Kegiatan', 
            'Hutang Lancar Lainnya',
        ])->get();

        $id_akun = $akunHutang->pluck('id_akun');
        $tahun_lalu = $tahun - 1;
        $tahun_dua_lalu = $tahun - 2;

        // ✅ Tidak difilter unit/divisi karena saldo_akhir_tahun tidak punya kolom itu
        $saldo_awal = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        // Ambil jurnal yang sudah diposting
        $jurnalPosted = DB::table('buku_besar')->pluck('id_jurnal_umum');

        $transaksi = Detail_Jurnal_Umum::whereIn('id_jurnal_umum', $jurnalPosted)
            ->whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_akun', $id_akun)
            ->get();

        $id_akun_terpakai = $transaksi->pluck('id_akun')->unique();

        $total_saldo_awal_kredit = Akun::whereIn('id_akun', $id_akun_terpakai)->sum('saldo_awal_kredit');
        $total_saldo_awal_debit = Akun::whereIn('id_akun', $id_akun_terpakai)->sum('saldo_awal_debit');

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        $saldo_akhir = $total_saldo_awal_kredit - $total_saldo_awal_debit + $total_kredit - $total_debit;

        $kenaikan_tahun_ini = max(0, $saldo_akhir - $saldo_awal);

        // Tahun lalu (tanpa filter unit/divisi)
        $saldo_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $saldo_dua_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_dua_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $kenaikan_tahun_lalu = max(0, $saldo_lalu - $saldo_dua_lalu);

        return [
            'tahun_ini' => $kenaikan_tahun_ini,
            'tahun_lalu' => $kenaikan_tahun_lalu,
        ];
    }





    private function hitungKenaikanHutangBiaya($tahun, $filters)
    {
        $akunHutangBiaya = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Kewajiban Jangka Pendek');
        })->get();

        $id_akun = $akunHutangBiaya->pluck('id_akun');

        $tahun_lalu = $tahun - 1;
        $tahun_dua_lalu = $tahun - 2;

        // Tidak difilter unit/divisi karena tidak tersedia
        $saldo_awal = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        // Ambil jurnal yang sudah diposting
        $jurnalPosted = DB::table('buku_besar')->pluck('id_jurnal_umum');

        $transaksi = Detail_Jurnal_Umum::whereIn('id_akun', $id_akun)
            ->whereIn('id_jurnal_umum', $jurnalPosted)
            ->whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->get();

        // Ambil akun yang benar-benar muncul di transaksi untuk perhitungan saldo awal
        $id_akun_terpakai = $transaksi->pluck('id_akun')->unique();

        $total_saldo_awal_kredit = Akun::whereIn('id_akun', $id_akun_terpakai)->sum('saldo_awal_kredit');
        $total_saldo_awal_debit = Akun::whereIn('id_akun', $id_akun_terpakai)->sum('saldo_awal_debit');

        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');
        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');

        $saldo_akhir = $total_saldo_awal_kredit - $total_saldo_awal_debit + $total_kredit - $total_debit;

        $kenaikan_tahun_ini = max(0, $saldo_akhir - $saldo_awal);

        // Tahun lalu vs dua tahun lalu (tidak difilter unit/divisi)
        $saldo_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $saldo_dua_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_dua_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $kenaikan_tahun_lalu = max(0, $saldo_lalu - $saldo_dua_lalu);

        return [
            'tahun_ini' => $kenaikan_tahun_ini,
            'tahun_lalu' => $kenaikan_tahun_lalu,
        ];
    }




    private function hitungKenaikanPiutang($tahun, $filters)
    {
        $akunPiutang = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'like', '%Piutang%');
        })->get();

        $id_akun_piutang = $akunPiutang->pluck('id_akun');

        $tahun_lalu = $tahun - 1;
        $tahun_dua_lalu = $tahun - 2;

        // Ambil jurnal yang sudah diposting
        $jurnalPosted = DB::table('buku_besar')->pluck('id_jurnal_umum');

        $transaksi = Detail_Jurnal_Umum::whereIn('id_akun', $id_akun_piutang)
            ->whereIn('id_jurnal_umum', $jurnalPosted)
            ->whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->get();

        $id_akun_terpakai = $transaksi->pluck('id_akun')->unique();

        $total_saldo_awal = Akun::whereIn('id_akun', $id_akun_terpakai)
            ->sum(DB::raw('saldo_awal_debit - saldo_awal_kredit'));

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        $saldo_akhir = $total_saldo_awal + $total_debit - $total_kredit;

        $kenaikan_tahun_ini = max(0, $saldo_akhir - $total_saldo_awal);

        // Tahun lalu: dari dua tahun ke belakang (tanpa filter unit/divisi)
        $saldo_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun_piutang)
            ->sum('saldo_akhir');

        $saldo_dua_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_dua_lalu)
            ->whereIn('id_akun', $id_akun_piutang)
            ->sum('saldo_akhir');

        $kenaikan_tahun_lalu = max(0, $saldo_lalu - $saldo_dua_lalu);

        return [
            'tahun_ini' => $kenaikan_tahun_ini,
            'tahun_lalu' => $kenaikan_tahun_lalu,
        ];
    }









    private function hitungKenaikanPersediaan($tahun, $filters)
    {
        $akunPersediaan = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Persediaan');
        })->get();

        $id_akun_persediaan = $akunPersediaan->pluck('id_akun');
        $postedJurnal = DB::table('buku_besar')->pluck('id_jurnal_umum');

        $tahun_lalu = $tahun - 1;

        // Tanpa filter unit/divisi karena tabel tidak menyimpan info itu
        $saldo_tahun_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun_persediaan)
            ->sum('saldo_akhir');

        // Hitung saldo awal dari akun terpakai saja
        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun_persediaan)
            ->get();

        $id_akun_digunakan = $transaksi->pluck('id_akun')->unique();

        $saldo_awal = Akun::whereIn('id_akun', $id_akun_digunakan)->sum('saldo_awal_debit');
        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        $saldo_akhir_tahun_ini = $saldo_awal + $total_debit - $total_kredit;
        $kenaikan_tahun_ini = max(0, $saldo_akhir_tahun_ini - $saldo_tahun_lalu);

        // Tahun lalu = saldo akhir tahun lalu (sudah didapat)
        return [
            'tahun_ini' => $kenaikan_tahun_ini,
            'tahun_lalu' => $saldo_tahun_lalu,
        ];
    }



    private function hitungKenaikanBiayaDibayarDimuka($tahun, $filters)
    {
        $akun = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Beban Dibayar Dimuka');
        })->get();

        $id_akun = $akun->pluck('id_akun');
        $postedJurnal = DB::table('buku_besar')->pluck('id_jurnal_umum');

        $tahun_lalu = $tahun - 1;
        $tahun_dua_lalu = $tahun - 2;

        // Saldo tahun lalu & dua tahun lalu → tanpa filter unit/divisi
        $saldo_tahun_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $saldo_dua_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_dua_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        // Transaksi tahun berjalan
        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun)
            ->get();

        $id_akun_dipakai = $transaksi->pluck('id_akun')->unique();

        $saldo_awal = Akun::whereIn('id_akun', $id_akun_dipakai)->sum('saldo_awal_debit');
        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        $saldo_akhir = $saldo_awal + $total_debit - $total_kredit;

        $kenaikan_tahun_ini = ($total_debit == 0 && $total_kredit == 0)
            ? 0
            : max(0, $saldo_akhir - $saldo_tahun_lalu);

        $kenaikan_tahun_lalu = max(0, $saldo_tahun_lalu - $saldo_dua_lalu);

        return [
            'tahun_ini' => $kenaikan_tahun_ini,
            'tahun_lalu' => $kenaikan_tahun_lalu,
        ];
    }




    private function hitungKenaikanAktivaLancarLainnya($tahun, $filters)
    {
        $akun = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Aset Lancar Lainnya');
        })->get();

        $id_akun = $akun->pluck('id_akun');
        $postedJurnal = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // 🔹 Tahun berjalan
        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun)
            ->get();

        $id_akun_terpakai = $transaksi->pluck('id_akun')->unique();
        $saldo_awal = Akun::whereIn('id_akun', $id_akun_terpakai)->sum('saldo_awal_debit');

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');
        $saldo_akhir = $saldo_awal + $total_debit - $total_kredit;
        $kenaikan_tahun_ini = max(0, $saldo_akhir - $saldo_awal);

        // 🔹 Tahun lalu: tanpa filter unit
        $tahun_lalu = $tahun - 1;
        $tahun_dua_lalu = $tahun - 2;

        $saldo_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $saldo_dua_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_dua_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $kenaikan_tahun_lalu = max(0, $saldo_lalu - $saldo_dua_lalu);

        return [
            'tahun_ini' => $kenaikan_tahun_ini,
            'tahun_lalu' => $kenaikan_tahun_lalu,
        ];
    }




    private function hitungPenurunanHutang($tahun, $filters)
    {
        $akunHutang = Akun::whereIn('akun', [
            'Sumbangan Diterima Dimuka',
            'Hutang Rekanan',
            'Hutang Kegiatan',
            'Hutang Lancar Lainnya',
        ])->get();

        $id_akun = $akunHutang->pluck('id_akun');
        $postedJurnal = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // 🔹 Tahun berjalan
        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun)
            ->get();

        $id_akun_terpakai = $transaksi->pluck('id_akun')->unique();
        $saldo_awal = Akun::whereIn('id_akun', $id_akun_terpakai)->sum('saldo_awal_kredit');

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        $saldo_akhir = $saldo_awal + $total_kredit - $total_debit;
        $penurunan_tahun_ini = max(0, $saldo_awal - $saldo_akhir);

        // 🔹 Tahun lalu: tanpa filter unit
        $tahun_lalu = $tahun - 1;
        $tahun_dua_lalu = $tahun - 2;

        $saldo_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $saldo_dua_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_dua_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $penurunan_tahun_lalu = max(0, $saldo_dua_lalu - $saldo_lalu);

        return [
            'tahun_ini' => $penurunan_tahun_ini,
            'tahun_lalu' => $penurunan_tahun_lalu,
        ];
    }





    private function hitungPenurunanHutangBiaya($tahun, $filters)
    {
        $akun = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Kewajiban Jangka Pendek');
        })->get();

        $id_akun = $akun->pluck('id_akun');
        $tahun_lalu = $tahun - 1;
        $postedJurnal = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // Saldo akhir tahun lalu (❌ TANPA filter unit)
        $saldo_tahun_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        // Hitung saldo akhir berjalan
        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun)
            ->get();

        // Hitung saldo awal hanya dari akun yang terpakai dalam transaksi berjalan
        $id_akun_terpakai = $transaksi->pluck('id_akun')->unique();
        $saldo_awal_kredit = Akun::whereIn('id_akun', $id_akun_terpakai)->sum('saldo_awal_kredit');
        $saldo_awal_debit = Akun::whereIn('id_akun', $id_akun_terpakai)->sum('saldo_awal_debit');
        $saldo_awal = $saldo_awal_kredit - $saldo_awal_debit;

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        $saldo_akhir_tahun_ini = $saldo_awal + $total_kredit - $total_debit;
        $penurunan_tahun_ini = ($total_debit == 0 && $total_kredit == 0)
            ? 0
            : max(0, $saldo_tahun_lalu - $saldo_akhir_tahun_ini);

        return [
            'tahun_ini' => $penurunan_tahun_ini,
            'tahun_lalu' => $saldo_tahun_lalu,
        ];
    }





    private function hitungKasMasukPenjualanAsetTetap($tahun, $filters)
    {
        $akunAsetTetap = Akun::whereIn('akun', [
            'Tanah',
            'Bangunan',
            'Bangunan Dalam Proses',
            'Inventaris Kantor',
            'Peralatan',
            'Kendaraan',
            'Aktiva Tetap Lainnya'
        ])->get();

        $id_akun = $akunAsetTetap->pluck('id_akun');
        $postedJurnal = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // 🔹 Tahun berjalan
        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun)
            ->where('debit_kredit', 'kredit')
            ->sum('nominal');

        // 🔹 Tahun lalu (tanpa filter unit)
        $tahun_lalu = $tahun - 1;
        $transaksi_lalu = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun_lalu, $filters) {
                $q->whereYear('tanggal', $tahun_lalu);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun)
            ->where('debit_kredit', 'kredit')
            ->sum('nominal');

        return [
            'tahun_ini' => $transaksi,
            'tahun_lalu' => $transaksi_lalu,
        ];
    }







    private function hitungKasKeluarPembelianAsetTetap($tahun, $filters)
    {
        $akunAsetTetap = Akun::whereHas('sub_kategori_akun', function ($query) {
        $query->where('sub_kategori_akun', 'Aktiva Tetap');
        })
        ->whereHas('detail_jurnal_umum.jurnal_umum', function ($query) use ($filters) {
            if (!empty($filters['id_unit'])) {
                $query->where('id_unit', $filters['id_unit']);
            }
            if (!empty($filters['id_divisi'])) {
                $query->where('id_divisi', $filters['id_divisi']);
            }
        })
        ->get();


        $id_akun = $akunAsetTetap->pluck('id_akun');
        $tahun_lalu = $tahun - 1;
        $postedJurnal = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // ❌ JANGAN filter unit saat ambil saldo tahun lalu
        $saldo_tahun_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $saldo_awal = $akunAsetTetap->sum('saldo_awal_debit') - $akunAsetTetap->sum('saldo_awal_kredit');

        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun)
            ->get();

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        $saldo_akhir_tahun_ini = $saldo_awal + $total_debit - $total_kredit;

        $kas_keluar_tahun_ini = max(0, $saldo_akhir_tahun_ini - $saldo_tahun_lalu);

        return [
            'tahun_ini' => $kas_keluar_tahun_ini,
            'tahun_lalu' => $saldo_tahun_lalu,
        ];
    }




    private function hitungKasKeluarInventaris($tahun, $filters)
    {
        $akunInventaris = Akun::whereIn('akun', [
            'Inventaris Kantor',
            'Peralatan',
            'Meubelair',
        ])
        ->whereHas('detail_jurnal_umum.jurnal_umum', function ($query) use ($filters) {
            if (!empty($filters['id_unit'])) {
                $query->where('id_unit', $filters['id_unit']);
            }
            if (!empty($filters['id_divisi'])) {
                $query->where('id_divisi', $filters['id_divisi']);
            }
        })
        ->get();


        $id_akun = $akunInventaris->pluck('id_akun');
        $postedJurnal = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // Tahun ini
        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun)
            ->where('debit_kredit', 'debit')
            ->sum('nominal');

        // Tahun lalu (❌ tanpa filter unit/divisi)
        $tahun_lalu = $tahun - 1;
        $transaksi_lalu = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun_lalu, $filters) {
                $q->whereYear('tanggal', $tahun_lalu);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun)
            ->where('debit_kredit', 'debit')
            ->sum('nominal');

        return [
            'tahun_ini' => $transaksi,
            'tahun_lalu' => $transaksi_lalu,
        ];
    }




    private function hitungKenaikanPinjamanJangkaPanjang($tahun, $filters)
    {
        $akun = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Kewajiban Jangka Panjang');
        })->get();

        $id_akun = $akun->pluck('id_akun');
        $postedJurnal = DB::table('buku_besar')->pluck('id_jurnal_umum');

        $tahun_lalu = $tahun - 1;
        $tahun_dua_lalu = $tahun - 2;

        // Saldo tahun lalu TANPA filter unit/divisi (akumulasi)
        $saldo_tahun_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        // Transaksi tahun ini, filter berdasarkan jurnal_umum
        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun)
            ->get();

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        $saldo_akhir = $saldo_tahun_lalu + $total_kredit - $total_debit;

        $kenaikan_tahun_ini = ($total_debit == 0 && $total_kredit == 0)
            ? 0
            : max(0, $saldo_akhir - $saldo_tahun_lalu);

        // Saldo tahun lalu & dua tahun lalu, untuk perbandingan
        $saldo_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $saldo_dua_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_dua_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        $kenaikan_tahun_lalu = max(0, $saldo_lalu - $saldo_dua_lalu);

        return [
            'tahun_ini' => $kenaikan_tahun_ini,
            'tahun_lalu' => $kenaikan_tahun_lalu,
        ];
    }





    private function hitungPenurunanPinjamanJangkaPanjang($tahun, $filters)
    {
        $akun = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Kewajiban Jangka Panjang');
        })->get();

        $id_akun = $akun->pluck('id_akun');
        $tahun_lalu = $tahun - 1;
        $postedJurnal = DB::table('buku_besar')->pluck('id_jurnal_umum');

        // Saldo tahun lalu TANPA filter unit/divisi
        $saldo_tahun_lalu = DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun_lalu)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');

        // Transaksi tahun ini berdasarkan jurnal_umum
        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
                $q->whereYear('tanggal', $tahun);
                if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
                if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
                if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
            })
            ->whereIn('id_jurnal_umum', $postedJurnal)
            ->whereIn('id_akun', $id_akun)
            ->get();

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        $saldo_akhir = $saldo_tahun_lalu + $total_kredit - $total_debit;

        $penurunan_tahun_ini = ($total_debit == 0 && $total_kredit == 0)
            ? 0
            : max(0, $saldo_tahun_lalu - $saldo_akhir);

        return [
            'tahun_ini' => $penurunan_tahun_ini,
            'tahun_lalu' => $saldo_tahun_lalu,
        ];
    }



    private function getSaldoKasTunaiAwal($tahun, $filters)
    {
        $akunKas = Akun::whereHas('sub_kategori_akun', function ($query) {
                $query->where('sub_kategori_akun', 'Kas');
            })
            ->whereHas('detail_jurnal_umum.jurnal_umum', function ($q) use ($filters) {
                if (!empty($filters['id_unit'])) {
                    $q->where('id_unit', $filters['id_unit']);
                }
                if (!empty($filters['id_divisi'])) {
                    $q->where('id_divisi', $filters['id_divisi']);
                }
            });

        $id_akun = $akunKas->pluck('id_akun');

        return DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun - 1)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');
    }


    private function getSaldoBankAwal($tahun, $filters)
    {
        $akunBank = Akun::whereHas('sub_kategori_akun', function ($query) {
                $query->where('sub_kategori_akun', 'Bank');
            })
            ->whereHas('detail_jurnal_umum.jurnal_umum', function ($q) use ($filters) {
                if (!empty($filters['id_unit'])) {
                    $q->where('id_unit', $filters['id_unit']);
                }
                if (!empty($filters['id_divisi'])) {
                    $q->where('id_divisi', $filters['id_divisi']);
                }
            });

        $id_akun = $akunBank->pluck('id_akun');

        return DB::table('saldo_akhir_tahun')
            ->where('tahun', $tahun - 1)
            ->whereIn('id_akun', $id_akun)
            ->sum('saldo_akhir');
    }




    private function getSaldoKasTunai($tahun, $filters)
    {
        $akunKas = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Kas');
        })->get();

        $id_akun = $akunKas->pluck('id_akun');

        $saldo_awal_debit = $akunKas->sum('saldo_awal_debit');
        $saldo_awal_kredit = $akunKas->sum('saldo_awal_kredit');
        $saldo_awal = $saldo_awal_debit - $saldo_awal_kredit;

        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
            $q->whereYear('tanggal', $tahun);
            if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
            if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
            if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
            if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
        })
        ->whereIn('id_akun', $id_akun)
        ->get();

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        return $saldo_awal + $total_debit - $total_kredit;
    }


    private function getSaldoBank($tahun, $filters)
    {
        $akunBank = Akun::whereHas('sub_kategori_akun', function ($query) {
            $query->where('sub_kategori_akun', 'Bank');
        })->get();

        $id_akun = $akunBank->pluck('id_akun');

        $saldo_awal_debit = $akunBank->sum('saldo_awal_debit');
        $saldo_awal_kredit = $akunBank->sum('saldo_awal_kredit');
        $saldo_awal = $saldo_awal_debit - $saldo_awal_kredit;

        $transaksi = Detail_Jurnal_Umum::whereHas('jurnal_umum', function ($q) use ($tahun, $filters) {
            $q->whereYear('tanggal', $tahun);
            if (!empty($filters['id_unit'])) $q->where('id_unit', $filters['id_unit']);
            if (!empty($filters['id_divisi'])) $q->where('id_divisi', $filters['id_divisi']);
            if (!empty($filters['start_date'])) $q->whereDate('tanggal', '>=', $filters['start_date']);
            if (!empty($filters['end_date'])) $q->whereDate('tanggal', '<=', $filters['end_date']);
        })
        ->whereIn('id_akun', $id_akun)
        ->get();

        $total_debit = $transaksi->where('debit_kredit', 'debit')->sum('nominal');
        $total_kredit = $transaksi->where('debit_kredit', 'kredit')->sum('nominal');

        return $saldo_awal + $total_debit - $total_kredit;
    }






















}
