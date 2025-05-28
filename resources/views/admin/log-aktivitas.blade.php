@extends('layouts.layout')
@push('styles')
    <title>SIA Yayasan Darussalam | Log Aktivitas</title>


    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            /* background-color: #f0f0f0; */
            /* warna abu-abu */
        }

        .page-wrapper {
            min-height: 100vh;
            /* background-color: #f0f0f0; */
            /* warna abu-abu */
            display: flex;
            flex-direction: column;
        }

        .body-wrapper {
            flex: 1;
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
                    <h5 class="card-title mb-4">Log Aktivitas</h5>

                    <form action="" method="GET" class="mb-4">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">

                                <input type="text" name="search" class="form-control" placeholder="Cari..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label>Tanggal </label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>

                            <div class="col-md-2">
                                <label></label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label>Jam </label>
                                <input type="time" name="start_time" class="form-control"
                                    value="{{ request('start_time') }}">
                            </div>
                            <div class="col-md-2">
                                <label></label>
                                <input type="time" name="end_time" class="form-control"
                                    value="{{ request('end_time') }}">
                            </div>
                            <div class="col-md-10">
                            </div>
                            <div class="col-md-1 d-grid">
                                <button type="button" class="btn btn-secondary"
                                    onclick="window.location.href = '{{ url()->current() }}'">
                                    <i class="ti ti-refresh fs-4"></i>
                                </button>
                            </div>
                            <div class="col-md-1 d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search fs-4"></i>
                                </button>
                            </div>
                        </div>
                    </form>




                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Waktu</th>
                                <th>Username</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($log_aktivitas as $data)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y H:i') }}
                                    </td>
                                    <td>{{ $data->user->username }}</td>
                                    <td>{{ $data->keterangan }}</td>
                                </tr>
                            @endforeach


                        </tbody>
                    </table>


                </div>
            </div>
        </div>
        <div class="py-6 px-6 text-center">
            <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>

        </div>
    </div>
@endsection

