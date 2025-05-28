@extends('layouts.layout')

@push('styles')
    <title>SIA Yayasan Darussalam | Register</title>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">Tambah User</h5>
            <div class="d-flex justify-content-end mb-3">
                <div class="form-group">
                    {{-- <label for="tipe_akun" class="form-label me-2"><strong>Tipe Akun:</strong></label> --}}
                    <!-- Pilih Tipe Akun -->
                    <select name="tipe_akun" id="tipe_akun" class="form-select" style="width: auto; display: inline-block;">
                        <option value="divisi">Akuntan Divisi</option>
                        <option value="unit">Akuntan Unit</option>
                        <option value="auditor">Auditor</option> <!-- Tambahan baru -->
                    </select>


                </div>
            </div>
            <div class="card">

                <div id="form-unit" class="card-body">
                    {{-- <form method="post" action="/register-akuntan-unit"> --}}
                    <form method="post" action="{{ route('register.akuntan.unit') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="tanggal" class="form-label"><strong>Profil</strong></label>
                        </div>

                        <!-- Unit Selection -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Unit</label>
                                <select name="id_unit" class="form-select" required>
                                    <option value="" disabled selected>Pilih Unit</option>
                                    @foreach ($unit as $item)
                                        <option value="{{ $item->id_unit }}">{{ $item->kode_unit }} -
                                            {{ $item->unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Personal Details -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" id="nama" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Telp</label>
                                <input type="text" class="form-control" name="telp" id="telp" required>
                            </div>
                        </div>

                        <br>
                        <hr class="border border-3 border-dark">
                        <br>

                        <!-- User Account -->
                        <div id="akunContainer">
                            <div class="mb-3">
                                <label for="akun" class="form-label"><strong>Akun
                                        Pengguna</strong></label>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" id="username" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" id="password" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        id="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <br>
                        <hr class="border border-3 border-dark">
                        <br>

                        <!-- Access Rights Table -->
                        <div id="aksesContainer">
                            <div class="mb-4">
                                <label class="form-label"><strong>Hak Akses</strong></label>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Modul</th>
                                                <th>View</th>
                                                <th>Create</th>
                                                <th>Update</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>


                                            <tr>
                                                <td class="text-start">Jurnal Umum</td>
                                                <td>
                                                    <input type="hidden" name="view_jurnal_umum" value="0">
                                                    <input class="form-check-input" type="checkbox" name="view_jurnal_umum"
                                                        value="1">
                                                </td>
                                                <td>
                                                    <input type="hidden" name="create_jurnal_umum" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="create_jurnal_umum" value="1">
                                                </td>
                                                <td>
                                                    <input type="hidden" name="update_jurnal_umum" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="update_jurnal_umum" value="1">
                                                </td>
                                                <td>
                                                    <input type="hidden" name="delete_jurnal_umum" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="delete_jurnal_umum" value="1">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">Buku Besar</td>
                                                <td>
                                                    <input type="hidden" name="view_buku_besar" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="view_buku_besar" value="1">
                                                </td>
                                                <td>
                                                    <input type="hidden" name="create_buku_besar" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="create_buku_besar" value="1">
                                                </td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td>
                                                    <input type="hidden" name="delete_buku_besar" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="delete_buku_besar" value="1">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">Laporan Neraca</td>
                                                <td>
                                                    <input type="hidden" name="view_laporan_neraca" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="view_laporan_neraca" value="1">
                                                </td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">Laporan Komprehensif</td>
                                                <td>
                                                    <input type="hidden" name="view_laporan_komprehensif"
                                                        value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="view_laporan_komprehensif" value="1">
                                                </td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">Laporan Perubahan Aset Neto</td>
                                                <td>
                                                    <input type="hidden" name="view_laporan_perubahan_aset_neto"
                                                        value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="view_laporan_perubahan_aset_neto" value="1">
                                                </td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">Laporan Posisi Keuangan</td>
                                                <td>
                                                    <input type="hidden" name="view_laporan_posisi_keuangan"
                                                        value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="view_laporan_posisi_keuangan" value="1">
                                                </td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">Laporan Arus Kas</td>
                                                <td>
                                                    <input type="hidden" name="view_laporan_arus_kas" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="view_laporan_arus_kas" value="1">
                                                </td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">Laporan Catatan Atas Laporan
                                                    Keuangan</td>
                                                <td>
                                                    <input type="hidden"
                                                        name="view_laporan_catatan_atas_laporan_keuangan" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="view_laporan_catatan_atas_laporan_keuangan" value="1">
                                                </td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">Laporan Proyeksi Rencana dan
                                                    Realisasi Anggaran</td>
                                                <td>
                                                    <input type="hidden"
                                                        name="view_laporan_proyeksi_rencana_dan_realisasi_anggaran"
                                                        value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="view_laporan_proyeksi_rencana_dan_realisasi_anggaran"
                                                        value="1">
                                                </td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                                <td><input class="form-check-input" type="checkbox" disabled></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>

                        <br>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary col-12">Simpan</button>
                    </form>
                </div>


                <div id="form-divisi" class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="/register-akuntan-divisi">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label"><strong>Profil</strong></label>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Divisi</label>
                                <select name="id_divisi" class="form-select" required>
                                    <option value="" disabled selected>Pilih Divisi</option>
                                    @foreach ($divisi as $item)
                                        <option value="{{ $item->id_divisi }}">{{ $item->divisi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Telp</label>
                                <input type="text" class="form-control" name="telp" required>
                            </div>
                        </div>

                        <br>
                        <hr class="border border-3 border-dark">
                        <br>

                        <div id="akunContainer">
                            <div class="mb-3">
                                <label class="form-label"><strong>Akun Pengguna</strong></label>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <br>

                        <button type="submit" class="btn btn-primary col-12">Simpan</button>
                    </form>
                </div>


                <div id="form-auditor" class="card-body">
                    <form method="post" action="{{ route('register.auditor') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label"><strong>Profil</strong></label>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Telp</label>
                                <input type="text" class="form-control" name="telp" required>
                            </div>
                        </div>

                        <br>
                        <hr class="border border-3 border-dark">
                        <br>

                        <div class="mb-3">
                            <label class="form-label"><strong>Akun Pengguna</strong></label>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <br>

                        <button type="submit" class="btn btn-primary col-12">Simpan</button>
                    </form>
                </div>

            </div>

        </div>
    </div>
    <div class="py-6 px-6 text-center">
        <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tipeAkunSelect = document.getElementById("tipe_akun");
            const formUnit = document.getElementById("form-unit");
            const formDivisi = document.getElementById("form-divisi");
            const formAuditor = document.getElementById("form-auditor"); // tambahan

            function toggleForms() {
                const selectedValue = tipeAkunSelect.value;
                if (selectedValue === "unit") {
                    formUnit.style.display = "block";
                    formDivisi.style.display = "none";
                    formAuditor.style.display = "none";
                } else if (selectedValue === "divisi") {
                    formUnit.style.display = "none";
                    formDivisi.style.display = "block";
                    formAuditor.style.display = "none";
                } else if (selectedValue === "auditor") {
                    formUnit.style.display = "none";
                    formDivisi.style.display = "none";
                    formAuditor.style.display = "block";
                }
            }

            tipeAkunSelect.addEventListener("change", toggleForms);
            toggleForms();
        });
    </script>
@endpush
