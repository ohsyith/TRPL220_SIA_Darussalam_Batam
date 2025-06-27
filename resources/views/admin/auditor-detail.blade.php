@extends('layouts.layout')
@push('styles')
    <title>SIA Yayasan Darussalam | Auditor</title>
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
    <div class="card">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">Detail Pengguna</h5>

            <div class="card">
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

                    <form method="POST" {{-- action="{{ route('akuntan-divisi.update', $auditor->id_akuntan_divisi) }}"> --}} action="">
                        @csrf
                        @method('PUT')

                        {{-- PROFIL --}}
                        <div class="mb-3">
                            <label class="form-label"><strong>Profil</strong></label>
                        </div>


                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" value="{{ $auditor->user->nama }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ $auditor->email }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Telp</label>
                                <input type="text" class="form-control" name="telp" value="{{ $auditor->telp }}"
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
                                    value="{{ $auditor->user->username }}" required>
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

                        <span onclick="document.getElementById('form-delete-akuntan').submit();"
                            style="color:red; cursor:pointer; text-decoration:underline;">
                            Hapus Pengguna
                        </span>
                        <br>
                        <br>

                        <button type="submit" class="btn btn-primary col-12">Update</button>
                    </form>



                    <form id="form-delete-akuntan" action="{{ route('auditor.destroy', $auditor->id_auditor) }}"
                        method="POST" style="display:none;">
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
