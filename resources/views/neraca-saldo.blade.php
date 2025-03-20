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
                                <h5 class="card-title">Neraca Saldo</h5><br><br>
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



                                    <br>

                                    <form method="GET" action="{{ route('neraca-saldo.index') }}">
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
                                    </form>


                                    <!-- Tabel Neraca Saldo -->
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
                                                        <td colspan="3">&nbsp;&nbsp;&nbsp;{{ $sub_kategori }}</td>
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
                                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $akun->akun }}</td>
                                                            <td class="text-end">Rp {{ number_format($debit, 2) }}</td>
                                                            <td class="text-end">Rp {{ number_format($kredit, 2) }}</td>
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
