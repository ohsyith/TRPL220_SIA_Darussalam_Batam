@extends('layouts.layout')
@push('styles')
    <title>SIA Yayasan Darussalam | Akuntan Unit</title>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">Detail Pengguna</h5>

            <div class="card">
                <div id="form-unit" class="card-body">
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

                    <form method="POST" {{-- action="{{ route('akuntan-unit.update', $akuntan_unit->id_akuntan_unit) }}"> --}} action="">
                        @csrf
                        @method('PUT')

                        {{-- PROFIL --}}
                        <div class="mb-3">
                            <label class="form-label"><strong>Profil</strong></label>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Unit</label>
                                <select name="id_unit" class="form-select" required>
                                    <option value="" disabled>Pilih unit</option>
                                    @foreach ($unit as $item)
                                        <option value="{{ $item->id_unit }}"
                                            {{ $akuntan_unit->id_unit == $item->id_unit ? 'selected' : '' }}>
                                            {{ $item->kode_unit }} - {{ $item->unit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama"
                                    value="{{ $akuntan_unit->user->nama }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ $akuntan_unit->email }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Telp</label>
                                <input type="text" class="form-control" name="telp" value="{{ $akuntan_unit->telp }}"
                                    required>
                            </div>
                        </div>

                        <hr class="my-4 border-dark">

                        {{-- AKUN PENGGUNA --}}
                        <div class="mb-3">
                            <label class="form-label"><strong>Akun Pengguna</strong></label>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username"
                                    value="{{ $akuntan_unit->user->username }}" required>
                            </div>
                        </div>

                        {{-- UBAH PASSWORD --}}
                        <div class="mb-3">
                            <label class="form-label"><strong>Ganti Password</strong> <span class="text-muted">(Kosongkan
                                    jika tidak ingin mengubah)</span></label>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Password Lama</label>
                                <input type="password" class="form-control" name="old_password">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Password Baru</label>
                                <input type="password" class="form-control" name="new_password">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" name="new_password_confirmation">
                            </div>
                        </div>

                        <br>
                        <hr class="border border-3 border-dark">
                        <br>

                        <!-- Access Rights Table -->
                        <div id="aksesContainer">
                            <div class="mb-4">
                                <label class="form-label"><strong>Hak Akses Modul</strong></label>
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
                                                        value="1" @checked(old('view_jurnal_umum', $akses->view_jurnal_umum ?? false))>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="create_jurnal_umum" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="create_jurnal_umum" value="1"
                                                        @checked(old('create_jurnal_umum', $akses->create_jurnal_umum ?? false))>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="update_jurnal_umum" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="update_jurnal_umum" value="1"
                                                        @checked(old('update_jurnal_umum', $akses->update_jurnal_umum ?? false))>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="delete_jurnal_umum" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="delete_jurnal_umum" value="1"
                                                        @checked(old('delete_jurnal_umum', $akses->delete_jurnal_umum ?? false))>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-start">Buku Besar</td>
                                                <td>
                                                    <input type="hidden" name="view_buku_besar" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="view_buku_besar" value="1"
                                                        @checked(old('view_buku_besar', $akses->view_buku_besar ?? false))>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="create_buku_besar" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="create_buku_besar" value="1"
                                                        @checked(old('create_buku_besar', $akses->create_buku_besar ?? false))>
                                                </td>
                                                <td>
                                                    <input class="form-check-input" type="checkbox" disabled>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="delete_buku_besar" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="delete_buku_besar" value="1"
                                                        @checked(old('delete_buku_besar', $akses->delete_buku_besar ?? false))>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-start">Laporan Neraca</td>
                                                <td>
                                                    <input type="hidden" name="view_laporan_neraca" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="view_laporan_neraca" value="1"
                                                        @checked(old('view_laporan_neraca', $akses->view_laporan_neraca ?? false))>
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
                                                        name="view_laporan_komprehensif" value="1"
                                                        @checked(old('view_laporan_komprehensif', $akses->view_laporan_komprehensif ?? false))>
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
                                                        name="view_laporan_perubahan_aset_neto" value="1"
                                                        @checked(old('view_laporan_perubahan_aset_neto', $akses->view_laporan_perubahan_aset_neto ?? false))>
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
                                                        name="view_laporan_posisi_keuangan" value="1"
                                                        @checked(old('view_laporan_posisi_keuangan', $akses->view_laporan_posisi_keuangan ?? false))>
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
                                                        name="view_laporan_arus_kas" value="1"
                                                        @checked(old('view_laporan_arus_kas', $akses->view_laporan_arus_kas ?? false))>
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
                                                        name="view_laporan_catatan_atas_laporan_keuangan" value="1"
                                                        @checked(old('view_laporan_catatan_atas_laporan_keuangan', $akses->view_laporan_catatan_atas_laporan_keuangan ?? false))>
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
                                                        value="1" @checked(old(
                                                                'view_laporan_proyeksi_rencana_dan_realisasi_anggaran',
                                                                $akses->view_laporan_proyeksi_rencana_dan_realisasi_anggaran ?? false))>
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

                        <span onclick="document.getElementById('form-delete-akuntan').submit();"
                            style="color:red; cursor:pointer; text-decoration:underline;">
                            Hapus Pengguna
                        </span>
                        <br>
                        <br>


                        <button type="submit" class="btn btn-primary col-12">Update</button>
                    </form>


                    <form id="form-delete-akuntan"
                        action="{{ route('akuntan-unit.destroy', $akuntan_unit->id_akuntan_unit) }}" method="POST"
                        style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <script>
                        document.getElementById('form-delete-akuntan').addEventListener('submit', function(e) {
                            if (!confirm('Yakin ingin menghapus pengguna ini?')) {
                                e.preventDefault();
                            }
                        });
                    </script>
                </div>

            </div>

        </div>
    </div>
    <div class="py-6 px-6 text-center">
        <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>
    </div>
@endsection
