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
                        <h5 class="card-title">Laporan Arus Kas</h5>
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

                    <form method="GET" action="{{ route('arus-kas.index') }}">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="unit" class="form-label">Unit</label>
                                <select name="unit" id="unit" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Semua Unit --</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id_unit }}"
                                            {{ request('unit') == $unit->id_unit ? 'selected' : '' }}>
                                            {{ $unit->unit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="divisi" class="form-label">Divisi</label>
                                <select name="divisi" id="divisi" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Semua Divisi --</option>
                                    @foreach ($divisis as $divisi)
                                        <option value="{{ $divisi->id_divisi }}"
                                            {{ request('divisi') == $divisi->id_divisi ? 'selected' : '' }}>
                                            {{ $divisi->divisi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Dari Tanggal</label>
                                <input type="date" class="form-control" name="start_date"
                                    value="{{ request('start_date') }}" onchange="this.form.submit()">
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}"
                                    onchange="this.form.submit()">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('neraca-saldo.index') }}" class="btn btn-secondary mt-2">
                                    <i class="ti ti-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>


                    <div id="print-area">
                        <table class="table table-bordered">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Komponen Laporan Arus Kas</th>
                                    <th class="text-end">Jumlah (Rp)</th>
                                    <th class="text-end">Tahun {{ $tahun - 1 }}</th>
                                    <th class="text-end">Tahun {{ $tahun }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- AKTIVITAS OPERASIONAL -->
                                <tr class="table-primary fw-bold">
                                    <td>1</td>
                                    <td colspan="4">Aktivitas Operasional</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>A</td>
                                    <td>Laba bersih menurut laporan Laba/Rugi</td>
                                    <td class="text-end">-</td>

                                    <td class="text-end">Rp
                                        {{ number_format($kenaikan_penghasilan_komprehensif_lalu, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp
                                        {{ number_format($kenaikan_penghasilan_komprehensif, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td></td>
                                    <td colspan="4">DITAMBAH</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Biaya Depresiasi/ penyusutan aset</td>
                                    <td class="text-end">Rp
                                        {{ number_format($total_depresiasi + $total_depresiasi_lalu, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($total_depresiasi_lalu, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($total_depresiasi, 0, ',', '.') }}</td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>Biaya Amortisasi</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Penurunan Piutang</td>
                                    <td class="text-end">
                                        Rp {{ number_format($penurunan_piutang_lalu + $penurunan_piutang, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_piutang_lalu > 0)
                                            Rp {{ number_format($penurunan_piutang_lalu, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_piutang > 0)
                                            Rp {{ number_format($penurunan_piutang, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </td>

                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Penurunan Persediaan</td>
                                    <td class="text-end">
                                        Rp
                                        {{ number_format($penurunan_persediaan_lalu + $penurunan_persediaan, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_persediaan_lalu > 0)
                                            Rp {{ number_format($penurunan_persediaan_lalu, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_persediaan > 0)
                                            Rp {{ number_format($penurunan_persediaan, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </td>

                                </tr>

                                <tr>
                                    <td></td>
                                    <td>Penurunan Biaya Dibayar Dimuka</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Penurunan aktiva lancar lainnya</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Kenaikan hutang</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Kenaikan hutang biaya</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>B</td>
                                    <td>Total Penambahan</td>
                                    <td class="text-end">Rp 0</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td></td>
                                    <td colspan="4">DIKURANGI</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Kenaikan Piutang</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">
                                        @if ($kenaikan_piutang > 0)
                                            Rp {{ number_format($kenaikan_piutang, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Kenaikan Persediaan</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Kenaikan Biaya Dibayar Dimuka</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Kenaikan Aktiva Lancar lainnya</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Penurunan Hutang Dagang</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Penurunan Hutang Biaya</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>C</td>
                                    <td>Total Pengurangan</td>
                                    <td class="text-end">Rp 0</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>D</td>
                                    <td>ARUS KAS BERSIH DARI AKTIVITAS OPERASIONAL (A+B-C)</td>
                                    <td class="text-end">Rp 0</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="table-primary fw-bold">
                                    <td>2</td>
                                    <td colspan="4">Aktivitas Investasi</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Kas masuk dari penjualan peralatan</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>kas keluar untuk pembelian aktiva tetap</td>
                                    <td class="text-end"></td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Kas keluar untuk pembelian furniture/ meubelair/ inventaris</td>
                                    <td class="text-end"></td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>E</td>
                                    <td>ARUS KAS BERSIH DARI AKTIVITAS INVESTASI</td>
                                    <td class="text-end">Rp 0</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="table-primary fw-bold">
                                    <td>3</td>
                                    <td colspan="4">Aktivitas Pendanaan</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Kenaikan Pinjaman Jangka Panjang</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Penurunan pinjaman jangka panjang </td>
                                    <td class="text-end"></td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Pembayaran dividen</td>
                                    <td class="text-end"></td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Penambahan/ penanaman modal</td>
                                    <td class="text-end"></td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>F</td>
                                    <td>ARUS KAS BERSIH DARI AKTIVITAS PENDANAAN</td>
                                    <td class="text-end">Rp 0</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>G</td>
                                    <td>KENAIKAN (PENURUNAN) KAS DAN SETARA KAS (D+E+F)</td>
                                    <td class="text-end">Rp 0</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>H</td>
                                    <td>SALDO KAS AWAL PERIODE (G+H)</td>
                                    <td class="text-end">Rp 0</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>I</td>
                                    <td>SALDO KAS AKHIR PERIODE </td>
                                    <td class="text-end">Rp 0</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
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
