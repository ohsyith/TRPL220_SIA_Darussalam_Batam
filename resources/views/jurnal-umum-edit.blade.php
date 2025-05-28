@extends('layouts.layout')
@push('styles')
    <title>SIA Yayasan Darussalam | Edit Transaksi</title>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Transaksi</h5>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="/jurnal-umum/{{ $jurnalUmum->id_jurnal_umum }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal"
                                value="{{ $jurnalUmum->tanggal }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" name="keterangan" id="keterangan"
                                value="{{ $jurnalUmum->keterangan }}" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Transaksi</label>
                                <select name="jenis_transaksi" class="form-select" required>
                                    <option value="Terikat"
                                        {{ $jurnalUmum->jenis_transaksi == 'Terikat' ? 'selected' : '' }}>
                                        Terikat</option>
                                    <option value="Tidak Terikat"
                                        {{ $jurnalUmum->jenis_transaksi == 'Tidak Terikat' ? 'selected' : '' }}>
                                        Tidak Terikat</option>
                                </select>
                            </div>

                            @if (Auth::user()->role === 'akuntan_unit')
                                <input type="hidden" name="id_unit" value="{{ $id_unit }}">
                            @else
                                <div class="col-md-3">
                                    <label class="form-label">Unit</label>
                                    <select name="id_unit" class="form-select" required>
                                        <option value="">Pilih Unit</option>
                                        @foreach ($unit as $data_unit)
                                            <option value="{{ $data_unit->id_unit }}"
                                                {{ $data_unit->id_unit == $jurnalUmum->id_unit ? 'selected' : '' }}>
                                                {{ $data_unit->kode_unit }} - {{ $data_unit->unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if (Auth::user()->role === 'akuntan_divisi')
                                <input type="hidden" name="id_divisi" value="{{ $id_divisi }}">
                            @else
                                <div class="col-md-3">
                                    <label class="form-label">Divisi</label>
                                    <select name="id_divisi" class="form-select" required>
                                        <option value="">Pilih Divisi</option>
                                        @foreach ($divisi as $data_divisi)
                                            <option value="{{ $data_divisi->id_divisi }}"
                                                {{ $data_divisi->id_divisi == $jurnalUmum->id_divisi ? 'selected' : '' }}>
                                                {{ $data_divisi->divisi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        <hr class="border border-3 border-dark">

                        <div id="akunContainer">
                            @foreach ($jurnalUmum->detail_jurnal_umum as $detail)
                                <div class="row mb-3 akun-row">
                                    <div class="col-md-6">
                                        <label class="form-label">Akun</label>
                                        <select name="id_akun[]" class="form-select" required>
                                            <option value="">Pilih Akun</option>
                                            @foreach ($akun as $data_akun)
                                                <option value="{{ $data_akun->id_akun }}"
                                                    {{ $data_akun->id_akun == $detail->id_akun ? 'selected' : '' }}>
                                                    {{ $data_akun->kode_akun }} - {{ $data_akun->akun }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Debit</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control format-rupiah" name="debit[]"
                                                value="{{ $detail->debit_kredit === 'debit' ? number_format($detail->nominal, 0, ',', '.') : '' }}"
                                                oninput="formatRupiah(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Kredit</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control format-rupiah" name="kredit[]"
                                                value="{{ $detail->debit_kredit === 'kredit' ? number_format($detail->nominal, 0, ',', '.') : '' }}"
                                                oninput="formatRupiah(this)">
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-success mb-3" onclick="tambahAkun()">Tambah Akun</button>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="postingBukuBesar" name="postingBukuBesar"
                                {{ $jurnalUmum->buku_besar ? 'checked' : '' }}>

                            <label class="form-check-label" for="postingBukuBesar">Posting ke Buku
                                Besar</label>
                        </div>

                        <button type="submit" class="btn btn-primary col-12">Simpan</button>
                    </form>

                    <script>
                        function tambahAkun() {
                            let container = document.getElementById('akunContainer');
                            let newRow = document.createElement('div');
                            newRow.classList.add('row', 'mb-3', 'akun-row');
                            newRow.innerHTML = `
                                            <div class="col-md-6">
                                                <label class="form-label">Akun</label>
                                                <select name="id_akun[]" class="form-select" required>
                                                    <option value="">Pilih Akun</option>
                                                    @foreach ($akun as $data_akun)
                                                        <option value="{{ $data_akun->id_akun }}">{{ $data_akun->kode_akun }} - {{ $data_akun->akun }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Debit</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control format-rupiah" name="debit[]" oninput="formatRupiah(this)">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Kredit</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control format-rupiah" name="kredit[]" oninput="formatRupiah(this)">
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-end">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="hapusAkun(this)">Hapus</button>
                                            </div>
                                        `;
                            container.appendChild(newRow);
                        }

                        function hapusAkun(button) {
                            let row = button.closest('.akun-row');
                            row.remove();
                        }

                        function formatRupiah(input) {
                            let value = input.value.replace(/\D/g, '');
                            input.value = new Intl.NumberFormat('id-ID').format(value);
                        }
                    </script>
                </div>
            </div>

        </div>
    </div>
    <div class="py-6 px-6 text-center">
        <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>
    </div>
@endsection
