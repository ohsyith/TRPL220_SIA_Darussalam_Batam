@extends('layouts.layout')
@push('styles')
    <title>SIA Yayasan Darussalam | Input Transaksi</title>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Input Transaksi</h5>
            <div class="card">
                <div class="card-body">
                    <form id="form-jurnal" method="post" action="/jurnal-umum">
                        @csrf
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" required>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" name="keterangan" id="keterangan" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Transaksi</label>
                                <select name="jenis_transaksi" class="form-select" required>
                                    <option value="" disabled selected>Pilih Jenis Transaksi</option>
                                    <option value="Terikat">Terikat</option>
                                    <option value="Tidak Terikat">Tidak Terikat</option>
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
                                            <option value="{{ $data_unit->id_unit }}">
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
                                    <label class="form-label">divisi</label>
                                    <select name="id_divisi" class="form-select" required>
                                        <option value="">Pilih divisi</option>
                                        @foreach ($divisi as $data_divisi)
                                            <option value="{{ $data_divisi->id_divisi }}">
                                                {{ $data_divisi->divisi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif



                            {{-- <div class="col-md-3">
                                            <label class="form-label">Divisi</label>
                                            <select name="id_divisi" class="form-select" required>
                                                <option value="">Pilih Divisi</option>
                                                @foreach ($divisi as $data_divisi)
                                                    <option value="{{ $data_divisi->id_divisi }}">
                                                        {{ $data_divisi->divisi }}</option>
                                                @endforeach
                                            </select>
                                        </div> --}}
                        </div>

                        {{-- <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Kode Sumbangan</label>
                                            <input type="text" class="form-control" name="kode_sumbangan"
                                                id="kode_sumbangan">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Kode P&H</label>
                                            <input type="text" class="form-control" name="kode_ph"
                                                id="kode_ph">
                                        </div>
                                    </div> --}}

                        <br>
                        <hr class="border border-3 border-dark">
                        <br>




                        <div id="akunContainer">
                            <!-- Akun Default 1 -->
                            <div class="row mb-3 akun-row">
                                <div class="col-md-6 akun-group">
                                    <label class="form-label">Akun</label>
                                    <input list="akun-list" class="form-control akun-search" placeholder="Pilih Akun"
                                        autocomplete="off" required>
                                    <input type="hidden" name="id_akun[]" class="akun-id">

                                    <datalist id="akun-list">
                                        @foreach ($akun as $data_akun)
                                            <option data-id="{{ $data_akun->id_akun }}"
                                                value="{{ $data_akun->kode_akun }} - {{ $data_akun->akun }}">
                                            </option>
                                        @endforeach
                                    </datalist>
                                </div>





                                <div class="col-md-3">
                                    <label class="form-label">Debit</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control format-rupiah" name="debit[]"
                                            oninput="formatRupiah(this)">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kredit</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control format-rupiah" name="kredit[]"
                                            oninput="formatRupiah(this)">
                                    </div>
                                </div>
                            </div>


                            <div class="row mb-3 akun-row">
                                <div class="col-md-6 akun-group">
                                    <label class="form-label">Akun</label>
                                    <input list="akun-list" class="form-control akun-search" placeholder="Pilih Akun"
                                        autocomplete="off" required>
                                    <input type="hidden" name="id_akun[]" class="akun-id">

                                    <datalist id="akun-list">
                                        @foreach ($akun as $data_akun)
                                            <option data-id="{{ $data_akun->id_akun }}"
                                                value="{{ $data_akun->kode_akun }} - {{ $data_akun->akun }}">
                                            </option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Debit</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control format-rupiah" name="debit[]"
                                            oninput="formatRupiah(this)">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kredit</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control format-rupiah" name="kredit[]"
                                            oninput="formatRupiah(this)">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="warningSaldo" class="text-danger mb-2 fw-bold" style="display: none;">
                        </div>



                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-success mb-3" onclick="tambahAkun()">Tambah
                                Akun</button>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="postingBukuBesar"
                                name="postingBukuBesar">
                            <label class="form-check-label" for="postingBukuBesar">Posting ke Buku
                                Besar</label>
                        </div>

                        <button type="submit" class="btn btn-primary col-12">Submit</button>
                    </form>



                </div>
            </div>

        </div>
    </div>
    <div class="py-6 px-6 text-center">
        <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('akun-search')) {
                const input = e.target;
                const val = input.value;
                const options = document.getElementById('akun-list').options;

                let foundId = '';
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === val) {
                        foundId = options[i].getAttribute('data-id');
                        break;
                    }
                }

                // Cari input hidden dalam div yang sama
                const parentDiv = input.closest('.akun-group');
                if (parentDiv) {
                    const hiddenInput = parentDiv.querySelector('.akun-id');
                    hiddenInput.value = foundId;
                }
            }
        });
    </script>

    <script>
        let isSaldoValid = false;

        function formatRupiah(input) {
            let value = input.value.replace(/\D/g, '');
            input.value = new Intl.NumberFormat('id-ID').format(value);
            cekSaldo(); // Langsung cek setelah format
        }

        function cekSaldo() {
            let totalDebit = 0;
            let totalKredit = 0;

            document.querySelectorAll('input[name="debit[]"]').forEach(function(input) {
                let val = parseInt(input.value.replace(/\D/g, '')) || 0;
                totalDebit += val;
            });

            document.querySelectorAll('input[name="kredit[]"]').forEach(function(input) {
                let val = parseInt(input.value.replace(/\D/g, '')) || 0;
                totalKredit += val;
            });

            const warning = document.getElementById('warningSaldo');
            if (totalDebit !== totalKredit) {
                isSaldoValid = false;
                warning.style.display = 'block';
                warning.textContent =
                    `Total Debit (Rp${new Intl.NumberFormat('id-ID').format(totalDebit)}) dan Kredit (Rp${new Intl.NumberFormat('id-ID').format(totalKredit)}) tidak seimbang.`;
            } else {
                isSaldoValid = true;
                warning.style.display = 'none';
                warning.textContent = '';
            }
        }

        // Tambahkan listener saat pertama kali halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[name="debit[]"], input[name="kredit[]"]').forEach(function(input) {
                input.addEventListener('input', cekSaldo);
            });

            // Cegah submit jika saldo tidak seimbang
            document.getElementById('form-jurnal').addEventListener('submit', function(e) {
                cekSaldo(); // pastikan dicek ulang saat submit
                if (!isSaldoValid) {
                    e.preventDefault();
                    document.getElementById('warningSaldo').scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Fungsi tambah akun
        function tambahAkun() {
            let container = document.getElementById('akunContainer');
            let newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-3', 'akun-row');
            newRow.innerHTML = `
                                            <div class="col-md-6 akun-group">
                                                <label class="form-label">Akun</label>
                                                <input list="akun-list" class="form-control akun-search" placeholder="Pilih Akun" autocomplete="off" required>
                                                <input type="hidden" name="id_akun[]" class="akun-id">
                                                <datalist id="akun-list">
                                                    @foreach ($akun as $data_akun)
                                                        <option data-id="{{ $data_akun->id_akun }}" value="{{ $data_akun->kode_akun }} - {{ $data_akun->akun }}"></option>
                                                    @endforeach
                                                </datalist>
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

            // Tambahkan event listener ke input baru
            newRow.querySelectorAll('input[name="debit[]"], input[name="kredit[]"]').forEach(function(input) {
                input.addEventListener('input', cekSaldo);
            });
        }

        function hapusAkun(button) {
            let row = button.closest('.akun-row');
            row.remove();
            cekSaldo(); // Recheck saldo setelah hapus
        }
    </script>
@endpush


