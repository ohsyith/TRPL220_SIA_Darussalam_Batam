@extends('layouts.layout')
@push('styles')
    <title>SIA Yayasan Darussalam | Akuntan Unit</title>


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
    </style>

    <style>
        .card-hover:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: scale(1.01);
            transition: all 0.2s ease-in-out;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Akuntan Unit</h5>

                    <form action="" method="GET" class="mb-4">
                        <div class="row g-2">
                            <div class="col-md-7">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama akuntan..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="unit" class="form-select">
                                    <option value="">-- Semua Unit --</option>
                                    @foreach ($unit as $data)
                                        <option value="{{ $data->id_unit }}">
                                            {{ $data->kode_unit }} | {{ $data->unit }}
                                        </option>
                                    @endforeach
                                    <!-- Tambahkan unit lainnya sesuai kebutuhan -->
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    @foreach ($akuntan_unit as $data)
                        <a href="{{ route('akuntan-unit.edit', ['id' => $data->id_akuntan_unit]) }}"
                            class="text-decoration-none">
                            <div class="card mb-3 border rounded-3 p-3 card-hover" style="cursor: pointer;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-dark fs-5">
                                        <strong>{{ $data->user->nama }}</strong>
                                    </div>
                                    <div class="text-muted fs-4">
                                        <strong>{{ $data->unit->kode_unit }} |
                                            {{ $data->unit->unit }}</strong>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach

                </div>
            </div>
        </div>

        <div class="py-6 px-6 text-center">
            <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>
        </div>
    </div>
@endsection

