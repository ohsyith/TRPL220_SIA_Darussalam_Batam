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
                        <h5 class="card-title">Laporan Neraca</h5>
                        <div class="action-buttons">

                            <!-- Tombol Export Excel dengan warna hijau tua -->
                            <a href="{{ route('neraca-saldo.index', ['export_excel' => 1, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
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

                        <form method="GET" action="{{ route('neraca-saldo.index') }}">
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <label for="start_date" class="form-label">Tanggal Awal</label>
                                    <input type="date" class="form-control" name="start_date"
                                        value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-5">
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" name="end_date"
                                        value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <a href="{{ route('neraca-saldo.index') }}" class="btn btn-secondary w-100 ms-2">
                                        <i class="ti ti-refresh fs-4"></i>
                                    </a>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100"> <i class="ti ti-search fs-4"></i>
                                    </button>
                                </div>
                            </div>
                        </form>


                        <!-- Tabel Neraca Saldo -->
                        <div id="print-area">

                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Akun</th>
                                        <th class="text-end">Debit (Rp)</th>
                                        <th class="text-end">Kredit (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalDebit = 0;
                                        $totalKredit = 0;
                                    @endphp

                                    @foreach ($semua_akun->groupBy('sub_kategori_akun.kategori_akun.kategori_akun') as $kategori => $sub_kategoris)
                                        <!-- Kategori Akun -->
                                        <tr class="table-primary fw-bold">
                                            <td colspan="3">{{ strtoupper($kategori) }}</td>
                                        </tr>

                                        @foreach ($sub_kategoris->groupBy('sub_kategori_akun.sub_kategori_akun') as $sub_kategori => $akuns)
                                            <!-- Sub Kategori Akun -->
                                            <tr class="table-secondary">
                                                <td colspan="3">&nbsp;&nbsp;&nbsp;{{ $sub_kategori }}
                                                </td>
                                            </tr>

                                            @foreach ($akuns as $akun)
                                                @php
                                                    $debit = $saldo_akun[$akun->id_akun]->total_debit ?? 0;
                                                    $kredit = $saldo_akun[$akun->id_akun]->total_kredit ?? 0;
                                                    $totalDebit += $debit;
                                                    $totalKredit += $kredit;
                                                @endphp
                                                <!-- Akun -->
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $akun->akun }}
                                                    </td>
                                                    <td class="text-end">Rp {{ number_format($debit, 2) }}
                                                    </td>
                                                    <td class="text-end">Rp {{ number_format($kredit, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <!-- Row Total -->
                                <tfoot class="fw-bold bg-light">
                                    <tr>
                                        <td>Total</td>
                                        <td class="text-end">Rp {{ number_format($totalDebit, 2) }}</td>
                                        <td class="text-end">Rp {{ number_format($totalKredit, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
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
@endpush
