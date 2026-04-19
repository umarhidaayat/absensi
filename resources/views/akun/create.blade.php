@extends('layouts.app')

@section('content')
<div class="w-full lg:max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">➕ Tambah Akun Baru</h2>

        <form action="{{ route('akun.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none" placeholder="Masukkan nama lengkap">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none" placeholder="email@contoh.com">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none" placeholder="Minimal 8 karakter">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Role Akses</label>
                <select name="role" required class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-white">
                    <option value="karyawan">Karyawan (Wajib Ceklok)</option>
                    <option value="admin">Admin (Rekapan & Setting)</option>
                </select>
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('akun.index') }}" class="text-gray-500 hover:text-gray-700 font-bold py-2 px-4">Batal</a>
                <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-lg transition duration-200">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>
@endsection