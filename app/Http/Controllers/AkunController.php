<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;
use App\Models\Sub_Kategori_Akun;
use Illuminate\Support\Facades\DB;

class AkunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subkategoriakun = Sub_Kategori_Akun::all();
        $akun = Akun::all();
        return view('akun', compact('subkategoriakun','akun'));
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

        // dd($request->all());

        // Validasi data input
        $request->validate([
            'id_sub_kategori_akun' => 'required|integer|exists:sub_kategori_akun,id_sub_kategori_akun',
            'kode_akun' => 'required|string|max:255|unique:akun,kode_akun',
            'akun' => 'required|string|max:255',
            'saldo_awal_debit' => 'required|integer',
            'saldo_awal_kredit' => 'required|integer',
            'budget_rapbs' => 'required|integer',
        ]);

        DB::beginTransaction();

        try {
            Akun::create([
                'id_sub_kategori_akun' => $request->id_sub_kategori_akun,
                'kode_akun' => $request->kode_akun,
                'akun' => $request->akun,
                'saldo_awal_debit' => $request->saldo_awal_debit,
                'saldo_awal_kredit' => $request->saldo_awal_kredit,
                'budget_rapbs' => $request->budget_rapbs,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Akun berhasil didaftarkan.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambah akun: ' . $e->getMessage());
        }
    }
 

    /**
     * Display the specified resource.
     */
    public function show(Akun $akun)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Akun $akun)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // dd($request->all());

        // Validasi data input
        $request->validate([
            'id_sub_kategori_akun' => 'required|exists:sub_kategori_akun,id_sub_kategori_akun', // Validasi kategori akun
            'kode_akun' => 'required|string|max:255|unique:akun,kode_akun,' . $request->id_akun . ',id_akun',
            'akun' => 'required|string|max:255|unique:akun,akun,' . $request->id_akun . ',id_akun',
            'saldo_awal_debit' => 'required|integer',
            'saldo_awal_kredit' => 'required|integer',
            'budget_rapbs' => 'required|integer',
        ]);

        DB::beginTransaction();

        try {
            // Temukan data sub kategori akun berdasarkan ID yang dikirim
            $subKategoriAkun = Akun::findOrFail($request->id_akun);
            
            // Update data sub kategori akun
            $subKategoriAkun->update([
                'id_sub_kategori_akun' => $request->id_sub_kategori_akun,
                'kode_akun' => $request->kode_akun,
                'akun' => $request->akun,
                'saldo_awal_debit' => $request->saldo_awal_debit,
                'saldo_awal_kredit' => $request->saldo_awal_kredit,
                'budget_rapbs' => $request->budget_rapbs,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Akun berhasil diperbarui.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui akun: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $akun = Akun::findOrFail($request->id_akun);
            $akun->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Akun berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }
}
