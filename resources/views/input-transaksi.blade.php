<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIA Yayasan Darussalam | Input Transaksi</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/YDB_PNG.png" />
    <link rel="stylesheet" href="../../node_modules/simplebar/dist/simplebar.min.css">
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
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">Input Transaksi</h5>
                        <div class="card">
                            <div class="card-body">
                                <form method="post" action="/store-jurnal_umum">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal</label>
                                        <input type="date" class="form-control" name="tanggal" id="tanggal"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <input type="text" class="form-control" name="keterangan" id="keterangan"
                                            required>
                                    </div>

                                    <div class="row mb-3 akun-row">
                                        <div class="col-md-6">
                                            <label class="form-label">Jenis Transaksi</label>
                                            <select name="id_jenis_transaksi[]" class="form-select" required>
                                                <option value=""></option>
                                                @foreach ($jenis_transaksi as $data)
                                                    <option value="{{ $data->id_jenis_transaksi }}">
                                                        {{ $data->jenis_transaksi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Unit</label>
                                            <select name="id_unit[]" class="form-select" required>
                                                <option value=""></option>
                                                @foreach ($unit as $data_unit)
                                                    <option value="{{ $data_unit->id_unit }}">
                                                        {{ $data_unit->kode_unit }} - {{ $data_unit->unit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Divisi</label>
                                            <select name="id_divisi[]" class="form-select" required>
                                                <option value=""></option>
                                                @foreach ($divisi as $data_divisi)
                                                    <option value="{{ $data_divisi->id_divisi }}">
                                                        {{ $data_divisi->divisi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>


                                    <div class="row mb-3 akun-row">
                                        <div class="col-md-6">
                                            <label for="keterangan" class="form-label">Kode Sumbangan</label>
                                            <input type="text" class="form-control" name="keterangan"
                                                id="keterangan" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="keterangan" class="form-label">Kode P&H</label>
                                            <input type="text" class="form-control" name="keterangan"
                                                id="keterangan" required>
                                        </div>

                                    </div>

                                    <br>
                                    <hr class="border border-3 border-dark">
                                    <br>
                                    <div id="akunContainer">
                                        <!-- Akun Default 1 -->
                                        <div class="row mb-3 akun-row">
                                            <div class="col-md-6">
                                                <label class="form-label">Akun</label>
                                                <select name="id_akun[]" class="form-select" required>
                                                    <option value="">Pilih Akun</option>
                                                    @foreach ($akun as $data_akun)
                                                        <option value="{{ $data_akun->id_akun }}">
                                                            {{ $data_akun->kode_akun }} - {{ $data_akun->akun }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Debit</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control format-rupiah"
                                                        name="debit[]" oninput="formatRupiah(this)">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Kredit</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control format-rupiah"
                                                        name="kredit[]" oninput="formatRupiah(this)">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Akun Default 2 -->
                                        <div class="row mb-3 akun-row">
                                            <div class="col-md-6">
                                                <label class="form-label">Akun</label>
                                                <select name="id_akun[]" class="form-select" required>
                                                    <option value="">Pilih Akun</option>
                                                    @foreach ($akun as $data_akun)
                                                        <option value="{{ $data_akun->id_akun }}">
                                                            {{ $data_akun->kode_akun }} - {{ $data_akun->akun }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Debit</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control format-rupiah"
                                                        name="debit[]" oninput="formatRupiah(this)">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Kredit</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control format-rupiah"
                                                        name="kredit[]" oninput="formatRupiah(this)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-success mb-3" onclick="tambahAkun()">Tambah</button>
                                    </div>
                                    
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="postingBukuBesar"
                                            name="postingBukuBesar">
                                        <label class="form-check-label" for="postingBukuBesar">Posting ke Buku
                                            Besar</label>
                                    </div>

                                    <button type="submit" class="btn btn-primary col-12">Submit</button>
                                </form>

                                <script>
                                    function tambahAkun() {
                                        let container = document.getElementById('akunContainer');
                                        let newRow = document.createElement('div');
                                        newRow.classList.add('row', 'mb-3', 'akun-row');
                                        newRow.innerHTML = `
                                            <div class="col-md-6">
                                                <label class="form-label">Akun</label>
                                                <select name="id_akun[]" class="form-select" required>
                                                    <option value="">Pilih Akun</option>
                                                    @foreach ($akun as $data_akun)
                                                    <option value="{{ $data_akun->id_akun }}">{{ $data_akun->kode_akun }} - {{ $data_akun->akun }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Debit</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control format-rupiah" name="debit[]" oninput="formatRupiah(this)">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Kredit</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control format-rupiah" name="kredit[]" oninput="formatRupiah(this)">
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-end">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="hapusAkun(this)">Hapus</button>
                                            </div>
                                        `;
                                        container.appendChild(newRow);
                                        updateHapusButtons();
                                    }

                                    function hapusAkun(button) {
                                        let row = button.closest('.akun-row');
                                        row.remove();
                                        updateHapusButtons();
                                    }

                                    function updateHapusButtons() {
                                        let rows = document.querySelectorAll('.akun-row');
                                        rows.forEach((row, index) => {
                                            let deleteButton = row.querySelector('button.btn-danger');
                                            if (deleteButton) {
                                                deleteButton.style.display = index >= 2 ? 'inline-block' : 'none';
                                            }
                                        });
                                    }

                                    function formatRupiah(input) {
                                        let value = input.value.replace(/[^0-9]/g, '');
                                        let formatted = new Intl.NumberFormat('id-ID').format(value);
                                        input.value = formatted;
                                    }

                                    document.addEventListener("DOMContentLoaded", updateHapusButtons);
                                </script>







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
    <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="../assets/js/sidebarmenu.js"></script>
    <script src="../assets/js/app.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>



</body>

</html>
