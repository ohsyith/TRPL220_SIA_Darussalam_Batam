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
                    <h5 class="card-title">Budget RAPBS Akun</h5><br>

                    {{-- Alert Sukses --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif

                    {{-- Alert Gagal --}}
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <a href="{{ asset('assets/templates/Template_Rapbs_Akun.xlsx') }}"
                            class="btn btn-link text-primary p-0" download>
                            <i class="fas fa-download me-1"></i> Download Template Import RAPBS Akun
                        </a>
                    </div>

                    {{-- Baris Import + Reset --}}
                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">

                        {{-- Form Import --}}
                        <form action="{{ route('budget-rapbs-akun.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="file" class="form-control" required>
                                <button class="btn btn-success" type="submit">
                                    <i class="fas fa-upload me-1"></i> Import Excel
                                </button>
                            </div>
                        </form>


                    </div>





                    <div class="table-responsive">

                        <form method="GET" action="{{ route('budget-rapbs-akun.index') }}" class="mb-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <label for="unit" class="form-label mb-0">Pilih Unit:</label>
                                </div>

                                <div class="col-auto">
                                    @if (in_array($user->role, ['admin', 'auditor']))
                                        <select name="unit" id="unit" class="form-select"
                                            onchange="this.form.submit()">
                                            <option value="all" {{ ($id_unit ?? 'all') === 'all' ? 'selected' : '' }}>
                                                Akumulasi (Semua Unit)
                                            </option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id_unit }}"
                                                    {{ ($id_unit ?? '') == $unit->id_unit ? 'selected' : '' }}>
                                                    {{ $unit->kode_unit }} - {{ $unit->unit }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif ($user->role === 'akuntan_unit')
                                        {{-- Tampilkan nama unit tanpa dropdown --}}
                                        @php
                                            $nama_unit = \App\Models\Unit::find($id_unit);
                                        @endphp
                                        <input type="text" class="form-control-plaintext"
                                            value="{{ $nama_unit->kode_unit }} - {{ $nama_unit->unit }}" readonly>
                                        <input type="hidden" name="unit" value="{{ $id_unit }}">
                                    @endif
                                </div>
                            </div>
                        </form>




                        {{-- Data Akun --}}
                        <table class="table text-nowrap align-middle mb-0">
                            <thead>
                                <tr class="border-2 border-bottom border-primary border-0">
                                    {{-- <th scope="col">SUB KATEGORI AKUN</th> --}}
                                    <th scope="col" class="ps-0">KODE</th>
                                    <th scope="col">AKUN</th>
                                    <th scope="col">Budget RAPBS</th>
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
                                                Rp {{ number_format($data->budget_rapbs ?? 0, 0, ',', '.') }}
                                            </a>
                                        </td>
                                        <td>
                                            @if ($id_unit !== 'all')
                                                <button type="button" class="btn btn-outline-warning"
                                                    data-bs-toggle="modal" data-bs-target="#modalEditAkun"
                                                    data-id_akun="{{ $data->id_akun }}"
                                                    data-id_sub="{{ $data->id_sub_kategori_akun }}"
                                                    data-kode="{{ $data->kode_akun }}" data-akun="{{ $data->akun }}"
                                                    data-budget="{{ floatval($data->budget_rapbs ?? 0) }}"
                                                    data-id_unit="{{ $id_unit }}" data-unit="{{ $nama_unit }}"
                                                    onclick="openModalEdit(this)">
                                                    Edit
                                                </button>
                                            @endif

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>


                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEditAkun" tabindex="-1" aria-labelledby="modalEditAkunLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <form action="{{ route('budget-rapbs-akun.storeOrUpdate') }}" method="POST">
                                    @csrf

                                    <input type="hidden" name="id_akun" id="edit_id_akun">
                                    <input type="hidden" name="id_unit" value="{{ $id_unit }}">

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit RAPBS Akun</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            {{-- Hidden Field --}}
                                            <input type="hidden" name="id_unit" id="edit_id_unit">


                                            {{-- Kode Akun --}}
                                            <div class="mb-3">
                                                <label for="edit_kode_akun" class="form-label">Kode Akun</label>
                                                <input type="text" id="edit_kode_akun" class="form-control bg-light"
                                                    readonly>
                                            </div>

                                            {{-- Nama Akun --}}
                                            <div class="mb-3">
                                                <label for="edit_akun" class="form-label">Nama Akun</label>
                                                <input type="text" id="edit_akun" class="form-control bg-light"
                                                    readonly>
                                            </div>

                                            {{-- Nama Unit --}}
                                            <div class="mb-3">
                                                <label for="edit_nama_unit" class="form-label">Unit</label>
                                                <input type="text" id="edit_nama_unit" class="form-control bg-light"
                                                    readonly>
                                            </div>

                                            {{-- Budget --}}
                                            <div class="mb-3">
                                                <label for="edit_budget_rapbs_akun" class="form-label">Budget
                                                    RAPBS</label>
                                                <input type="number" name="budget_rapbs_akun"
                                                    id="edit_budget_rapbs_akun" class="form-control" required>
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
        function openModalEdit(btn) {
            document.getElementById('edit_id_akun').value = btn.dataset.id_akun;
            document.getElementById('edit_id_unit').value = btn.dataset.id_unit;

            document.getElementById('edit_kode_akun').value = btn.dataset.kode;
            document.getElementById('edit_akun').value = btn.dataset.akun;
            document.getElementById('edit_budget_rapbs_akun').value = btn.dataset.budget;
            document.getElementById('edit_nama_unit').value = btn.dataset.unit;
        }
    </script>
@endpush
