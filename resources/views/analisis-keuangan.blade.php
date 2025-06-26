@extends('layouts.layout')

@push('styles')
    <title>SIA Yayasan Darussalam | Akun</title>

    <style>
        html,
        body,
        .page-wrapper,
        .body-wrapper {
            height: 100%;
            min-height: 100vh;
        }

        .body-wrapper {
            display: flex;
            flex-direction: column;
        }

        .container-fluid {
            flex: 1;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-    title">Analisis Keuangan, {{ $user->nama }}</h5>
                    <p class="card-text">Ini adalah halaman dashboard utama Anda.</p>
                </div>

                <div class="container mt-4">


                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Indikator</th>
                                    <th>Nilai</th>
                                    {{-- <th>Satuan</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Return on Investment (ROI)</td>
                                    <td>{{ number_format($roi * 100, 2) }}%</td>
                                    {{-- <td>Persen</td> --}}
                                </tr>
                                <tr>
                                    <td>Return on Assets (ROA)</td>
                                    <td>{{ number_format($roa * 100, 2) }}%</td>
                                    {{-- <td>Persen</td> --}}
                                </tr>
                                <tr>
                                    <td>Rasio Lancar</td>
                                    <td>{{ number_format($rasio_lancar, 2) }}</td>
                                    {{-- <td>Kali</td> --}}
                                </tr>
                                <tr>
                                    <td>Quick Ratio</td>
                                    <td>{{ number_format($quick_ratio, 2) }}</td>
                                    {{-- <td>Kali</td> --}}
                                </tr>
                                <tr>
                                    <td>Debt to Asset Ratio (DAR)</td>
                                    <td>{{ number_format($dar * 100, 2) }}%</td>
                                    {{-- <td>Persen</td> --}}
                                </tr>
                                <tr>
                                    <td>Debt to Equity Ratio (DER)</td>
                                    <td>{{ number_format($der, 2) }}</td>
                                    {{-- <td>Kali</td> --}}
                                </tr>
                                <tr>
                                    <td>Asset Turnover Ratio (ATR)</td>
                                    <td>{{ number_format($atr, 2) }}</td>
                                    {{-- <td>Kali</td> --}}
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <h5 class="mt-4">Data Dasar</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Aset</th>
                            <td>Rp {{ number_format($total_aset, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Aset Lancar</th>
                            <td>Rp {{ number_format($aset_lancar, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Persediaan</th>
                            <td>Rp {{ number_format($persediaan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Kewajiban Lancar</th>
                            <td>Rp {{ number_format($kewajiban_lancar, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Kewajiban Jangka Panjang</th>
                            <td>Rp {{ number_format($kewajiban_panjang, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Total Kewajiban</th>
                            <td>Rp {{ number_format($total_kewajiban, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Modal</th>
                            <td>Rp {{ number_format($modal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Total Pendapatan</th>
                            <td>Rp {{ number_format($pendapatan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Total Beban</th>
                            <td>Rp {{ number_format($beban, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Laba Bersih</th>
                            <td>Rp {{ number_format($laba_bersih, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="py-6 px-6 text-center">
        <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>

    </div>
@endsection



