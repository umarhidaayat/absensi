@extends('layouts.app')

@section('content')
<div class="w-full lg:max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-8">
        <h2 class="text-2xl font-black text-gray-800 mb-6">✏️ Edit Akun Karyawan</h2>

        <form action="{{ route('akun.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ $user->name }}" required class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-indigo-500 outline-none font-semibold text-gray-700 transition">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" value="{{ $user->email }}" required class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-indigo-500 outline-none font-semibold text-gray-700 transition">
            </div>

            <div class="mb-8">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password Baru <span class="text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah password)</span></label>
                <input type="password" name="password" class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-indigo-500 outline-none font-semibold text-gray-700 transition" placeholder="Minimal 8 karakter">
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-gray-100 mt-6">
                <a href="{{ route('akun.index') }}" class="text-gray-500 hover:text-gray-700 font-bold px-4 py-2">Batal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-200 transition duration-200">
                    Update Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection