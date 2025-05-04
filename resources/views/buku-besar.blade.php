<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIA Yayasan Darussalam | Buku Besar</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/YDB_PNG.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <link rel="stylesheet" href="../css/sidebar.css" />

</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        <x-sidebar></x-sidebar>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <x-header></x-header>

            <!--  Header End -->
            <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Buku Besar</h5><br><br>
                                <div class="table-responsive">
                                    <form method="GET" action="{{ route('buku-besar.index') }}">
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label for="start_date" class="form-label">Dari Tanggal</label>
                                                <input type="date" class="form-control" name="start_date"
                                                    value="{{ request('start_date') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                                <input type="date" class="form-control" name="end_date"
                                                    value="{{ request('end_date') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="akun" class="form-label">Filter Akun</label>
                                                <select class="form-control" name="akun">
                                                    <option value="">-- Pilih Akun --</option>
                                                    @foreach ($akunList as $akun)
                                                        <option value="{{ $akun->id_akun }}"
                                                            {{ request('akun', 1) == $akun->id_akun ? 'selected' : '' }}>
                                                            {{ $akun->akun }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label for="search" class="form-label">Cari</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="search"
                                                        placeholder="Cari apa saja..." value="{{ request('search') }}">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="ti ti-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            

                                        </div>

                                        <!-- Tombol Reset -->
                                        <div class="row">
                                            <div class="col-12 text-end">
                                                <a href="{{ route('buku-besar.index') }}" class="btn btn-secondary">
                                                    <i class="ti ti-refresh"></i> Reset
                                                </a>
                                            </div>
                                        </div>
                                    </form>



                                    <br>

                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead>
                                            <tr class="border-2 border-bottom border-primary border-0">
                                                <th scope="col" class="ps-0">Tgl</th>
                                                <th scope="col">No Bukti</th>
                                                <th scope="col" class="text-center">Keterangan</th>
                                                <th scope="col" class="text-center">Jenis</th>
                                                <th scope="col" class="text-center">Unit</th>
                                                <th scope="col" class="text-center">Divisi</th>
                                                <th scope="col" class="text-center">Kd Sumbangan</th>
                                                <th scope="col" class="text-center">Kd P&H</th>
                                                <th scope="col" class="text-center">Akun Debit (Rp)</th>
                                                <th scope="col" class="text-center">Akun Kredit (Rp)</th>
                                                {{-- <th scope="col" class="text-center">Jumlah</th> --}}
                                            </tr>
                                        </thead>

                                        <tbody class="table-group-divider">
                                            {{-- @foreach ($detail_jurnal as $detail)
                                                @php
                                                    if ($detail->debit_kredit === 'debit') {
                                                        $total_debit += $detail->nominal;
                                                    } elseif ($detail->debit_kredit === 'kredit') {
                                                        $total_kredit += $detail->nominal;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="ps-0 fw-medium">{{ $detail->jurnal_umum->tanggal }}</td>
                                                    <td class="text-center fw-medium">
                                                        {{ $detail->jurnal_umum->no_bukti }}</td>
                                                    <td class="text-center fw-medium">
                                                        {{ $detail->jurnal_umum->keterangan }}</td>
                                                    <td class="text-center fw-medium">
                                                        {{ $detail->jurnal_umum->jenis_transaksi ?? '-' }}
                                                    </td>
                                                    <td class="text-center fw-medium">
                                                        {{ $detail->jurnal_umum->unit->unit ?? '-' }}</td>
                                                    <td class="text-center fw-medium">
                                                        {{ $detail->jurnal_umum->divisi->divisi ?? '-' }}</td>
                                                    <td class="text-center fw-medium">
                                                        {{ $detail->jurnal_umum->kode_sumbangan ?? '-' }}</td>
                                                    <td class="text-center fw-medium">
                                                        {{ $detail->jurnal_umum->kode_ph ?? '-' }}</td>
                                                    <td class="text-center fw-medium">
                                                        @if ($detail->debit_kredit === 'debit')
                                                            {{ $detail->akun->akun ?? 'Akun Tidak Ditemukan' }}
                                                            (Rp {{ number_format($detail->nominal) }})
                                                        @endif
                                                    </td>
                                                    <td class="text-center fw-medium">
                                                        @if ($detail->debit_kredit === 'kredit')
                                                            {{ $detail->akun->akun ?? 'Akun Tidak Ditemukan' }}
                                                            (Rp {{ number_format($detail->nominal) }})
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach --}}

                                            @foreach ($detail_jurnal as $detail)
                                                <tr>
                                                    <td class="ps-0 fw-medium">{{ $detail->tanggal }}</td>
                                                    <td class="text-center fw-medium">{{ $detail->no_bukti }}</td>
                                                    <td class="text-center fw-medium">{{ $detail->keterangan }}</td>
                                                    <td class="text-center fw-medium">{{ $detail->jenis ?? '-' }}</td>
                                                    <td class="text-center fw-medium">{{ $detail->unit ?? '-' }}</td>
                                                    <td class="text-center fw-medium">{{ $detail->divisi ?? '-' }}
                                                    </td>
                                                    <td class="text-center fw-medium">
                                                        {{ $detail->kode_sumbangan ?? '-' }}</td>
                                                    <td class="text-center fw-medium">{{ $detail->kode_ph ?? '-' }}
                                                    </td>
                                                    <td class="text-center fw-medium">
                                                        @if ($detail->debit_kredit === 'debit')
                                                            {{ $detail->akun ?? 'Akun Tidak Ditemukan' }}
                                                            (Rp {{ number_format($detail->nominal) }})
                                                        @endif
                                                    </td>
                                                    <td class="text-center fw-medium">
                                                        @if ($detail->debit_kredit === 'kredit')
                                                            {{ $detail->akun ?? 'Akun Tidak Ditemukan' }}
                                                            (Rp {{ number_format($detail->nominal) }})
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                            {{-- Baris Total --}}
                                            <tr class="fw-bold">
                                                <td colspan="8" class="text-end">Total</td>
                                                <td class="text-center">Rp {{ number_format($total_debit) }}</td>
                                                <td class="text-center">Rp {{ number_format($total_kredit) }}</td>
                                                {{-- <td class="text-center">Rp
                                                    {{ number_format($total_debit + $total_kredit) }}</td> --}}
                                            </tr>
                                        </tbody>


                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="py-6 px-6 text-center">
                        <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>
                    </div>
                </div>
            </div>
        </div>
        <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
        <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
        <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
        <script src="../assets/js/sidebarmenu.js"></script>
        <script src="../assets/js/app.min.js"></script>
        <script src="../assets/js/dashboard.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

        {{-- sidebar --}}
        <script src="../js/sidebar.js"></script>
</body>

</html>
