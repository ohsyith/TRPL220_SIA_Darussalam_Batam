@extends('layouts.layout')
@push('styles')
    <title>SIA Yayasan Darussalam | Laporan Neraca</title>

    <style>
        /* Mengatur warna hijau tua untuk tombol Export Excel */
        .custom-green {
            background-color: #208a20;
            /* Warna hijau tua */
            color: white;
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
        /* Sembunyikan secara default */
        .only-print {
            display: none !important;
        }

        /* Tampilkan hanya saat print */
        @media print {
            .only-print {
                display: flex !important;
                margin-bottom: 20px;
                align-items: center;
            }

            .only-print img {
                height: 80px;
                margin-right: 20px;
            }

            .only-print h4 {
                font-size: 18px;
                margin: 0;
            }

            .only-print small {
                font-size: 12px;
            }
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
                            <a href="{{ route('arus-kas3.index', ['export_excel' => 1]) }}" class="btn custom-green">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </a>

                            <button class="btn custom-grey ms-2" onclick="printLaporan()">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('arus-kas3.index') }}">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                @php
                                    $unit_user = \App\Models\Unit::find($id_unit);
                                @endphp
                                <label for="unit" class="form-label">Unit</label>
                                @if (in_array(Auth::user()->role, ['admin', 'auditor']))
                                    <select name="unit" id="unit" class="form-control"
                                        onchange="this.form.submit()">
                                        <option value="">-- Semua Unit --</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id_unit }}"
                                                {{ request('unit', $id_unit) == $unit->id_unit ? 'selected' : '' }}>
                                                {{ $unit->unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif (Auth::user()->role === 'akuntan_unit')
                                    <select class="form-control" disabled>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id_unit }}"
                                                {{ $unit->id_unit == $id_unit ? 'selected' : '' }}>
                                                {{ $unit->unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="unit" value="{{ $id_unit }}">
                                @endif

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
                                    value="{{ request('start_date', date('Y-01-01')) }}" onchange="this.form.submit()">
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" name="end_date"
                                    value="{{ request('end_date', date('Y-m-d')) }}" onchange="this.form.submit()">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('arus-kas3.index') }}" class="btn btn-secondary mt-2">
                                    <i class="ti ti-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>


                    <div id="print-area">
                        <div class="only-print d-flex align-items-center mb-3">
                            <img src="{{ asset('assets/images/logos/YDB_PNG.png') }}" alt="Logo"
                                style="height: 80px; margin-right: 20px;">
                            <div>
                                <h4 class="mb-0">LAPORAN ARUS KAS<br>YAYASAN DARUSSALAM BATAM</h4>
                                <small>Periode {{ \Carbon\Carbon::parse($start_date)->translatedFormat('d F Y') }} s.d.
                                    {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d F Y') }}</small>
                            </div>
                        </div>


                        <table class="table table-bordered">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Komponen Laporan Arus Kas</th>
                                    <th class="text-end">Total {{ $tahun }}</th>
                                    <th class="text-end">Tahun {{ $tahun - 1 }}</th>
                                    <th class="text-end">Tahun {{ $tahun }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- AKTIVITAS OPERASIONAL -->
                                <tr class="table-primary fw-bold">
                                    <td>A</td>
                                    <td colspan="4">Aktivitas Operasional</td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>Kenaikan/Penurunan Aset Bersih</td>
                                    <td class="text-end">
                                        @if ($laba_bersih_tahun_ini > 0)
                                            Rp
                                            {{ number_format($laba_bersih_tahun_ini, 0, ',', '.') }}
                                        @elseif($laba_bersih_tahun_ini < 0)
                                            (Rp
                                            {{ number_format(abs($laba_bersih_tahun_ini), 0, ',', '.') }})
                                        @elseif($laba_bersih_tahun_ini == 0)
                                            -
                                        @endif
                                    </td>
                                </tr>



                                <tr class="fw-bold">
                                    <td></td>
                                    <td colspan="4">Penurunan (Kenaikan) Aset Lancar :</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Persediaan Perlengkapan Kantor</td>

                                    <td class="text-end">
                                        @php
                                            $selisih_persediaan_perlengkapan_kantor =
                                                $persediaan_perlengkapan_kantor_lalu - $persediaan_perlengkapan_kantor;
                                        @endphp

                                        @if ($selisih_persediaan_perlengkapan_kantor == 0)
                                            -
                                        @elseif ($selisih_persediaan_perlengkapan_kantor > 0)
                                            Rp {{ number_format($selisih_persediaan_perlengkapan_kantor, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($selisih_persediaan_perlengkapan_kantor, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($persediaan_perlengkapan_kantor_lalu == 0)
                                            -
                                        @elseif ($persediaan_perlengkapan_kantor_lalu > 0)
                                            Rp {{ number_format($persediaan_perlengkapan_kantor_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($persediaan_perlengkapan_kantor_lalu, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($persediaan_perlengkapan_kantor == 0)
                                            -
                                        @elseif ($persediaan_perlengkapan_kantor > 0)
                                            Rp {{ number_format($persediaan_perlengkapan_kantor, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($persediaan_perlengkapan_kantor, 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>



                                <tr>
                                    <td></td>
                                    <td>Persediaan Perlengkapan Asrama</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_persediaan_perlengkapan_asrama =
                                                $persediaan_perlengkapan_asrama_lalu - $persediaan_perlengkapan_asrama;
                                        @endphp

                                        @if ($selisih_persediaan_perlengkapan_asrama == 0)
                                            -
                                        @elseif ($selisih_persediaan_perlengkapan_asrama > 0)
                                            Rp {{ number_format($selisih_persediaan_perlengkapan_asrama, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($selisih_persediaan_perlengkapan_asrama, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($persediaan_perlengkapan_asrama_lalu == 0)
                                            -
                                        @elseif ($persediaan_perlengkapan_asrama_lalu > 0)
                                            Rp {{ number_format($persediaan_perlengkapan_asrama_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($persediaan_perlengkapan_asrama_lalu, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($persediaan_perlengkapan_asrama == 0)
                                            -
                                        @elseif ($persediaan_perlengkapan_asrama > 0)
                                            Rp {{ number_format($persediaan_perlengkapan_asrama, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($persediaan_perlengkapan_asrama, 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>




                                <tr>
                                    <td></td>
                                    <td>Persediaan ATK</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_persediaan_atk = $persediaan_atk_lalu - $persediaan_atk;
                                        @endphp

                                        @if ($selisih_persediaan_atk == 0)
                                            -
                                        @elseif ($selisih_persediaan_atk > 0)
                                            Rp {{ number_format($selisih_persediaan_atk, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($selisih_persediaan_atk, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($persediaan_atk_lalu == 0)
                                            -
                                        @elseif ($persediaan_atk_lalu > 0)
                                            Rp {{ number_format($persediaan_atk_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($persediaan_atk_lalu, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($persediaan_atk == 0)
                                            -
                                        @elseif ($persediaan_atk > 0)
                                            Rp {{ number_format($persediaan_atk, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($persediaan_atk, 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>


                                <tr>
                                    <td></td>
                                    <td>Persediaan Lainnya</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_persediaan_lainnya =
                                                $persediaan_lainnya_lalu - $persediaan_lainnya;
                                        @endphp

                                        @if ($selisih_persediaan_lainnya == 0)
                                            -
                                        @elseif ($selisih_persediaan_lainnya > 0)
                                            Rp {{ number_format($selisih_persediaan_lainnya, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($selisih_persediaan_lainnya, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($persediaan_lainnya_lalu == 0)
                                            -
                                        @elseif ($persediaan_lainnya_lalu > 0)
                                            Rp {{ number_format($persediaan_lainnya_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($persediaan_lainnya_lalu, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($persediaan_lainnya == 0)
                                            -
                                        @elseif ($persediaan_lainnya > 0)
                                            Rp {{ number_format($persediaan_lainnya, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($persediaan_lainnya, 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>



                                <tr>
                                    <td></td>
                                    <td>Piutang Rekanan</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_piutang_rekanan = $piutang_rekanan_lalu - $piutang_rekanan;
                                        @endphp

                                        @if ($selisih_piutang_rekanan == 0)
                                            -
                                        @elseif ($selisih_piutang_rekanan > 0)
                                            Rp {{ number_format($selisih_piutang_rekanan, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($selisih_piutang_rekanan, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($piutang_rekanan_lalu == 0)
                                            -
                                        @elseif ($piutang_rekanan_lalu > 0)
                                            Rp {{ number_format($piutang_rekanan_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($piutang_rekanan_lalu, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($piutang_rekanan == 0)
                                            -
                                        @elseif ($piutang_rekanan > 0)
                                            Rp {{ number_format($piutang_rekanan, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($piutang_rekanan, 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>



                                <tr>
                                    <td></td>
                                    <td>Piutang Kegiatan</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_piutang_kegiatan = $piutang_kegiatan_lalu - $piutang_kegiatan;
                                        @endphp

                                        @if ($selisih_piutang_kegiatan == 0)
                                            -
                                        @elseif ($selisih_piutang_kegiatan > 0)
                                            Rp {{ number_format($selisih_piutang_kegiatan, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($selisih_piutang_kegiatan), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($piutang_kegiatan_lalu == 0)
                                            -
                                        @elseif ($piutang_kegiatan_lalu > 0)
                                            Rp {{ number_format($piutang_kegiatan_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($piutang_kegiatan_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($piutang_kegiatan == 0)
                                            -
                                        @elseif ($piutang_kegiatan > 0)
                                            Rp {{ number_format($piutang_kegiatan, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($piutang_kegiatan), 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>



                                <tr>
                                    <td></td>
                                    <td>Piutang Karyawan</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_piutang_karyawan = $piutang_karyawan_lalu - $piutang_karyawan;
                                        @endphp

                                        @if ($selisih_piutang_karyawan == 0)
                                            -
                                        @elseif ($selisih_piutang_karyawan > 0)
                                            Rp {{ number_format($selisih_piutang_karyawan, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($selisih_piutang_karyawan), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($piutang_karyawan_lalu == 0)
                                            -
                                        @elseif ($piutang_karyawan_lalu > 0)
                                            Rp {{ number_format($piutang_karyawan_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($piutang_karyawan_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($piutang_karyawan == 0)
                                            -
                                        @elseif ($piutang_karyawan > 0)
                                            Rp {{ number_format($piutang_karyawan, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($piutang_karyawan), 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>



                                <tr>
                                    <td></td>
                                    <td>Piutang Sumbangan</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_piutang_sumbangan = $piutang_sumbangan_lalu - $piutang_sumbangan;
                                        @endphp

                                        @if ($selisih_piutang_sumbangan == 0)
                                            -
                                        @elseif ($selisih_piutang_sumbangan > 0)
                                            Rp {{ number_format($selisih_piutang_sumbangan, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($selisih_piutang_sumbangan), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($piutang_sumbangan_lalu == 0)
                                            -
                                        @elseif ($piutang_sumbangan_lalu > 0)
                                            Rp {{ number_format($piutang_sumbangan_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($piutang_sumbangan_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($piutang_sumbangan == 0)
                                            -
                                        @elseif ($piutang_sumbangan > 0)
                                            Rp {{ number_format($piutang_sumbangan, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($piutang_sumbangan), 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>


                                <tr>
                                    <td></td>
                                    <td>Piutang Lainnya</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_piutang_lainnya = $piutang_lainnya_lalu - $piutang_lainnya;
                                        @endphp

                                        @if ($selisih_piutang_lainnya == 0)
                                            -
                                        @elseif ($selisih_piutang_lainnya > 0)
                                            Rp {{ number_format($selisih_piutang_lainnya, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($selisih_piutang_lainnya), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($piutang_lainnya_lalu == 0)
                                            -
                                        @elseif ($piutang_lainnya_lalu > 0)
                                            Rp {{ number_format($piutang_lainnya_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($piutang_lainnya_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($piutang_lainnya == 0)
                                            -
                                        @elseif ($piutang_lainnya > 0)
                                            Rp {{ number_format($piutang_lainnya, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($piutang_lainnya), 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>



                                <tr>
                                    <td></td>
                                    <td>Cadangan Kerugian Piutang tak tertagih</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>



                                <tr>
                                    <td></td>
                                    <td>Sewa Dibayar Dimuka</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_sewa_dibayar_dimuka =
                                                $sewa_dibayar_dimuka_lalu - $sewa_dibayar_dimuka;
                                        @endphp

                                        @if ($selisih_sewa_dibayar_dimuka == 0)
                                            -
                                        @elseif ($selisih_sewa_dibayar_dimuka > 0)
                                            Rp {{ number_format($selisih_sewa_dibayar_dimuka, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($selisih_sewa_dibayar_dimuka), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($sewa_dibayar_dimuka_lalu == 0)
                                            -
                                        @elseif ($sewa_dibayar_dimuka_lalu > 0)
                                            Rp {{ number_format($sewa_dibayar_dimuka_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($sewa_dibayar_dimuka_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($sewa_dibayar_dimuka == 0)
                                            -
                                        @elseif ($sewa_dibayar_dimuka > 0)
                                            Rp {{ number_format($sewa_dibayar_dimuka, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($sewa_dibayar_dimuka), 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>




                                <tr>
                                    <td></td>
                                    <td>Tabungan Pensiun Karyawan</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_tabungan_pensiun_karyawan =
                                                $tabungan_pensiun_karyawan_lalu - $tabungan_pensiun_karyawan;
                                        @endphp

                                        @if ($selisih_tabungan_pensiun_karyawan == 0)
                                            -
                                        @elseif ($selisih_tabungan_pensiun_karyawan > 0)
                                            Rp {{ number_format($selisih_tabungan_pensiun_karyawan, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($selisih_tabungan_pensiun_karyawan), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($tabungan_pensiun_karyawan_lalu == 0)
                                            -
                                        @elseif ($tabungan_pensiun_karyawan_lalu > 0)
                                            Rp {{ number_format($tabungan_pensiun_karyawan_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($tabungan_pensiun_karyawan_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($tabungan_pensiun_karyawan == 0)
                                            -
                                        @elseif ($tabungan_pensiun_karyawan > 0)
                                            Rp {{ number_format($tabungan_pensiun_karyawan, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($tabungan_pensiun_karyawan), 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>




                                <tr>
                                    <td></td>
                                    <td>Pajak Dibayar Dimuka</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_pajak_dibayar_dimuka =
                                                $pajak_dibayar_dimuka_lalu - $pajak_dibayar_dimuka;
                                        @endphp

                                        @if ($selisih_pajak_dibayar_dimuka == 0)
                                            -
                                        @elseif ($selisih_pajak_dibayar_dimuka > 0)
                                            Rp {{ number_format($selisih_pajak_dibayar_dimuka, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($selisih_pajak_dibayar_dimuka), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($pajak_dibayar_dimuka_lalu == 0)
                                            -
                                        @elseif ($pajak_dibayar_dimuka_lalu > 0)
                                            Rp {{ number_format($pajak_dibayar_dimuka_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($pajak_dibayar_dimuka_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($pajak_dibayar_dimuka == 0)
                                            -
                                        @elseif ($pajak_dibayar_dimuka > 0)
                                            Rp {{ number_format($pajak_dibayar_dimuka, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($pajak_dibayar_dimuka), 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>



                                <tr class="fw-bold">
                                    <td></td>
                                    <td colspan="4">Kenaikan (Penurunan) Kewajiban Jangka Pendek :</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Hutang Jangka Pendek</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_hutang_jangka_pendek =
                                                $hutang_jangka_pendek_lalu - $hutang_jangka_pendek;
                                        @endphp

                                        @if ($selisih_hutang_jangka_pendek == 0)
                                            -
                                        @elseif ($selisih_hutang_jangka_pendek > 0)
                                            Rp {{ number_format($selisih_hutang_jangka_pendek, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($selisih_hutang_jangka_pendek), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($hutang_jangka_pendek_lalu == 0)
                                            -
                                        @elseif ($hutang_jangka_pendek_lalu > 0)
                                            Rp {{ number_format($hutang_jangka_pendek_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($hutang_jangka_pendek_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($hutang_jangka_pendek == 0)
                                            -
                                        @elseif ($hutang_jangka_pendek > 0)
                                            Rp {{ number_format($hutang_jangka_pendek, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($hutang_jangka_pendek), 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>



                                @php
                                    $aktivitas_operasional =
                                        $laba_bersih_tahun_ini +
                                        $selisih_persediaan_perlengkapan_kantor +
                                        $selisih_persediaan_perlengkapan_asrama +
                                        $selisih_persediaan_atk +
                                        $selisih_persediaan_lainnya +
                                        $selisih_piutang_rekanan +
                                        $selisih_piutang_kegiatan +
                                        $selisih_piutang_karyawan +
                                        $selisih_piutang_sumbangan +
                                        $selisih_piutang_lainnya +
                                        $selisih_sewa_dibayar_dimuka +
                                        $selisih_tabungan_pensiun_karyawan +
                                        $selisih_pajak_dibayar_dimuka +
                                        $selisih_hutang_jangka_pendek;
                                @endphp
                                <tr class="table-secondary fw-medium">
                                    <td></td>
                                    <td>Kas Bersih yang diperoleh dari Aktivitas Operasional</td>
                                    <td class="text-end">
                                        @if ($aktivitas_operasional == 0)
                                            -
                                        @elseif ($aktivitas_operasional > 0)
                                            Rp {{ number_format($aktivitas_operasional, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($aktivitas_operasional), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>



                                {{-- Aktivitas Investasi --}}
                                <tr class="table-primary fw-bold">
                                    <td>B</td>
                                    <td colspan="4">Aktivitas Investasi</td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td> (Penambahan) Pengurangan Investasi</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>



                                <tr>
                                    <td></td>
                                    <td>(Penambahan) Pengurangan Aset Tetap</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_aset_tetap = $aset_tetap_lalu - $aset_tetap;
                                        @endphp

                                        @if ($selisih_aset_tetap == 0)
                                            -
                                        @elseif ($selisih_aset_tetap > 0)
                                            Rp {{ number_format($selisih_aset_tetap, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($selisih_aset_tetap, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($aset_tetap_lalu == 0)
                                            -
                                        @elseif ($aset_tetap_lalu > 0)
                                            Rp {{ number_format($aset_tetap_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($aset_tetap_lalu, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($aset_tetap == 0)
                                            -
                                        @elseif ($aset_tetap > 0)
                                            Rp {{ number_format($aset_tetap, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($aset_tetap, 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>



                                @php
                                    $aktivitas_investasi = $selisih_aset_tetap;
                                @endphp
                                <tr class="table-secondary fw-medium">
                                    <td></td>
                                    <td>Kas Bersih yang diperoleh dari Aktivitas Investasi</td>
                                    <td class="text-end">
                                        @if ($aktivitas_investasi == 0)
                                            -
                                        @elseif ($aktivitas_investasi > 0)
                                            Rp {{ number_format($aktivitas_investasi, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($aktivitas_investasi), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>






                                {{-- Aktivitas pendanaan --}}
                                <tr class="table-primary fw-bold">
                                    <td>C</td>
                                    <td colspan="4">Aktivitas Pendanaan</td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>Penambahan (Penurunan) Kewajiban Jangka Panjang</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_kewajiban_jangka_panjang =
                                                $kewajiban_jangka_panjang_lalu - $kewajiban_jangka_panjang;
                                        @endphp

                                        @if ($selisih_kewajiban_jangka_panjang == 0)
                                            -
                                        @elseif ($selisih_kewajiban_jangka_panjang > 0)
                                            Rp {{ number_format($selisih_kewajiban_jangka_panjang, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($selisih_kewajiban_jangka_panjang), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kewajiban_jangka_panjang_lalu == 0)
                                            -
                                        @elseif ($kewajiban_jangka_panjang_lalu > 0)
                                            Rp {{ number_format($kewajiban_jangka_panjang_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($kewajiban_jangka_panjang_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kewajiban_jangka_panjang == 0)
                                            -
                                        @elseif ($kewajiban_jangka_panjang > 0)
                                            Rp {{ number_format($kewajiban_jangka_panjang, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($kewajiban_jangka_panjang), 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>



                                <tr>
                                    <td></td>
                                    <td>Aset Neto</td>
                                    <td class="text-end">
                                        @php
                                            $selisih_aset_neto = $aset_neto_lalu - $aset_neto;
                                        @endphp

                                        @if ($selisih_aset_neto == 0)
                                            -
                                        @elseif ($selisih_aset_neto > 0)
                                            Rp {{ number_format($selisih_aset_neto, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format($selisih_aset_neto, 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($aset_neto_lalu == 0)
                                            -
                                        @elseif ($aset_neto_lalu > 0)
                                            Rp {{ number_format($aset_neto_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($aset_neto_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($aset_neto == 0)
                                            -
                                        @elseif ($aset_neto > 0)
                                            Rp {{ number_format($aset_neto, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($aset_neto), 0, ',', '.') }})
                                        @endif
                                    </td>
                                </tr>




                                @php
                                    $aktivitas_pendanaan = $selisih_kewajiban_jangka_panjang + $selisih_aset_neto;
                                @endphp
                                <tr class="table-secondary fw-medium">
                                    <td></td>
                                    <td>Kas Bersih yang diperoleh dari Aktivitas Pendanaan</td>
                                    <td class="text-end">
                                        @if ($aktivitas_pendanaan == 0)
                                            -
                                        @elseif ($aktivitas_pendanaan > 0)
                                            Rp {{ number_format($aktivitas_pendanaan, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($aktivitas_pendanaan), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>




                                @php
                                    $kenaikan_penurunan_kas =
                                        $aktivitas_operasional + $aktivitas_investasi + $aktivitas_pendanaan;
                                @endphp
                                <tr class="table-warning fw-medium">
                                    <td></td>
                                    <td>Kenaikan (Penurunan) Kas ( A + B + C )</td>
                                    <td class="text-end ">
                                        @if ($kenaikan_penurunan_kas == 0)
                                            -
                                        @elseif ($kenaikan_penurunan_kas > 0)
                                            Rp {{ number_format($kenaikan_penurunan_kas, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($kenaikan_penurunan_kas), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end ">-</td>
                                    <td class="text-end ">-</td>
                                </tr>

                                <tr class="table-warning fw-medium">
                                    <td></td>
                                    <td>Saldo Kas Awal</td>
                                    <td class="text-end">
                                        @if ($saldo_kas_lalu == 0)
                                            -
                                        @elseif ($saldo_kas_lalu > 0)
                                            Rp {{ number_format($saldo_kas_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($saldo_kas_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end ">-</td>
                                    <td class="text-end ">-</td>
                                </tr>

                                <tr class="table-warning fw-medium">
                                    <td></td>
                                    <td>Saldo Kas Akhir</td>
                                    <td class="text-end">
                                        @if ($saldo_kas == 0)
                                            -
                                        @elseif ($saldo_kas > 0)
                                            Rp {{ number_format($saldo_kas, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($saldo_kas), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end ">-</td>
                                    <td class="text-end ">-</td>
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
@endpush
