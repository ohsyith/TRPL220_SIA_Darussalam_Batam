<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Unit;
use App\Models\User;
use App\Models\Hak_Akses;
use App\Models\Akuntan_Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkuntanUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil semua data unit untuk dropdown
        $unit = Unit::all();

        // Query awal dengan eager load relasi
        $query = Akuntan_Unit::with(['user', 'unit']);

        // Filter berdasarkan nama user (jika ada input)
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan unit (jika ada input)
        if ($request->filled('unit')) {
            $query->where('id_unit', $request->unit);
        }

        // Ambil hasilnya
        $akuntan_unit = $query->get();

        // Kirim data ke view
        return view('admin.akuntan-unit', compact('akuntan_unit', 'unit'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validasi input
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|unique:user,username|max:255',
                'password' => 'required|string|min:8|confirmed',
                'id_unit' => 'required|exists:unit,id_unit',
                'email' => 'required|email',
                'telp' => 'required|string',
            ]);

            // Buat user baru
            $user = User::create([
                'nama' => $request->nama,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'role' => 'akuntan_unit',
            ]);

            // Simpan akuntan_unit
            $akuntanUnit = Akuntan_Unit::create([
                'id_akuntan_unit' => $user->id_user,
                'id_unit' => $request->id_unit,
                'email' => $request->email,
                'telp' => $request->telp,
            ]);

            // Buat hak akses
            Hak_Akses::create([
                'id_akuntan_unit' => $user->id_user,
                'view_jurnal_umum' => $request->input('view_jurnal_umum', 0),
                'create_jurnal_umum' => $request->input('create_jurnal_umum', 0),
                'update_jurnal_umum' => $request->input('update_jurnal_umum', 0),
                'delete_jurnal_umum' => $request->input('delete_jurnal_umum', 0),
                'view_buku_besar' => $request->input('view_buku_besar', 0),
                'create_buku_besar' => $request->input('create_buku_besar', 0),
                'delete_buku_besar' => $request->input('delete_buku_besar', 0),
                'view_laporan_neraca' => $request->input('view_laporan_neraca', 0),
                'view_laporan_komprehensif' => $request->input('view_laporan_komprehensif', 0),
                'view_laporan_posisi_keuangan' => $request->input('view_laporan_posisi_keuangan', 0),
                'view_laporan_arus_kas' => $request->input('view_laporan_arus_kas', 0),
                'view_laporan_perubahan_aset_neto' => $request->input('view_laporan_perubahan_aset_neto', 0),
                'view_laporan_catatan_atas_laporan_keuangan' => $request->input('view_laporan_catatan_atas_laporan_keuangan', 0),
                'view_laporan_proyeksi_rencana_dan_realisasi_anggaran' => $request->input('view_laporan_proyeksi_rencana_dan_realisasi_anggaran', 0),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Akuntan Unit berhasil didaftarkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(akuntan_unit $akuntan_unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $akuntan_unit = Akuntan_Unit::findOrFail($id);
        $user = User::findOrFail($id);
        $unit = Unit::all();  // Ambil semua unit
        $akses = Hak_Akses::where('id_akuntan_unit', $id)->first();
        // dd($akses->create_jurnal_umum );
        return view('admin.akuntan-unit-detail', compact('akuntan_unit', 'unit', 'user', 'akses'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Validasi input
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:user,username,' . $id . ',id_user',
                'password' => 'nullable|string|min:8|confirmed',
                'id_unit' => 'required|exists:unit,id_unit',
                'email' => 'required|email',
                'telp' => 'required|string',
            ]);

            // Cari user dan akuntan
            $user = User::findOrFail($id);
            $akuntanUnit = Akuntan_Unit::where('id_akuntan_unit', $id)->firstOrFail();
            $hakAkses = Hak_Akses::where('id_akuntan_unit', $id)->firstOrFail();

            // Update user
            $user->update([
                'nama' => $request->nama,
                'username' => $request->username,
                // Hanya update password jika diisi
                'password' => $request->filled('password') ? bcrypt($request->password) : $user->password,
            ]);

            // Update akuntan_unit
            $akuntanUnit->update([
                'id_unit' => $request->id_unit,
                'email' => $request->email,
                'telp' => $request->telp,
            ]);

            // Update hak akses
            $hakAkses->update([
                'view_jurnal_umum' => $request->input('view_jurnal_umum', 0),
                'create_jurnal_umum' => $request->input('create_jurnal_umum', 0),
                'update_jurnal_umum' => $request->input('update_jurnal_umum', 0),
                'delete_jurnal_umum' => $request->input('delete_jurnal_umum', 0),
                'view_buku_besar' => $request->input('view_buku_besar', 0),
                'create_buku_besar' => $request->input('create_buku_besar', 0),
                'delete_buku_besar' => $request->input('delete_buku_besar', 0),
                'view_laporan_neraca' => $request->input('view_laporan_neraca', 0),
                'view_laporan_komprehensif' => $request->input('view_laporan_komprehensif', 0),
                'view_laporan_posisi_keuangan' => $request->input('view_laporan_posisi_keuangan', 0),
                'view_laporan_arus_kas' => $request->input('view_laporan_arus_kas', 0),
                'view_laporan_perubahan_aset_neto' => $request->input('view_laporan_perubahan_aset_neto', 0),
                'view_laporan_catatan_atas_laporan_keuangan' => $request->input('view_laporan_catatan_atas_laporan_keuangan', 0),
                'view_laporan_proyeksi_rencana_dan_realisasi_anggaran' => $request->input('view_laporan_proyeksi_rencana_dan_realisasi_anggaran', 0),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data Akuntan Unit berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Hapus hak akses terlebih dahulu (foreign key constraint)
            Hak_Akses::where('id_akuntan_unit', $id)->delete();

            // Hapus data akuntan unit
            Akuntan_Unit::where('id_akuntan_unit', $id)->delete();

            // Hapus user
            User::where('id_user', $id)->delete();

            DB::commit();
            return redirect()->route('akuntan-unit.index')->with('success', 'Data Akuntan Unit berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

}
