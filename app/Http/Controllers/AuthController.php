<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman form login
    public function showLoginForm()
    {
        // Jika user sudah login, cek role-nya lalu arahkan ke halaman yang sesuai
        if (Auth::check()) {
            if (auth()->user()->role === 'admin') {
                return redirect()->route('rekapan');
            }
            return redirect()->route('ceklok');
        }
        
        return view('auth.login');
    }

    // Memproses data login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Coba melakukan pencocokan data (authentication)
        if (Auth::attempt($credentials)) {
            // Jika berhasil, buat sesi baru untuk keamanan
            $request->session()->regenerate();

            // Cek Role untuk menentukan halaman tujuan setelah login
            if (auth()->user()->role === 'admin') {
                return redirect()->route('rekapan')->with('success', 'Selamat datang, Admin!');
            } else {
                return redirect()->route('ceklok')->with('success', 'Selamat datang kembali, jangan lupa absen!');
            }
        }

        // Jika gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}