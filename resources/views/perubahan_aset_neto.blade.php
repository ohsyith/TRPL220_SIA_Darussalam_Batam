@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Laporan Perubahan Aset Neto</h5>
                        <div class="action-buttons">
                            <!-- Tombol Export Excel dengan warna hijau tua -->
                            <a href="{{ route('perubahan-aset-neto.index', ['export_excel' => 1, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                                class="btn btn-success custom-green">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </a>

                            <!-- Tombol Print dengan warna abu-abu tua -->
                            <button class="btn btn-secondary ms-2 custom-grey" onclick="printLaporan()">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <br>

                        <form method="GET" action="{{ route('perubahan-aset-neto.index') }}">
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <label for="start_date" class="form-label">Tanggal Awal</label>
                                    <input type="date" class="form-control" name="start_date"
                                        value="{{ request('start_date', date('Y-01-01')) }}">
                                </div>
                                <div class="col-md-5">
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" name="end_date"
                                        value="{{ request('end_date', date('Y-m-d')) }}">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <a href="{{ route('perubahan-aset-neto.index') }}"
                                        class="btn btn-secondary w-100 ms-2">
                                        <i class="ti ti-refresh fs-4"></i>
                                    </a>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ti ti-search fs-4"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Tabel Perubahan Aset Neto -->
                        <div id="print-area">
                            <div class="text-center mb-4">
                                <h4>YAYASAN DARUSSALAM BATAM</h4>
                                <h5>LAPORAN PERUBAHAN ASET NETO</h5>
                                <p>Periode Laporan: {{ date('d M Y', strtotime(request('start_date', date('Y-01-01')))) }} - {{ date('d M Y', strtotime(request('end_date', date('Y-m-d')))) }}</p>
                            </div>

                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="60%">Keterangan</th>
                                        <th class="text-end" width="40%">Jumlah (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Aset Neto Dengan Pembatasan -->
                                    <tr class="table-primary fw-bold">
                                        <td colspan="2">ASET NETO DENGAN PEMBATASAN SUMBER DAYA</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;Saldo Awal</td>
                                        <td class="text-end">Rp {{ number_format($data['dengan_pembatasan']['saldo_awal'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;Kenaikan (Penurunan) Aset Neto Periode Lalu</td>
                                        <td class="text-end">Rp {{ number_format($data['dengan_pembatasan']['perubahan_periode_lalu'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;Kenaikan (Penurunan) Aset Neto Periode Berjalan</td>
                                        <td class="text-end">Rp {{ number_format($data['dengan_pembatasan']['perubahan_periode_berjalan'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="table-secondary">
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Saldo Akhir Aset Neto Dengan Pembatasan</strong></td>
                                        <td class="text-end"><strong>Rp {{ number_format($data['dengan_pembatasan']['saldo_akhir'], 0, ',', '.') }}</strong></td>
                                    </tr>
                                    
                                    <!-- Aset Neto Tanpa Pembatasan -->
                                    <tr class="table-primary fw-bold">
                                        <td colspan="2">ASET NETO TANPA PEMBATASAN SUMBER DAYA</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;Saldo Awal</td>
                                        <td class="text-end">Rp {{ number_format($data['tanpa_pembatasan']['saldo_awal'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;Kenaikan (Penurunan) Aset Neto Periode Lalu</td>
                                        <td class="text-end">Rp {{ number_format($data['tanpa_pembatasan']['perubahan_periode_lalu'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;Kenaikan (Penurunan) Aset Neto Periode Berjalan</td>
                                        <td class="text-end">Rp {{ number_format($data['tanpa_pembatasan']['perubahan_periode_berjalan'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="table-secondary">
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Saldo Akhir Aset Neto Tanpa Pembatasan</strong></td>
                                        <td class="text-end"><strong>Rp {{ number_format($data['tanpa_pembatasan']['saldo_akhir'], 0, ',', '.') }}</strong></td>
                                    </tr>
                                </tbody>
                                <!-- Row Total -->
                                <tfoot class="fw-bold bg-light">
                                    <tr>
                                        <td>TOTAL SALDO AKHIR ASET NETO</td>
                                        <td class="text-end">Rp {{ number_format($data['total_saldo_akhir'], 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- Tanda Tangan -->
                            <div class="row mt-5">
                                <div class="col-12 text-end">
                                    <p>Batam, {{ date('d M Y', strtotime(request('end_date', date('Y-m-d')))) }}</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3 text-center">
                                    <p>Mengetahui,</p>
                                    <p class="mb-5">Ketua Yayasan</p>
                                    <p><u>H. Asep Rohmat, ST</u></p>
                                </div>
                                <div class="col-3 text-center">
                                    <p>&nbsp;</p>
                                    <p class="mb-5">Bendahara Yayasan</p>
                                    <p><u>Marno, S.Pd</u></p>
                                </div>
                                <div class="col-3 text-center">
                                    <p>Dibuat Oleh:</p>
                                    <p class="mb-5">Kepala Dep. Finance</p>
                                    <p><u>Kurnia Candrawati, SE</u></p>
                                </div>
                                <div class="col-3 text-center">
                                    <p>&nbsp;</p>
                                    <p class="mb-5">Bagian Keuangan</p>
                                    <p><u>Umi Rahayu, SHI</u></p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <p>Diperiksa Oleh:</p>
                                    <p class="mb-5">&nbsp;</p>
                                    <p><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></p>
                                </div>
                            </div>
                        </div>

                        <style>
                            @media print {
                                body * {
                                    visibility: hidden;
                                }

                                #print-area,
                                #print-area * {
                                    visibility: visible;
                                }

                                #print-area {
                                    position: absolute;
                                    left: 0;
                                    top: 0;
                                    width: 100%;
                                }

                                .table-dark {
                                    background-color: #212529 !important;
                                    color: white !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }

                                .table-secondary {
                                    background-color: #e2e3e5 !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }

                                .table-primary {
                                    background-color: #cfe2ff !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }
                            }
                        </style>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-6 px-6 text-center">
            <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>
        </div>
    </div>
</div>

<script>
    function printLaporan() {
        window.print();
    }
</script>
@endsection