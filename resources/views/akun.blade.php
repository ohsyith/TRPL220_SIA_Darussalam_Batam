@extends('layouts.layout')
@push('styles')
    <title>SIA Yayasan Darussalam | Akun</title>
    <style>
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: auto;
            margin-left: auto;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Akun</h5><br>


                    <div class="mb-3">
                        <a href="{{ asset('assets/templates/Template_Akun.xlsx') }}" class="btn btn-link text-primary p-0"
                            download>
                            <i class="fas fa-download me-1"></i> Download Template Import Akun
                        </a>
                    </div>

                    {{-- Baris Import + Reset --}}
                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">

                        {{-- Form Import --}}
                        <form action="{{ route('akun.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="file" class="form-control" required>
                                <button class="btn btn-success" type="submit">
                                    <i class="fas fa-upload me-1"></i> Import Excel
                                </button>
                            </div>
                        </form>

                        {{-- Tombol Tambah Akun --}}
                        <div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                                <i class="fas fa-plus me-1"></i> Tambah Akun
                            </button>
                        </div>



                    </div>











                    <div class="table-responsive">

                        <!-- Modal Tambah -->
                        <div class="modal fade" id="modalTambahKategori" tabindex="-1"
                            aria-labelledby="modalTambahKategoriLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">
                                <form method="post" action="{{ route('akun.store') }}">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tambah Akun</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="id_sub_kategori_akun" class="form-label">Sub
                                                    Kategori
                                                    Akun</label>
                                                <select class="form-select" name="id_sub_kategori_akun" required>
                                                    <option value="" disabled selected>Pilih Kategori
                                                        Akun</option>
                                                    @foreach ($subkategoriakun as $data)
                                                        <option value="{{ $data->id_sub_kategori_akun }}">
                                                            {{ $data->kode_sub_kategori_akun }} -
                                                            {{ $data->sub_kategori_akun }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="kode_akun" class="form-label">Kode
                                                    Akun</label>
                                                <input type="text" name="kode_akun" class="form-control" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="akun" class="form-label">Akun</label>
                                                <input type="text" name="akun" class="form-control" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="saldo_awal_debit" class="form-label">Saldo
                                                    Awal Debit</label>
                                                <input type="number" name="saldo_awal_debit" id="saldo_awal_debit"
                                                    class="form-control" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="saldo_awal_kredit" class="form-label">Saldo
                                                    Awal Kredit</label>
                                                <input type="number" name="saldo_awal_kredit" id="saldo_awal_kredit"
                                                    class="form-control" required>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Data Akun --}}
                        <table class="table text-nowrap align-middle mb-0">
                            <thead>
                                <tr class="border-2 border-bottom border-primary border-0">
                                    {{-- <th scope="col">SUB KATEGORI AKUN</th> --}}
                                    <th scope="col" class="ps-0">KODE</th>
                                    <th scope="col">AKUN</th>
                                    <th scope="col">Saldo Awal Debit</th>
                                    <th scope="col">Saldo Awal Kredit</th>
                                    {{-- <th scope="col">Budget RAPBS</th> --}}
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                @foreach ($akun as $data)
                                    <tr>
                                        <th scope="row" class="ps-0 fw-medium">
                                            <span class="table-link1 text-truncate d-block">{{ $data->kode_akun }}</span>
                                        </th>
                                        <td>
                                            <a href="javascript:void(0)"
                                                class="link-primary text-dark fw-medium d-block">{{ $data->akun }}</a>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="link-primary text-dark fw-medium d-block">
                                                Rp
                                                {{ $data->saldo_awal_debit < 0 ? '(' . number_format(abs($data->saldo_awal_debit), 0, ',', '.') . ')' : number_format($data->saldo_awal_debit, 0, ',', '.') }}

                                            </a>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="link-primary text-dark fw-medium d-block">
                                                Rp
                                                {{ $data->saldo_awal_kredit < 0 ? '(' . number_format(abs($data->saldo_awal_kredit), 0, ',', '.') . ')' : number_format($data->saldo_awal_kredit, 0, ',', '.') }}

                                            </a>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                                                data-bs-target="#modalEditAkun" onclick="setEditAkun(this)"
                                                data-id_akun="{{ $data->id_akun }}"
                                                data-id_sub_kategori_akun="{{ $data->id_sub_kategori_akun }}"
                                                data-kode_akun="{{ $data->kode_akun }}" data-akun="{{ $data->akun }}"
                                                data-saldo_awal_debit="{{ $data->saldo_awal_debit }}"
                                                data-saldo_awal_kredit="{{ $data->saldo_awal_kredit }}">
                                                Edit
                                            </button>






                                            <button type="button" class="btn btn-outline-danger m-1"
                                                data-bs-toggle="modal" data-bs-target="#modalHapusAkun"
                                                onclick="setHapusAkun('{{ $data->id_akun }}')">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEditAkun" tabindex="-1" aria-labelledby="modalEditAkunLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <form action="{{ route('akun.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="id_akun" id="edit_id_akun">

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Akun</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="edit_id_sub_kategori_akun" class="form-label">Sub Kategori
                                                    Akun</label>
                                                <select name="id_sub_kategori_akun" id="edit_id_sub_kategori_akun"
                                                    class="form-select" required>
                                                    @foreach ($subkategoriakun as $subkategori)
                                                        <option value="{{ $subkategori->id_sub_kategori_akun }}">
                                                            {{ $subkategori->sub_kategori_akun }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="edit_kode_akun" class="form-label">Kode
                                                    Akun</label>
                                                <input type="text" name="kode_akun" id="edit_kode_akun"
                                                    class="form-control" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="edit_akun" class="form-label">Akun</label>
                                                <input type="text" name="akun" id="edit_akun" class="form-control"
                                                    required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="edit_saldo_awal_debit" class="form-label">Saldo Awal
                                                    Debit</label>
                                                <input type="number" name="saldo_awal_debit" id="edit_saldo_awal_debit"
                                                    class="form-control" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="edit_saldo_awal_kredit" class="form-label">Saldo Awal
                                                    Kredit</label>
                                                <input type="number" name="saldo_awal_kredit"
                                                    id="edit_saldo_awal_kredit" class="form-control" required>
                                            </div>


                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Konfirmasi Hapus -->
                        <div class="modal fade" id="modalHapusAkun" tabindex="-1" aria-labelledby="modalHapusAkunLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('akun.destroy') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id_akun" id="hapus_id_akun">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus akun ini?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>
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
@endsection

@push('scripts')
    <script>
        function setEditAkun(button) {
            const btn = button.dataset;
            console.log("DATA BUTTON:", btn); // debug

            document.getElementById('edit_id_akun').value = btn.id_akun;
            document.getElementById('edit_kode_akun').value = btn.kode_akun;
            document.getElementById('edit_akun').value = btn.akun;
            document.getElementById('edit_saldo_awal_debit').value = btn.saldo_awal_debit;
            document.getElementById('edit_saldo_awal_kredit').value = btn.saldo_awal_kredit;

            const select = document.getElementById('edit_id_sub_kategori_akun');
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value == btn.id_sub_kategori_akun) {
                    select.selectedIndex = i;
                    break;
                }
            }
        }




        function setHapusAkun(id) {
            document.getElementById('hapus_id_akun').value = id;
        }
    </script>
@endpush
