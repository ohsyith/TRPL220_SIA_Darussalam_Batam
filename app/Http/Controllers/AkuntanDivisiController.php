<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Log_Activity;
use Illuminate\Http\Request;
use App\Models\Akuntan_Divisi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AkuntanDivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil semua data divisi untuk dropdown
        $divisi = Divisi::all();

        // Query awal dengan eager load relasi
        $query = Akuntan_Divisi::with(['user', 'divisi']);

        // Filter berdasarkan nama user (jika ada input)
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan divisi (jika ada input)
        if ($request->filled('divisi')) {
            $query->where('id_divisi', $request->divisi);
        }

        // Ambil hasilnya
        $akuntan_divisi = $query->get();

        // Kirim data ke view
        return view('admin.akuntan-divisi', compact('akuntan_divisi', 'divisi'));
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
        // Validasi data input
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:user,username',
            'password' => 'required|string|confirmed|min:6',
            'id_divisi' => 'required|exists:divisi,id_divisi',
            'email' => 'required|email|max:255',
            'telp' => 'required|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            // Simpan ke tabel user
            // Simpan ke tabel user
    $user = User::create([
        'nama' => $request->nama,
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'role' => 'akuntan_divisi', // Set role sebagai "divisi"
    ]);

    // Debugging untuk memastikan id_user sudah ada
    // dd($user->id_user);

    // Simpan ke tabel akuntan_divisi
    Akuntan_Divisi::create([
        'id_akuntan_divisi' => $user->id_user, // id_user dari user
        'id_divisi' => $request->id_divisi,
        'email' => $request->email,
        'telp' => $request->telp,
    ]);


        DB::commit();

        return redirect()->back()->with('success', 'Akuntan Divisi berhasil didaftarkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mendaftarkan Akuntan Divisi: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Akuntan_Divisi $akuntan_divisi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $akuntan_divisi = Akuntan_Divisi::findOrFail($id);
        $divisi = Divisi::all();  // Ambil semua divisi
        return view('admin.akuntan-divisi-detail', compact('akuntan_divisi', 'divisi'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_divisi' => 'required',
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
            'telp' => 'required|string|max:20',
            'username' => 'required|string|max:50',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        $akuntan_divisi = Akuntan_Divisi::findOrFail($id);
        $user = $akuntan_divisi->user;

        // Update divisi dan info kontak
        $akuntan_divisi->update([
            'id_divisi' => $request->id_divisi,
            'email' => $request->email,
            'telp' => $request->telp,
        ]);

        // Update user info
        $user->nama = $request->nama;
        $user->username = $request->username;

        // Jika password baru diisi, validasi password lama dan update
        if ($request->filled('new_password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->with('error', 'Password lama tidak sesuai.');
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->route('akuntan-divisi.edit', $id)->with('success', 'Data akuntan divisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $akuntan_divisi = Akuntan_Divisi::findOrFail($id);
        $user = $akuntan_divisi->user;

        // Hapus data akuntan divisi
        $akuntan_divisi->delete();

        // Hapus user terkait jika memang hanya digunakan oleh akuntan divisi
        if ($user) {
            // Hapus log activity yang terkait
            Log_Activity::where('id_user', $user->id_user)->delete();

            $user->delete();
        }

        return redirect()->route('akuntan-divisi.index')->with('success', 'Data akuntan divisi berhasil dihapus.');
    }


}
