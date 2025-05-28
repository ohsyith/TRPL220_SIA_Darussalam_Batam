<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Auditor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuditorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        // Query awal dengan eager load relasi
        $query = Auditor::with(['user']);

        // Filter berdasarkan nama user (jika ada input)
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        // Ambil hasilnya
        $auditor = $query->get();

        // Kirim data ke view
        return view('admin.auditor', compact('auditor'));
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
        'role' => 'auditor', // Set role sebagai "divisi"
    ]);

    // Debugging untuk memastikan id_user sudah ada
    // dd($user->id_user);

    // Simpan ke tabel auditor
    Auditor::create([
        'id_auditor' => $user->id_user, // id_user dari user
        'email' => $request->email,
        'telp' => $request->telp,
    ]);


        DB::commit();

        return redirect()->back()->with('success', 'Auditor berhasil didaftarkan.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mendaftarkan Auditor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Auditor $auditor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $auditor = Auditor::findOrFail($id);
        return view('admin.auditor-detail', compact('auditor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
            'telp' => 'required|string|max:20',
            'username' => 'required|string|max:50',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        $auditor = Auditor::findOrFail($id);
        $user = $auditor->user;

        // Update auditor dan info kontak
        $auditor->update([
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

        return redirect()->route('auditor.edit', $id)->with('success', 'Data auditor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $auditor = Auditor::findOrFail($id);
        $user = $auditor->user;

        // Hapus data akuntan divisi
        $auditor->delete();

        // Hapus user terkait jika memang hanya digunakan oleh akuntan divisi
        if ($user) {
            $user->delete();
        }

        return redirect()->route('auditor.index')->with('success', 'Data auditor berhasil dihapus.');
    }
}
