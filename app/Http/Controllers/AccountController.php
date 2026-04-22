<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role', 'asc')->orderBy('name', 'asc')->get();
        
        return view('akun.index', compact('users'));
    }

    public function create()
    {
        return view('akun.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,karyawan', 
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'role' => $request->role,
        ]);

        return redirect()->route('akun.index')->with('success', 'Akun berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return redirect()->route('akun.index')->with('error', 'Akses ditolak! Akun Admin tidak dapat diedit dari sini.');
        }

        return view('akun.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = $request->password; 
        }

        $user->save();

        return redirect()->route('akun.index')->with('success', 'Data akun karyawan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return redirect()->route('akun.index')->with('error', 'Gagal! Akun Admin tidak boleh dihapus.');
        }

        $user->delete();

        return redirect()->route('akun.index')->with('success', 'Akun Karyawan berhasil dihapus secara permanen!');
    }
}