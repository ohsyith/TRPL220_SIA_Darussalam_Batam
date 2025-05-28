@extends('layouts.layout')
@push('styles')
    <title>SIA Yayasan Darussalam | Laporan Neraca</title>

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
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- Header dan Tombol Aksi -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Laporan Arus Kas - {{ $tahun }}</h5>
                        <div class="action-buttons">
                            <a href="{{ route('arus-kas.index', ['tahun' => $tahun, 'export_excel' => 1]) }}"
                                class="btn btn-success">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </a>
                            <button class="btn btn-secondary ms-2" onclick="printLaporan()">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                    </div>

                    <!-- Filter Tahun -->
                    <form method="GET" action="{{ route('arus-kas.index') }}">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="tahun" class="form-label">Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="{{ $tahun }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('arus-kas.index') }}" class="btn btn-secondary w-100 ms-2">
                                    <i class="ti ti-refresh fs-4"></i>
                                </a>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-search fs-4"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Tabel Arus Kas -->
                    <div id="print-area">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Keterangan</th>
                                    <th class="text-end">Jumlah (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aktivitas Operasi -->
                                <tr class="table-primary fw-bold">
                                    <td colspan="2">Arus Kas dari Aktivitas Operasi</td>
                                </tr>
                                <tr>
                                    <td>Kenaikan (Penurunan) Penghasilan Komprehensif</td>
                                    <td class="text-end">Rp {{ number_format($kasOperasi, 2) }}</td>
                                </tr>

                                <!-- Aktivitas Investasi -->
                                <tr class="table-primary fw-bold">
                                    <td colspan="2">Arus Kas dari Aktivitas Investasi</td>
                                </tr>
                                <tr>
                                    <td>(Penambahan)/Pengurangan Aset Tetap</td>
                                    <td class="text-end">Rp {{ number_format($kasInvestasi, 2) }}</td>
                                </tr>

                                <!-- Aktivitas Pendanaan -->
                                <tr class="table-primary fw-bold">
                                    <td colspan="2">Arus Kas dari Aktivitas Pendanaan</td>
                                </tr>
                                <tr>
                                    <td>Penambahan (Penurunan) Kewajiban Jangka Panjang</td>
                                    <td class="text-end">Rp {{ number_format($kasPendanaan, 2) }}</td>
                                </tr>

                                <!-- Kenaikan Kas -->
                                <tr class="table-success fw-bold">
                                    <td>Kenaikan (Penurunan) Kas dan Setara Kas</td>
                                    <td class="text-end">Rp {{ number_format($kenaikanKas, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Kas dan Setara Kas Awal Periode</td>
                                    <td class="text-end">Rp {{ number_format($kasAwal, 2) }}</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>Saldo Kas dan Setara Kas Akhir Periode</td>
                                    <td class="text-end">Rp {{ number_format($kasAkhir, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- CSS Print -->
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

                            .table-dark,
                            .table-primary,
                            .table-success {
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                        }
                    </style>

                </div>
            </div>
        </div>

        <div class="py-6 px-6 text-center">
            <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function printLaporan() {
            window.print();
        }
    </script>

    </div>
    <script>
        function printLaporan() {
            window.print();
        }
    </script>
@endpush
