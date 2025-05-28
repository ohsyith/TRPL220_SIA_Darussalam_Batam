@extends('layouts.layout')


@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* Mengatur warna hijau tua untuk tombol Export Excel */
        .custom-green {
            background-color: #208a20;
            /* Warna hijau tua */
            border: none;
        }

        /* Mengatur warna abu-abu tua untuk tombol Print */
        .custom-grey {
            background-color: #8a8a8a;
            /* Warna abu-abu tua */
            border: none;
            color: white;
            /* Warna teks putih */
        }
    </style>

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

            .print-header,
            .print-footer {
                display: block !important;
            }

            .d-none {
                display: block !important;
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

            .table-success {
                background-color: #d1e7dd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Laporan Komprehensif</h5>
                        <div class="action-buttons">




                            <!-- Tombol Export Excel dengan warna hijau tua -->
                            <a href="{{ url('/laporan-komprehensif?export_excel=1&tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai) }}"
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
                        <form method="GET" action="/laporan-komprehensif">
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Awal</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control"
                                        value="{{ $tanggal_mulai }}">
                                </div>
                                <div class="col-md-5">
                                    <label for="tanggal_selesai" class="form-label">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control"
                                        value="{{ $tanggal_selesai }}">
                                </div>

                                <div class="col-md-1 d-flex align-items-end">
                                    <a href="{{ route('laporan-komprehensif.index') }}"
                                        class="btn btn-secondary w-100 ms-2">
                                        <i class="ti ti-refresh fs-4"></i>
                                    </a>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100"> <i class="ti ti-search fs-4"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <br>

                        <div id="print-area">
                            <!-- Header khusus untuk cetak -->
                            <div class="print-header text-center d-none">
                                <h3>LAPORAN KOMPREHENSIF</h3>
                                <h4>YAYASAN DARUSSALAM BATAM</h4>
                                <p>Periode: {{ date('d/m/Y', strtotime($tanggal_mulai)) }} -
                                    {{ date('d/m/Y', strtotime($tanggal_selesai)) }}</p>
                                <hr>
                            </div>

                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Akun</th>
                                        <th>Tanpa Pembatasan</th>
                                        <th>Dengan Pembatasan</th>
                                        <th class="text-end">Jumlah (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Pendapatan -->
                                    <tr class="table-secondary">
                                        <td colspan="4"><strong>Pendapatan</strong></td>
                                    </tr>
                                    @php $total_pendapatan_all = 0; @endphp
                                    @foreach ($pendapatan_all as $item)
                                        <tr>
                                            <td>&nbsp;&nbsp;&nbsp;{{ $item->nama_akun }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($item->total_tanpa, 2, ',', '.') }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($item->total_dengan, 2, ',', '.') }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($item->total, 2, ',', '.') }}</td>
                                        </tr>
                                        @php $total_pendapatan_all += $item->total; @endphp
                                    @endforeach
                                    <tr class="fw-bold">
                                        <td>Total Pendapatan</td>
                                        <td class="text-end">Rp
                                            {{ number_format($total_pendapatan, 2, ',', '.') }}</td>
                                        <td class="text-end">Rp
                                            {{ number_format($total_pendapatan_terikat, 2, ',', '.') }}
                                        </td>
                                        <td class="text-end">Rp
                                            {{ number_format($total_pendapatan_all, 2, ',', '.') }}</td>
                                    </tr>

                                    <!-- Beban -->
                                    <tr class="table-secondary">
                                        <td colspan="4"><strong>Beban</strong></td>
                                    </tr>
                                    @php $total_beban_all = 0; @endphp
                                    @foreach ($beban_all as $item)
                                        <tr>
                                            <td>&nbsp;&nbsp;&nbsp;{{ $item->nama_akun }}</td>
                                            <td class="text-end">(Rp
                                                {{ number_format($item->total_tanpa, 2, ',', '.') }})</td>
                                            <td class="text-end">(Rp
                                                {{ number_format($item->total_dengan, 2, ',', '.') }})</td>
                                            <td class="text-end">(Rp
                                                {{ number_format($item->total, 2, ',', '.') }})</td>
                                        </tr>
                                        @php $total_beban_all += $item->total; @endphp
                                    @endforeach
                                    <tr class="fw-bold">
                                        <td>Total Beban</td>
                                        <td class="text-end">(Rp
                                            {{ number_format($total_beban, 2, ',', '.') }})</td>
                                        <td class="text-end">(Rp
                                            {{ number_format($total_beban_terikat, 2, ',', '.') }})</td>
                                        <td class="text-end">(Rp
                                            {{ number_format($total_beban_all, 2, ',', '.') }})</td>
                                    </tr>

                                    <!-- Kenaikan (Penurunan) -->
                                    <tr class="table-success fw-bold">
                                        <td>KENAIKAN (PENURUNAN) PENGHASILAN KOMPREHENSIF</td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end">Rp
                                            {{ number_format($kenaikan_penghasilan_komprehensif, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Footer khusus untuk cetak -->
                            <div class="print-footer text-center d-none mt-3">
                                <p>Sistem Informasi Akuntansi Yayasan Darussalam Batam |
                                    {{ date('Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-6 px-6 text-center">
            <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | {{ date('Y') }}
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function printLaporan() {
            window.print();
        }
    </script>
@endpush
