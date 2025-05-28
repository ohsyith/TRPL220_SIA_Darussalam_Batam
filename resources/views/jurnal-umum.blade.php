@extends('layouts.layout')
@push('styles')
    <title>SIA Yayasan Darussalam | Jurnal Umum</title>
    <style>
        .th-with-dot {
            position: relative;
        }
        .dot-red {
            position: absolute;
            top: -35px;
            left: 28px;
            /* Ubah dari right menjadi left */
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            cursor: pointer;
        }
        .dot-container {
            /* position: relative; */
            display: flex;
            align-items: center;
            gap: 200px;
        }
        .ellipsis-dropdown {
            position: absolute;
            top: -40px;
            left: 1px;

        }
    </style>
@endpush


@section('content')
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

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @php
                            $jurnalBelumDiposting = $detailjurnalumum
                                ->pluck('jurnal_umum.id_jurnal_umum')
                                ->unique()
                                ->diff($postedJurnalIds);
                        @endphp

                        @if ($jurnalBelumDiposting->count() > 0)
                            <form method="POST" action="{{ route('buku-besar.postingSemua') }}"
                                onsubmit="return confirm('Yakin ingin memposting semua jurnal yang belum diposting?')">
                                @csrf
                                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                                <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                                <button type="submit"
                                    class="btn btn-success d-flex align-items-center gap-2 shadow-sm rounded-pill px-4 py-2 mb-3">
                                    <i class="ti ti-send"></i> {{-- Ganti icon sesuai font icon yang kamu pakai (misal Feather, FontAwesome, dll) --}}
                                    <span class="fw-semibold">Posting Semua Jurnal</span>
                                </button>
                            </form>
                        @endif



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
                                                <th scope="row" class="ps-0 fw-medium th-with-dot"
                                                    rowspan="{{ $rowspan }}">

                                                    <div
                                                        class="d-flex justify-content-between align-items-start position-relative">
                                                        @if (!in_array($data->jurnal_umum->id_jurnal_umum, $postedJurnalIds))
                                                            <span class="dot-red"
                                                                data-id="{{ $data->jurnal_umum->id_jurnal_umum }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#postingModal"></span>
                                                        @endif

                                                        <div class="dropdown ellipsis-dropdown ms-auto">
                                                            <button class="btn btn-sm p-0 border-0 bg-transparent"
                                                                type="button"
                                                                id="dropdownMenuButton{{ $data->jurnal_umum->id_jurnal_umum }}"
                                                                data-bs-toggle="dropdown" aria-expanded="false"
                                                                style="font-size: 20px; line-height: 1;">
                                                                &#8942;
                                                            </button>
                                                            <ul class="dropdown-menu"
                                                                aria-labelledby="dropdownMenuButton{{ $data->jurnal_umum->id_jurnal_umum }}">
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                        href="{{ url('/jurnal-umum/' . $data->jurnal_umum->id_jurnal_umum) }}">Edit</a>
                                                                </li>

                                                                <form
                                                                    action="{{ route('jurnal-umum.destroy', $data->jurnal_umum->id_jurnal_umum) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Yakin ingin menghapus?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="dropdown-item text-danger"
                                                                        type="submit">Hapus</button>
                                                                </form>

                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    {{ $data->jurnal_umum->tanggal }}
                                                </th>


                                                <td class="text-center fw-medium" rowspan="{{ $rowspan }}">
                                                    {{ $data->jurnal_umum->no_bukti }}
                                                </td>
                                                <td class="text-center fw-medium" rowspan="{{ $rowspan }}">
                                                    {{ $data->jurnal_umum->keterangan }}
                                                </td>
                                                <td class="text-center fw-medium" rowspan="{{ $rowspan }}">
                                                    {{ $data->jurnal_umum->jenis_transaksi }}
                                                </td>
                                                <td class="text-center fw-medium" rowspan="{{ $rowspan }}">
                                                    {{ $data->jurnal_umum->unit->unit }}
                                                </td>
                                                <td class="text-center fw-medium" rowspan="{{ $rowspan }}">
                                                    {{ $data->jurnal_umum->divisi->divisi }}
                                                </td>
                                                <td class="text-center fw-medium" rowspan="{{ $rowspan }}">
                                                    {{ $data->jurnal_umum->kode_sumbangan }}
                                                </td>
                                                <td class="text-center fw-medium" rowspan="{{ $rowspan }}">
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

                                        </tr>
                                    @endforeach
                                @endforeach

                                <!-- Row Total -->
                                <tr class="fw-bold bg-light">
                                    <td colspan="8" class="text-end">Total</td>
                                    <td class="text-center">Rp {{ number_format($totalDebit) }}</td>
                                    <td class="text-center">Rp {{ number_format($totalKredit) }}</td>
                                </tr>

                            </tbody>

                        </table>

                        <!-- Modal -->
                        <div class="modal fade" id="postingModal" tabindex="-1" aria-labelledby="postingModalLabel"
                            aria-hidden="true">
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
                                        <form id="postingForm" method="POST" action="{{ route('buku-besar.store') }}">
                                            @csrf
                                            <input type="hidden" name="id_jurnal_umum" id="idJurnalUmum">
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Posting</button>
                                            </div>
                                        </form>

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
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.dot-red').forEach(dot => {
                dot.addEventListener('click', function() {
                    const jurnalId = this.getAttribute('data-id');
                    document.getElementById('idJurnalUmum').value = jurnalId;
                });
            });
        });
    </script>
@endpush


