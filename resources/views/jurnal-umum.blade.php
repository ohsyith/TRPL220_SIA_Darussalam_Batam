<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIA Yayasan Darussalam | Jurnal Umum</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/YDB_PNG.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<style>
    .th-with-dot {
        position: relative;
    }

    .dot-red {
        position: absolute;
        top: 5px;
        left: 5px;
        width: 10px;
        height: 10px;
        background-color: red;
        border-radius: 50%;
        cursor: pointer;
    }
</style>

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
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse"
                                href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-icon-hover" href="javascript:void(0)">
                                <i class="ti ti-bell-ringing"></i>
                                <div class="notification bg-primary rounded-circle"></div>
                            </a>
                        </li>
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="../assets/images/profile/user-1.jpg" alt="" width="35"
                                        height="35" class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="javascript:void(0)"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="javascript:void(0)"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-mail fs-6"></i>
                                            <p class="mb-0 fs-3">My Account</p>
                                        </a>
                                        <a href="javascript:void(0)"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-list-check fs-6"></i>
                                            <p class="mb-0 fs-3">My Task</p>
                                        </a>
                                        <a href="./authentication-login.html"
                                            class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--  Header End -->
            <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Jurnal Umum</h5><br><br>
                                <div class="table-responsive">
                                    <form method="GET" action="{{ route('jurnal-umum.index') }}">
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="start_date" class="form-label">Dari Tanggal</label>
                                                <input type="date" class="form-control" name="start_date"
                                                    value="{{ request('start_date') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                                <input type="date" class="form-control" name="end_date"
                                                    value="{{ request('end_date') }}">
                                            </div>
                                            <div class="col-md-4">
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

                                        <!-- Tombol Reset di Pojok Kanan Bawah -->
                                        <div class="row">
                                            <div class="col-12 text-end">
                                                <a href="{{ route('jurnal-umum.index') }}" class="btn btn-secondary">
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
                                                <th scope="col" class="text-center">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            @php
                                                $groupedData = $detailjurnalumum->groupBy('jurnal_umum.no_bukti');
                                                $totalDebit = 0;
                                                $totalKredit = 0;
                                                $totalKeseluruhan = 0;
                                            @endphp

                                            @foreach ($groupedData as $no_bukti => $group)
                                                @php $rowspan = $group->count(); @endphp
                                                @foreach ($group as $index => $data)
                                                    <tr>
                                                        @if ($index === 0)
                                                            {{-- <th scope="row" class="ps-0 fw-medium" --}}

                                                            {{-- <th scope="row" class="ps-0 fw-medium th-with-dot"
                                                                rowspan="{{ $rowspan }}">
                                                                {{ $data->jurnal_umum->tanggal }}
                                                            </th> --}}

                                                            <th scope="row"
                                                                class="ps-0 fw-medium {{ in_array($data->jurnal_umum->id, $postedJurnalIds) ? '' : 'position-relative th-with-dot' }}"
                                                                rowspan="{{ $rowspan }}">

                                                                @if (!in_array($data->jurnal_umum->id, $postedJurnalIds))
                                                                    <span class="dot-red" data-bs-toggle="modal"
                                                                        data-bs-target="#postingModal"></span>
                                                                @endif

                                                                {{ $data->jurnal_umum->tanggal }}
                                                            </th>










                                                            <td class="text-center fw-medium"
                                                                rowspan="{{ $rowspan }}">
                                                                {{ $data->jurnal_umum->no_bukti }}
                                                            </td>
                                                            <td class="text-center fw-medium"
                                                                rowspan="{{ $rowspan }}">
                                                                {{ $data->jurnal_umum->keterangan }}
                                                            </td>
                                                            <td class="text-center fw-medium"
                                                                rowspan="{{ $rowspan }}">
                                                                {{ $data->jurnal_umum->jenis_transaksi->jenis_transaksi }}
                                                            </td>
                                                            <td class="text-center fw-medium"
                                                                rowspan="{{ $rowspan }}">
                                                                {{ $data->jurnal_umum->unit->unit }}
                                                            </td>
                                                            <td class="text-center fw-medium"
                                                                rowspan="{{ $rowspan }}">
                                                                {{ $data->jurnal_umum->divisi->divisi }}
                                                            </td>
                                                            <td class="text-center fw-medium"
                                                                rowspan="{{ $rowspan }}">
                                                                {{ $data->jurnal_umum->kode_sumbangan }}
                                                            </td>
                                                            <td class="text-center fw-medium"
                                                                rowspan="{{ $rowspan }}">
                                                                {{ $data->jurnal_umum->kode_ph }}
                                                            </td>
                                                        @endif
                                                        <td class="text-center fw-medium border-2">
                                                            @if ($data->debit_kredit === 'debit')
                                                                {{ $data->akun->akun ?? 'Akun Tidak Ditemukan' }}
                                                                (Rp {{ number_format($data->nominal) }})
                                                                @php $totalDebit += $data->nominal; @endphp
                                                            @endif
                                                        </td>
                                                        <td class="text-center fw-medium border-2">
                                                            @if ($data->debit_kredit === 'kredit')
                                                                {{ $data->akun->akun ?? 'Akun Tidak Ditemukan' }}
                                                                (Rp {{ number_format($data->nominal) }})
                                                                @php $totalKredit += $data->nominal; @endphp
                                                            @endif
                                                        </td>
                                                        <td class="text-center fw-medium">
                                                            Rp {{ number_format($data->nominal) }}
                                                            @php $totalKeseluruhan += $data->nominal; @endphp
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach

                                            <!-- Row Total -->
                                            <tr class="fw-bold bg-light">
                                                <td colspan="8" class="text-end">Total</td>
                                                <td class="text-center">Rp {{ number_format($totalDebit) }}</td>
                                                <td class="text-center">Rp {{ number_format($totalKredit) }}</td>
                                                <td class="text-center">Rp {{ number_format($totalKeseluruhan) }}</td>
                                            </tr>

                                        </tbody>

                                    </table>

                                    <!-- Modal -->
                                    <div class="modal fade" id="postingModal" tabindex="-1"
                                        aria-labelledby="postingModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-sm modal-dialog-centered">
                                            <!-- Tambahkan modal-dialog-centered -->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="postingModalLabel">Konfirmasi</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    Posting ke Buku Besar?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm">Posting</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


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
</body>

</html>
