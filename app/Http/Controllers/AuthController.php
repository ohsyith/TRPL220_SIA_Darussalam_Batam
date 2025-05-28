<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Tampilkan form login
    public function login_form()
    {
        return view('login'); // Sesuaikan view login kamu, misal di resources/views/auth/login.blade.php
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba login
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->remember)) {
            $request->session()->regenerate();

            // Arahkan berdasarkan role
            $role = Auth::user()->role;
            if ($role == 'admin') {
                return redirect()->intended('/admin');
            } elseif ($role == 'akuntan_unit') {
                return redirect()->intended('/');
            } elseif ($role == 'akuntan_divisi') {
                return redirect()->intended('/');
            } elseif ($role == 'auditor') {
                return redirect()->intended('/');
            } else {
                Auth::logout();
                return redirect()->route('login')->withErrors('Role tidak dikenali.');
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
