@extends('layouts.app')

@section('content')
<div class="w-full">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Akun</h2>
            <p class="text-gray-500 font-medium mt-1">Kelola data akses admin dan karyawan</p>
        </div>
        <a href="{{ route('akun.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition duration-200 shadow-lg shadow-indigo-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Akun
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl mb-6 font-semibold shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-100 border-l-4 border-rose-500 text-rose-700 p-4 rounded-xl mb-6 font-semibold shadow-sm">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/80">
                    <tr class="text-[10px] uppercase font-black text-gray-500 tracking-widest">
                        <th class="p-5 text-left">Nama Pengguna</th>
                        <th class="p-5 text-left">Alamat Email</th>
                        <th class="p-5 text-center">Role Akses</th>
                        <th class="p-5 text-center">Aksi (Khusus Karyawan)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                    <tr class="hover:bg-indigo-50/30 transition">
                        <td class="p-5 font-bold text-gray-800">{{ $user->name }}</td>
                        <td class="p-5 text-gray-600 font-medium">{{ $user->email }}</td>
                        <td class="p-5 text-center">
                            @if($user->role === 'admin')
                                <span class="bg-purple-100 text-purple-700 text-[10px] font-black uppercase px-3 py-1 rounded-lg">Admin</span>
                            @else
                                <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase px-3 py-1 rounded-lg">Karyawan</span>
                            @endif
                        </td>
                        <td class="p-5 text-center">
                            @if($user->role === 'karyawan')
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('akun.edit', $user->id) }}" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition" title="Edit Data">
                                        ✏️
                                    </a>
                                    <form action="{{ route('akun.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun karyawan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-500 hover:text-white transition" title="Hapus Akun">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs font-bold text-gray-400 italic">Protected</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-10 text-center text-gray-400 font-medium">Belum ada data akun terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection