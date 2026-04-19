@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto m-4">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">⏱️ Kelola Shift: {{ $office->name }}</h2>
        <a href="{{ route('setting.index') }}" class="text-blue-600 hover:underline">&larr; Kembali ke Daftar Kantor</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6 col-span-1 h-fit">
            <h3 class="font-bold text-lg mb-4">Tambah Shift Baru</h3>
            <form action="{{ route('setting.shift.store', $office->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Shift</label>
                    <input type="text" name="name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" placeholder="Contoh: Shift Pagi">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Jam Masuk</label>
                    <input type="time" name="start_time" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Jam Pulang</label>
                    <input type="time" name="end_time" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                    Simpan Shift
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 col-span-1 md:col-span-2">
            <h3 class="font-bold text-lg mb-4">Daftar Shift</h3>
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 border-b text-left">Nama Shift</th>
                        <th class="py-2 px-4 border-b text-left">Jam Masuk</th>
                        <th class="py-2 px-4 border-b text-left">Jam Pulang</th>
                        <th class="py-2 px-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $shift)
                    <tr>
                        <td class="py-2 px-4 border-b">{{ $shift->name }}</td>
                        <td class="py-2 px-4 border-b text-green-600 font-semibold">{{ $shift->start_time }}</td>
                        <td class="py-2 px-4 border-b text-red-600 font-semibold">{{ $shift->end_time }}</td>
                        <td class="py-2 px-4 border-b text-center">
                            <form action="{{ route('setting.shift.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('Hapus shift ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs py-1 px-3 rounded">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-500">Belum ada shift. Silakan tambah di samping.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection