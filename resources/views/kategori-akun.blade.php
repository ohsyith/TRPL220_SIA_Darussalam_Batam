@extends('layouts.layout')
@push('styles')
    <title>SIA Yayasan Darussalam | Kategori Akun</title>

    <style>
        html,
        body,
        .page-wrapper,
        .body-wrapper {
            height: 100%;
            min-height: 100vh;
        }

        .body-wrapper {
            display: flex;
            flex-direction: column;
        }

        .container-fluid {
            flex: 1;
        }

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
                    <h5 class="card-title">Kategori Akun</h5><br>
                    <div class="table-responsive">

                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                            Tambah Kategori Akun
                        </button>



                        <!-- Modal Tambah -->
                        <div class="modal fade" id="modalTambahKategori" tabindex="-1"
                            aria-labelledby="modalTambahKategoriLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">
                                <form method="post" action="{{ route('kategori-akun.store') }}">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tambah Kategori Akun</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="kode_kategori_akun" class="form-label">Kode</label>
                                                <input type="text" name="kode_kategori_akun" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="kategori_akun" class="form-label">
                                                    Kategori Akun</label>
                                                <input type="text" name="kategori_akun" class="form-control" required>
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

                        <table class="table text-nowrap align-middle mb-0">
                            <thead>
                                <tr class="border-2 border-bottom border-primary border-0">
                                    <th scope="col" class="ps-0">KODE</th>
                                    <th scope="col">KATEGORI AKUN</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                @foreach ($kategoriakun as $data)
                                    <tr>
                                        <th scope="row" class="ps-0 fw-medium">
                                            <span
                                                class="table-link1 text-truncate d-block">{{ $data->kode_kategori_akun }}</span>
                                        </th>
                                        <td>
                                            <a href="javascript:void(0)"
                                                class="link-primary text-dark fw-medium d-block">{{ $data->kategori_akun }}</a>
                                        </td>
                                        <td>
                                            <!-- Tombol Edit -->
                                            <button type="button" class="btn btn-outline-warning m-1"
                                                data-bs-toggle="modal" data-bs-target="#modalEditKategori"
                                                onclick="setEditKategori('{{ $data->id_kategori_akun }}', '{{ $data->kode_kategori_akun }}', '{{ $data->kategori_akun }}')">
                                                Edit
                                            </button>



                                            <button type="button" class="btn btn-outline-danger m-1" data-bs-toggle="modal"
                                                data-bs-target="#modalHapusKategori"
                                                onclick="setHapusKategori('{{ $data->id_kategori_akun }}')">
                                                Hapus
                                            </button>

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>


                        <!-- Modal Edit (satu kali saja di luar loop) -->
                        <div class="modal fade" id="modalEditKategori" tabindex="-1"
                            aria-labelledby="modalEditKategoriLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <form action="{{ route('kategori-akun.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="id_kategori_akun" id="edit_id_kategori">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Kategori Akun</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="edit_kode_kategori_akun" class="form-label">Kode Kategori
                                                    Akun</label>
                                                <input type="text" name="kode_kategori_akun" id="edit_kode_kategori_akun"
                                                    class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_kategori_akun" class="form-label">Kategori Akun
                                                    Kategori</label>
                                                <input type="text" name="kategori_akun" id="edit_kategori_akun"
                                                    class="form-control" required>
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
                        <div class="modal fade" id="modalHapusKategori" tabindex="-1"
                            aria-labelledby="modalHapusKategoriLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('kategori-akun.destroy') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id_kategori_akun" id="hapus_id_kategori">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus kategori akun ini?</p>
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
        function setEditKategori(id, kode, kategori) {
            document.getElementById('edit_id_kategori').value = id;
            document.getElementById('edit_kode_kategori_akun').value = kode;
            document.getElementById('edit_kategori_akun').value = kategori;
        }

        function setHapusKategori(id) {
            document.getElementById('hapus_id_kategori').value = id;
        }
    </script>
@endpush
