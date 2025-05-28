@extends('layouts.layout')

@push('styles')
    <title>SIA Yayasan Darussalam | Dashboard</title>

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
@endpush


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Selamat datang di Dashboard, {{ $user->nama }}</h5>
                    <p class="card-text">Ini adalah halaman dashboard utama Anda.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6 px-6 text-center">
        <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>
    </div>
@endsection
