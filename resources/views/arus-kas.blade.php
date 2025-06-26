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
                                    value="{{ request('start_date') }}" onchange="this.form.submit()">
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" name="end_date"
                                    value="{{ request('end_date') }}" onchange="this.form.submit()">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('arus-kas.index') }}" class="btn btn-secondary mt-2">
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

                                @php
                                    $a = $laba_bersih_tahun_ini;
                                @endphp
                                <tr class="fw-bold">
                                    <td>A</td>
                                    <td>Laba bersih menurut laporan Laba/Rugi</td>
                                    <td class="text-end">
                                        @if ($a > 0)
                                            Rp
                                            {{ number_format($a, 0, ',', '.') }}
                                        @elseif($a < 0)
                                            (Rp
                                            {{ number_format(abs($a), 0, ',', '.') }})
                                        @elseif($a == 0)
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        -
                                    </td>
                                    <td class="text-end">
                                        -
                                    </td>
                                </tr>

                                <tr class="fw-bold">
                                    <td></td>
                                    <td colspan="4">DITAMBAH</td>
                                </tr>

                                {{-- //depresiais --}}
                                {{-- <tr>
                                    <td></td>
                                    <td>Biaya Depresiasi / Penyusutan Aset</td>

                                    <td class="text-end">
                                        @php
                                            $selisihDepresiasi = $depresiasi_tahun_lalu - $depresiasi_tahun_ini;
                                        @endphp

                                        @if ($depresiasi_tahun_lalu == 0 && $depresiasi_tahun_ini == 0)
                                            -
                                        @elseif ($selisihDepresiasi > 0)
                                            Rp {{ number_format($selisihDepresiasi, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        @if ($depresiasi_tahun_lalu == 0)
                                            -
                                        @else
                                            Rp {{ number_format($depresiasi_tahun_lalu, 0, ',', '.') }}
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        @if ($depresiasi_tahun_ini == 0)
                                            -
                                        @else
                                            Rp {{ number_format($depresiasi_tahun_ini, 0, ',', '.') }}
                                        @endif
                                    </td>
                                </tr> --}}




                                {{-- <tr>
                                    <td></td>
                                    <td>Biaya Amortisasi</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr> --}}

                                <tr>
                                    <td></td>
                                    <td>Penurunan Piutang</td>
                                    <td class="text-end">
                                        @if ($penurunan_piutang_tahun_lalu - $penurunan_piutang_tahun_ini > 0)
                                            Rp
                                            {{ number_format($penurunan_piutang_tahun_lalu - $penurunan_piutang_tahun_ini, 0, ',', '.') }}
                                        @elseif($penurunan_piutang_tahun_lalu - $penurunan_piutang_tahun_ini < 0)
                                            (Rp
                                            {{ number_format(abs($penurunan_piutang_tahun_lalu - $penurunan_piutang_tahun_ini), 0, ',', '.') }})
                                        @elseif($penurunan_piutang_tahun_lalu - $penurunan_piutang_tahun_ini == 0)
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_piutang_tahun_lalu > 0)
                                            Rp {{ number_format($penurunan_piutang_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_piutang_tahun_ini > 0)
                                            Rp {{ number_format($penurunan_piutang_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>


                                <tr>
                                    <td></td>
                                    <td>Penurunan Persediaan</td>
                                    <td class="text-end">
                                        @if ($penurunan_persediaan_tahun_lalu - $penurunan_persediaan_tahun_ini > 0)
                                            Rp
                                            {{ number_format($penurunan_persediaan_tahun_lalu - $penurunan_persediaan_tahun_ini, 0, ',', '.') }}
                                        @elseif($penurunan_persediaan_tahun_lalu - $penurunan_persediaan_tahun_ini < 0)
                                            (Rp
                                            {{ number_format(abs($penurunan_persediaan_tahun_lalu - $penurunan_persediaan_tahun_ini), 0, ',', '.') }})
                                        @elseif($penurunan_persediaan_tahun_lalu - $penurunan_persediaan_tahun_ini == 0)
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_persediaan_tahun_lalu > 0)
                                            Rp {{ number_format($penurunan_persediaan_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_persediaan_tahun_ini > 0)
                                            Rp {{ number_format($penurunan_persediaan_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>


                                <tr>
                                    <td></td>
                                    <td>Penurunan Biaya Dibayar Dimuka</td>
                                    <td class="text-end">
                                        @if ($penurunan_bdd_tahun_lalu - $penurunan_bdd_tahun_ini > 0)
                                            Rp
                                            {{ number_format($penurunan_bdd_tahun_lalu - $penurunan_bdd_tahun_ini, 0, ',', '.') }}
                                        @elseif($penurunan_bdd_tahun_lalu - $penurunan_bdd_tahun_ini < 0)
                                            (Rp
                                            {{ number_format(abs($penurunan_bdd_tahun_lalu - $penurunan_bdd_tahun_ini), 0, ',', '.') }})
                                        @elseif($penurunan_bdd_tahun_lalu - $penurunan_bdd_tahun_ini == 0)
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_bdd_tahun_lalu > 0)
                                            Rp {{ number_format($penurunan_bdd_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_bdd_tahun_ini > 0)
                                            Rp {{ number_format($penurunan_bdd_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>Penurunan Aktiva Lancar Lainnya</td>
                                    <td class="text-end">
                                        @if ($penurunan_aktiva_lancar_tahun_lalu - $penurunan_aktiva_lancar_tahun_ini > 0)
                                            Rp
                                            {{ number_format($penurunan_aktiva_lancar_tahun_lalu - $penurunan_aktiva_lancar_tahun_ini, 0, ',', '.') }}
                                        @else
                                            (Rp
                                            {{ number_format(abs($penurunan_aktiva_lancar_tahun_lalu - $penurunan_aktiva_lancar_tahun_ini), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_aktiva_lancar_tahun_lalu > 0)
                                            Rp {{ number_format($penurunan_aktiva_lancar_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($penurunan_aktiva_lancar_tahun_lalu), 0, ',', '.') }})
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_aktiva_lancar_tahun_ini > 0)
                                            Rp {{ number_format($penurunan_aktiva_lancar_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>


                                <tr>
                                    <td></td>
                                    <td>Kenaikan Hutang</td>

                                    <td class="text-end">
                                        @php
                                            $selisih = $kenaikan_hutang_tahun_lalu - $kenaikan_hutang_tahun_ini;
                                        @endphp

                                        @if ($selisih == 0 || $selisih === null)
                                            -
                                        @elseif ($selisih > 0)
                                            Rp {{ number_format($selisih, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($selisih), 0, ',', '.') }})
                                        @endif
                                    </td>

                                    {{-- Tahun Lalu --}}
                                    <td class="text-end">
                                        @if ($kenaikan_hutang_tahun_lalu == 0 || $kenaikan_hutang_tahun_lalu === null)
                                            -
                                        @else
                                            Rp {{ number_format($kenaikan_hutang_tahun_lalu, 0, ',', '.') }}
                                        @endif
                                    </td>

                                    {{-- Tahun Ini --}}
                                    <td class="text-end">
                                        @if ($kenaikan_hutang_tahun_ini == 0 || $kenaikan_hutang_tahun_ini === null)
                                            -
                                        @else
                                            Rp {{ number_format($kenaikan_hutang_tahun_ini, 0, ',', '.') }}
                                        @endif
                                    </td>
                                </tr>


                                <tr>
                                    <td></td>
                                    <td>Kenaikan Hutang Biaya</td>

                                    <td class="text-end">
                                        @php
                                            $selisih =
                                                $kenaikan_hutang_biaya_tahun_lalu - $kenaikan_hutang_biaya_tahun_ini;
                                        @endphp

                                        @if ($selisih == 0 || $selisih === null)
                                            -
                                        @elseif ($selisih > 0)
                                            Rp {{ number_format($selisih, 0, ',', '.') }}
                                        @else
                                            (Rp {{ number_format(abs($selisih), 0, ',', '.') }})
                                        @endif
                                    </td>

                                    {{-- Tahun Lalu --}}
                                    <td class="text-end">
                                        @if ($kenaikan_hutang_biaya_tahun_lalu == 0 || $kenaikan_hutang_biaya_tahun_lalu === null)
                                            -
                                        @else
                                            Rp {{ number_format($kenaikan_hutang_biaya_tahun_lalu, 0, ',', '.') }}
                                        @endif
                                    </td>

                                    {{-- Tahun Ini --}}
                                    <td class="text-end">
                                        @if ($kenaikan_hutang_biaya_tahun_ini == 0 || $kenaikan_hutang_biaya_tahun_ini === null)
                                            -
                                        @else
                                            Rp {{ number_format($kenaikan_hutang_biaya_tahun_ini, 0, ',', '.') }}
                                        @endif
                                    </td>
                                </tr>



                                @php
                                    $b =
                                        $depresiasi_tahun_lalu -
                                        $depresiasi_tahun_ini +
                                        ($penurunan_piutang_tahun_lalu - $penurunan_piutang_tahun_ini) +
                                        ($penurunan_persediaan_tahun_lalu - $penurunan_persediaan_tahun_ini) +
                                        ($penurunan_bdd_tahun_lalu - $penurunan_bdd_tahun_ini) +
                                        ($penurunan_aktiva_lancar_tahun_lalu - $penurunan_aktiva_lancar_tahun_ini) +
                                        ($kenaikan_hutang_tahun_lalu - $kenaikan_hutang_tahun_ini) +
                                        ($kenaikan_hutang_biaya_tahun_lalu - $kenaikan_hutang_biaya_tahun_ini);
                                @endphp
                                <tr class="fw-bold">
                                    <td>B</td>
                                    <td>Total Penambahan</td>
                                    <td class="text-end">
                                        @if ($b > 0)
                                            Rp
                                            {{ number_format($b, 0, ',', '.') }}
                                        @elseif($b < 0)
                                            (Rp
                                            {{ number_format(abs($b), 0, ',', '.') }})
                                        @elseif($b == 0)
                                            -
                                        @endif
                                    </td>
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
                                    <td class="text-end">
                                        @if ($kenaikan_piutang_tahun_lalu - $kenaikan_piutang_tahun_ini != 0)
                                            Rp
                                            {{ $kenaikan_piutang_tahun_lalu - $kenaikan_piutang_tahun_ini < 0 ? '(' . number_format(abs($kenaikan_piutang_tahun_lalu - $kenaikan_piutang_tahun_ini), 0, ',', '.') . ')' : number_format($kenaikan_piutang_tahun_lalu - $kenaikan_piutang_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        @if ($kenaikan_piutang_tahun_lalu != 0)
                                            Rp
                                            {{ $kenaikan_piutang_tahun_lalu < 0 ? '(' . number_format(abs($kenaikan_piutang_tahun_lalu), 0, ',', '.') . ')' : number_format($kenaikan_piutang_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kenaikan_piutang_tahun_ini != 0)
                                            Rp
                                            {{ $kenaikan_piutang_tahun_ini < 0 ? '(' . number_format(abs($kenaikan_piutang_tahun_ini), 0, ',', '.') . ')' : number_format($kenaikan_piutang_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>Kenaikan Persediaan</td>
                                    <td class="text-end">-</td>

                                    <td class="text-end">
                                        @if ($kenaikan_persediaan_tahun_lalu !== null && $kenaikan_persediaan_tahun_lalu != 0)
                                            @if ($kenaikan_persediaan_tahun_lalu < 0)
                                                (Rp {{ number_format(abs($kenaikan_persediaan_tahun_lalu), 0, ',', '.') }})
                                            @else
                                                Rp {{ number_format($kenaikan_persediaan_tahun_lalu, 0, ',', '.') }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        @if ($kenaikan_persediaan_tahun_ini !== null && $kenaikan_persediaan_tahun_ini != 0)
                                            @if ($kenaikan_persediaan_tahun_ini < 0)
                                                (Rp {{ number_format(abs($kenaikan_persediaan_tahun_ini), 0, ',', '.') }})
                                            @else
                                                Rp {{ number_format($kenaikan_persediaan_tahun_ini, 0, ',', '.') }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>


                                <tr>
                                    <td></td>
                                    <td>Kenaikan Biaya Dibayar Dimuka</td>
                                    <td class="text-end">
                                        @if ($kenaikan_biaya_dibayar_dimuka_tahun_lalu - $kenaikan_biaya_dibayar_dimuka_tahun_ini != 0)
                                            {{ $kenaikan_biaya_dibayar_dimuka_tahun_lalu - $kenaikan_biaya_dibayar_dimuka_tahun_ini < 0
                                                ? '(Rp ' .
                                                    number_format(
                                                        abs($kenaikan_biaya_dibayar_dimuka_tahun_lalu - $kenaikan_biaya_dibayar_dimuka_tahun_ini),
                                                        0,
                                                        ',',
                                                        '.',
                                                    ) .
                                                    ')'
                                                : 'Rp ' .
                                                    number_format(
                                                        $kenaikan_biaya_dibayar_dimuka_tahun_lalu - $kenaikan_biaya_dibayar_dimuka_tahun_ini,
                                                        0,
                                                        ',',
                                                        '.',
                                                    ) }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        @if ($kenaikan_biaya_dibayar_dimuka_tahun_lalu != 0)
                                            {{ $kenaikan_biaya_dibayar_dimuka_tahun_lalu < 0
                                                ? '(Rp ' . number_format(abs($kenaikan_biaya_dibayar_dimuka_tahun_lalu), 0, ',', '.') . ')'
                                                : 'Rp ' . number_format($kenaikan_biaya_dibayar_dimuka_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        @if ($kenaikan_biaya_dibayar_dimuka_tahun_ini != 0)
                                            {{ $kenaikan_biaya_dibayar_dimuka_tahun_ini < 0
                                                ? '(Rp ' . number_format(abs($kenaikan_biaya_dibayar_dimuka_tahun_ini), 0, ',', '.') . ')'
                                                : 'Rp ' . number_format($kenaikan_biaya_dibayar_dimuka_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>


                                <tr>
                                    <td></td>
                                    <td>Kenaikan Aktiva Lancar lainnya</td>
                                    <td class="text-end">
                                        @if ($kenaikan_aktiva_lancar_lainnya_tahun_lalu - $kenaikan_aktiva_lancar_lainnya_tahun_ini != 0)
                                            (Rp
                                            {{ $kenaikan_aktiva_lancar_lainnya_tahun_lalu - $kenaikan_aktiva_lancar_lainnya_tahun_ini < 0 ? number_format(abs($kenaikan_aktiva_lancar_lainnya_tahun_lalu - $kenaikan_aktiva_lancar_lainnya_tahun_ini), 0, ',', '.') : number_format($kenaikan_aktiva_lancar_lainnya_tahun_lalu - $kenaikan_aktiva_lancar_lainnya_tahun_ini, 0, ',', '.') }})
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kenaikan_aktiva_lancar_lainnya_tahun_lalu != 0)
                                            Rp
                                            {{ $kenaikan_aktiva_lancar_lainnya_tahun_lalu < 0 ? '(' . number_format(abs($kenaikan_aktiva_lancar_lainnya_tahun_lalu), 0, ',', '.') . ')' : number_format($kenaikan_aktiva_lancar_lainnya_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kenaikan_aktiva_lancar_lainnya_tahun_ini != 0)
                                            Rp
                                            {{ $kenaikan_aktiva_lancar_lainnya_tahun_ini < 0 ? '(' . number_format(abs($kenaikan_aktiva_lancar_lainnya_tahun_ini), 0, ',', '.') . ')' : number_format($kenaikan_aktiva_lancar_lainnya_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
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
                                    <td class="text-end">
                                        @if ($penurunan_hutang_biaya_tahun_lalu - $penurunan_hutang_biaya_tahun_ini != 0)
                                            Rp
                                            {{ $penurunan_hutang_biaya_tahun_lalu - $penurunan_hutang_biaya_tahun_ini < 0
                                                ? '(' .
                                                    number_format(abs($penurunan_hutang_biaya_tahun_lalu - $penurunan_hutang_biaya_tahun_ini), 0, ',', '.') .
                                                    ')'
                                                : number_format($penurunan_hutang_biaya_tahun_lalu - $penurunan_hutang_biaya_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        @if ($penurunan_hutang_biaya_tahun_lalu != 0)
                                            Rp
                                            {{ $penurunan_hutang_biaya_tahun_lalu < 0
                                                ? '(' . number_format(abs($penurunan_hutang_biaya_tahun_lalu), 0, ',', '.') . ')'
                                                : number_format($penurunan_hutang_biaya_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        @if ($penurunan_hutang_biaya_tahun_ini != 0)
                                            Rp
                                            {{ $penurunan_hutang_biaya_tahun_ini < 0
                                                ? '(' . number_format(abs($penurunan_hutang_biaya_tahun_ini), 0, ',', '.') . ')'
                                                : number_format($penurunan_hutang_biaya_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>


                                @php
                                    $c =
                                        $kenaikan_piutang_tahun_lalu -
                                        $kenaikan_piutang_tahun_ini +
                                        ($kenaikan_persediaan_tahun_lalu - $kenaikan_persediaan_tahun_ini) +
                                        ($kenaikan_biaya_dibayar_dimuka_tahun_lalu -
                                            $kenaikan_biaya_dibayar_dimuka_tahun_ini) +
                                        ($kenaikan_aktiva_lancar_lainnya_tahun_lalu -
                                            $kenaikan_aktiva_lancar_lainnya_tahun_ini) +
                                        ($penurunan_hutang_biaya_tahun_lalu - $penurunan_hutang_biaya_tahun_ini);
                                @endphp

                                <tr class="fw-bold">
                                    <td>C</td>
                                    <td>Total Pengurangan</td>
                                    <td class="text-end">
                                        @if ($c > 0)
                                            Rp
                                            {{ number_format($c, 0, ',', '.') }}
                                        @elseif($c < 0)
                                            (Rp
                                            {{ number_format(abs($c), 0, ',', '.') }})
                                        @elseif($c == 0)
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>

                                @php
                                    $d = $a + $b - $c;
                                @endphp
                                <tr class="fw-bold">
                                    <td>D</td>
                                    <td>ARUS KAS BERSIH DARI AKTIVITAS OPERASIONAL (A+B-C)</td>
                                    <td class="text-end">
                                        @if ($d > 0)
                                            Rp
                                            {{ number_format($d, 0, ',', '.') }}
                                        @elseif($d < 0)
                                            (Rp
                                            {{ number_format(abs($d), 0, ',', '.') }})
                                        @elseif($d == 0)
                                            -
                                        @endif
                                    </td>
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
                                    <td class="text-end">
                                        @if ($kas_masuk_penjualan_aset_tetap_tahun_lalu - $kas_masuk_penjualan_aset_tetap_tahun_ini != 0)
                                            Rp
                                            {{ $kas_masuk_penjualan_aset_tetap_tahun_lalu - $kas_masuk_penjualan_aset_tetap_tahun_ini < 0
                                                ? '(' .
                                                    number_format(
                                                        abs($kas_masuk_penjualan_aset_tetap_tahun_lalu - $kas_masuk_penjualan_aset_tetap_tahun_ini),
                                                        0,
                                                        ',',
                                                        '.',
                                                    ) .
                                                    ')'
                                                : number_format(
                                                    $kas_masuk_penjualan_aset_tetap_tahun_lalu - $kas_masuk_penjualan_aset_tetap_tahun_ini,
                                                    0,
                                                    ',',
                                                    '.',
                                                ) }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        @if ($kas_masuk_penjualan_aset_tetap_tahun_lalu > 0)
                                            Rp {{ number_format($kas_masuk_penjualan_aset_tetap_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kas_masuk_penjualan_aset_tetap_tahun_ini > 0)
                                            Rp {{ number_format($kas_masuk_penjualan_aset_tetap_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>Kas keluar untuk pembelian aktiva tetap</td>
                                    <td class="text-end">
                                        @if ($kas_keluar_pembelian_aset_tetap_tahun_lalu - $kas_keluar_pembelian_aset_tetap_tahun_ini != 0)
                                            Rp
                                            {{ $kas_keluar_pembelian_aset_tetap_tahun_lalu - $kas_keluar_pembelian_aset_tetap_tahun_ini < 0
                                                ? '(' .
                                                    number_format(
                                                        abs($kas_keluar_pembelian_aset_tetap_tahun_lalu - $kas_keluar_pembelian_aset_tetap_tahun_ini),
                                                        0,
                                                        ',',
                                                        '.',
                                                    ) .
                                                    ')'
                                                : number_format(
                                                    $kas_keluar_pembelian_aset_tetap_tahun_lalu - $kas_keluar_pembelian_aset_tetap_tahun_ini,
                                                    0,
                                                    ',',
                                                    '.',
                                                ) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kas_keluar_pembelian_aset_tetap_tahun_lalu > 0)
                                            Rp
                                            {{ number_format($kas_keluar_pembelian_aset_tetap_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kas_keluar_pembelian_aset_tetap_tahun_ini > 0)
                                            Rp {{ number_format($kas_keluar_pembelian_aset_tetap_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>Kas keluar untuk pembelian furniture/ meubelair/ inventaris</td>
                                    <td class="text-end">
                                        @if (
                                            $kas_keluar_inventaris_tahun_lalu - $kas_keluar_inventaris_tahun_ini !== null &&
                                                $kas_keluar_inventaris_tahun_lalu - $kas_keluar_inventaris_tahun_ini != 0)
                                            @if ($kas_keluar_inventaris_tahun_lalu - $kas_keluar_inventaris_tahun_ini < 0)
                                                (Rp
                                                {{ number_format(abs($kas_keluar_inventaris_tahun_lalu - $kas_keluar_inventaris_tahun_ini), 0, ',', '.') }})
                                            @else
                                                Rp
                                                {{ number_format($kas_keluar_inventaris_tahun_lalu - $kas_keluar_inventaris_tahun_ini, 0, ',', '.') }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kas_keluar_inventaris_tahun_lalu > 0)
                                            Rp {{ number_format($kas_keluar_inventaris_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kas_keluar_inventaris_tahun_ini > 0)
                                            Rp {{ number_format($kas_keluar_inventaris_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>



                                @php
                                    $e =
                                        $kas_masuk_penjualan_aset_tetap_tahun_lalu -
                                        $kas_masuk_penjualan_aset_tetap_tahun_ini +
                                        ($kas_keluar_pembelian_aset_tetap_tahun_lalu -
                                            $kas_keluar_pembelian_aset_tetap_tahun_ini) +
                                        ($kas_keluar_inventaris_tahun_lalu - $kas_keluar_inventaris_tahun_ini);
                                @endphp

                                <tr class="fw-bold">
                                    <td>E</td>
                                    <td>ARUS KAS BERSIH DARI AKTIVITAS INVESTASI</td>
                                    <td class="text-end">
                                        @if ($e > 0)
                                            Rp
                                            {{ number_format($e, 0, ',', '.') }}
                                        @elseif($e < 0)
                                            (Rp
                                            {{ number_format(abs($e), 0, ',', '.') }})
                                        @elseif($e == 0)
                                            -
                                        @endif
                                    </td>
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
                                    <td class="text-end">
                                        @if ($kenaikan_pinjaman_jangka_panjang_tahun_lalu - $kenaikan_pinjaman_jangka_panjang_tahun_ini > 0)
                                            Rp
                                            {{ number_format($kenaikan_pinjaman_jangka_panjang_tahun_lalu - $kenaikan_pinjaman_jangka_panjang_tahun_ini, 0, ',', '.') }}
                                        @elseif($kenaikan_pinjaman_jangka_panjang_tahun_lalu - $kenaikan_pinjaman_jangka_panjang_tahun_ini < 0)
                                            (Rp
                                            {{ number_format(abs($kenaikan_pinjaman_jangka_panjang_tahun_lalu - $kenaikan_pinjaman_jangka_panjang_tahun_ini), 0, ',', '.') }})
                                        @elseif($kenaikan_pinjaman_jangka_panjang_tahun_lalu - $kenaikan_pinjaman_jangka_panjang_tahun_ini == 0)
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kenaikan_pinjaman_jangka_panjang_tahun_lalu > 0)
                                            Rp
                                            {{ number_format($kenaikan_pinjaman_jangka_panjang_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($kenaikan_pinjaman_jangka_panjang_tahun_ini > 0)
                                            Rp
                                            {{ number_format($kenaikan_pinjaman_jangka_panjang_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>Penurunan Pinjaman Jangka Panjang</td>
                                    <td class="text-end">
                                        @if ($penurunan_pinjaman_jangka_panjang_tahun_lalu - $penurunan_pinjaman_jangka_panjang_tahun_ini > 0)
                                            Rp
                                            {{ number_format($penurunan_pinjaman_jangka_panjang_tahun_lalu - $penurunan_pinjaman_jangka_panjang_tahun_ini, 0, ',', '.') }}
                                        @elseif($penurunan_pinjaman_jangka_panjang_tahun_lalu - $penurunan_pinjaman_jangka_panjang_tahun_ini < 0)
                                            (Rp
                                            {{ number_format(abs($penurunan_pinjaman_jangka_panjang_tahun_lalu - $penurunan_pinjaman_jangka_panjang_tahun_ini), 0, ',', '.') }})
                                        @elseif($penurunan_pinjaman_jangka_panjang_tahun_lalu - $penurunan_pinjaman_jangka_panjang_tahun_ini == 0)
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_pinjaman_jangka_panjang_tahun_lalu > 0)
                                            Rp
                                            {{ number_format($penurunan_pinjaman_jangka_panjang_tahun_lalu, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($penurunan_pinjaman_jangka_panjang_tahun_ini > 0)
                                            Rp
                                            {{ number_format($penurunan_pinjaman_jangka_panjang_tahun_ini, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>Pembayaran dividen</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Penambahan/ penanaman modal</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>



                                @php
                                    $f =
                                        $kenaikan_pinjaman_jangka_panjang_tahun_lalu -
                                        $kenaikan_pinjaman_jangka_panjang_tahun_ini +
                                        ($penurunan_pinjaman_jangka_panjang_tahun_lalu -
                                            $penurunan_pinjaman_jangka_panjang_tahun_ini);
                                @endphp
                                <tr class="fw-bold">
                                    <td>F</td>
                                    <td>ARUS KAS BERSIH DARI AKTIVITAS PENDANAAN</td>
                                    <td class="text-end">
                                        @if ($f > 0)
                                            Rp
                                            {{ number_format($f, 0, ',', '.') }}
                                        @elseif($f < 0)
                                            (Rp
                                            {{ number_format(abs($f), 0, ',', '.') }})
                                        @elseif($f == 0)
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>

                                @php
                                    $g = $d + $e + $f;
                                @endphp
                                <tr class="fw-bold">
                                    <td>G</td>
                                    <td>KENAIKAN (PENURUNAN) KAS DAN SETARA KAS (D+E+F)</td>
                                    <td class="text-end">
                                        @if ($g > 0)
                                            Rp
                                            {{ number_format($g, 0, ',', '.') }}
                                        @elseif($g < 0)
                                            (Rp
                                            {{ number_format(abs($g), 0, ',', '.') }})
                                        @elseif($g == 0)
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>H</td>
                                    <td>SALDO KAS AWAL PERIODE (G+H)</td>
                                    <td class="text-end">Rp
                                        {{ number_format($saldoKasTunaiAwal + $saldoKasBankAwal, 0, ',', '.') }}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>I</td>
                                    <td>SALDO KAS AKHIR PERIODE </td>
                                    <td class="text-end">Rp
                                        {{ number_format($saldoKasTunai + $saldoKasBank, 0, ',', '.') }}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                            </tbody>
                        </table>

                        Kas Tunai : Rp {{ number_format($saldoKasTunai, 0, ',', '.') }}
                        <br>
                        Kas Bank : Rp {{ number_format($saldoKasBank, 0, ',', '.') }}
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
