<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIA Yayasan Darussalam | Buku Besar</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/YDB_PNG.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
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
                                <h5 class="card-title">Laporan Komprehensif</h5><br><br>
                                <div class="table-responsive">
                                    {{-- <form method="GET" action="{{ route('buku-besar.index') }}">
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="start_date" class="form-label">Dari Tanggal</label>
                                                <input type="date" class="form-control" name="start_date"
                                                    value="{{ request('start_date') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                                <input type="date" class="form-control" name="end_date"
                                                    value="{{ request('end_date') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="search" class="form-label">Cari</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="search"
                                                        placeholder="Cari apa saja..." value="{{ request('search') }}">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="ti ti-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="akun" class="form-label">Filter Akun</label>
                                                <select class="form-control" name="akun">
                                                    <option value="">-- Pilih Akun --</option>
                                                    @foreach ($akunList as $akun)
                                                        <option value="{{ $akun->id_akun }}"
                                                            {{ request('akun') == $akun->id_akun ? 'selected' : '' }}>
                                                            {{ $akun->akun }}
                                                        </option>
                                                    @endforeach
                                                </select>
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
                                    </form> --}}




                                    <form method="GET" action="/laporan-komprehensif">
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
                                            </div>
                                            <div class="col-md-3 align-self-end">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                    

                                    <br>

                                    {{-- <form method="GET" action="{{ route('neraca-saldo.index') }}">
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
                                            <div class="col-md-4 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                            </div>
                                        </div>
                                    </form> --}}


                                    <!-- Tabel Neraca Saldo -->
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Akun</th>
                                                <th class="text-end">Jumlah (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- TANPA PEMBATASAN DARI PEMBERI SUMBER DAYA -->
                                            <tr class="table-primary fw-bold">
                                                <td colspan="2">TANPA PEMBATASAN DARI PEMBERI SUMBER DAYA</td>
                                            </tr>
                                    
                                            <!-- Pendapatan -->
                                            <tr class="table-secondary">
                                                <td colspan="2"><strong>Pendapatan</strong></td>
                                            </tr>
                                            @foreach($pendapatan as $item)
                                            <tr>
                                                <td>&nbsp;&nbsp;&nbsp;{{ $item->nama_akun }}</td>
                                                <td class="text-end">Rp {{ number_format($item->total, 2, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="fw-bold">
                                                <td>Total Pendapatan</td>
                                                <td class="text-end">Rp {{ number_format($total_pendapatan, 2, ',', '.') }}</td>
                                            </tr>
                                    
                                            <!-- Beban -->
                                            <tr class="table-secondary">
                                                <td colspan="2"><strong>Beban</strong></td>
                                            </tr>
                                            @foreach($beban as $item)
                                            <tr>
                                                <td>&nbsp;&nbsp;&nbsp;{{ $item->nama_akun }}</td>
                                                <td class="text-end">(Rp {{ number_format($item->total, 2, ',', '.') }})</td>
                                            </tr>
                                            @endforeach
                                            <tr class="fw-bold">
                                                <td>Total Beban</td>
                                                <td class="text-end">(Rp {{ number_format($total_beban, 2, ',', '.') }})</td>
                                            </tr>
                                    
                                            <!-- Surplus/Defisit -->
                                            <tr class="table-warning fw-bold">
                                                <td>Surplus (Defisit)</td>
                                                <td class="text-end">Rp {{ number_format($surplus_defisit, 2, ',', '.') }}</td>
                                            </tr>
                                    
                                            <!-- DENGAN PEMBATASAN DARI PEMBERI SUMBER DAYA -->
                                            <tr class="table-primary fw-bold">
                                                <td colspan="2">DENGAN PEMBATASAN DARI PEMBERI SUMBER DAYA</td>
                                            </tr>
                                    
                                            <!-- Pendapatan Dengan Pembatasan -->
                                            <tr class="table-secondary">
                                                <td colspan="2"><strong>Pendapatan</strong></td>
                                            </tr>
                                            @foreach($pendapatan_terikat as $item)
                                            <tr>
                                                <td>&nbsp;&nbsp;&nbsp;{{ $item->nama_akun }}</td>
                                                <td class="text-end">Rp {{ number_format($item->total, 2, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="fw-bold">
                                                <td>Total Pendapatan</td>
                                                <td class="text-end">Rp {{ number_format($total_pendapatan_terikat, 2, ',', '.') }}</td>
                                            </tr>
                                    
                                            <!-- Beban Dengan Pembatasan -->
                                            <tr class="table-secondary">
                                                <td colspan="2"><strong>Beban</strong></td>
                                            </tr>
                                            @foreach($beban_terikat as $item)
                                            <tr>
                                                <td>&nbsp;&nbsp;&nbsp;{{ $item->nama_akun }}</td>
                                                <td class="text-end">(Rp {{ number_format($item->total, 2, ',', '.') }})</td>
                                            </tr>
                                            @endforeach
                                            <tr class="fw-bold">
                                                <td>Total Beban</td>
                                                <td class="text-end">(Rp {{ number_format($total_beban_terikat, 2, ',', '.') }})</td>
                                            </tr>
                                    
                                            <tr class="table-warning fw-bold">
                                                <td>Surplus (Defisit)</td>
                                                <td class="text-end">Rp {{ number_format($surplus_defisit_terikat, 2, ',', '.') }}</td>
                                            </tr>
                                    
                                            <!-- Penghasilan Komprehensif Lain -->
                                            <tr class="table-primary fw-bold">
                                                <td>Penghasilan Komprehensif Lain</td>
                                                <td class="text-end">Rp {{ number_format($penghasilan_komprehensif_lain, 2, ',', '.') }}</td>
                                            </tr>
                                    
                                            <!-- Total Penghasilan Komprehensif -->
                                            <tr class="table-dark text-white fw-bold">
                                                <td>Total Penghasilan Komprehensif</td>
                                                <td class="text-end">Rp {{ number_format($total_penghasilan_komprehensif, 2, ',', '.') }}</td>
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
</body>

</html>
