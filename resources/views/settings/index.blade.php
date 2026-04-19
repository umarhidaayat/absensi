@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md overflow-hidden m-4">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">🏢 Daftar Kantor & Cabang</h2>
            <a href="{{ route('setting.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                + Tambah Kantor
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 border-b text-left text-gray-600 font-semibold">Nama Kantor</th>
                        <th class="py-2 px-4 border-b text-left text-gray-600 font-semibold">Koordinat (Lat, Long)</th>
                        <th class="py-2 px-4 border-b text-left text-gray-600 font-semibold">Radius</th>
                        <th class="py-2 px-4 border-b text-center text-gray-600 font-semibold">Jumlah Shift</th>
                        <th class="py-2 px-4 border-b text-center text-gray-600 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offices as $office)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="py-3 px-4 border-b text-gray-800 font-medium">{{ $office->name }}</td>
                        <td class="py-3 px-4 border-b text-gray-600 text-sm">{{ $office->latitude }}, <br> {{ $office->longitude }}</td>
                        <td class="py-3 px-4 border-b text-gray-600">{{ $office->radius }} m</td>
                        
                        <td class="py-3 px-4 border-b text-center text-gray-600">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                {{ $office->shifts->count() }} Shift
                            </span>
                        </td>
                        
                        <td class="py-3 px-4 border-b text-center">
                            <a href="{{ route('setting.shift', $office->id) }}" class="bg-green-500 hover:bg-green-600 text-white text-sm py-1 px-3 rounded mr-1 transition duration-200 inline-block mb-1 sm:mb-0">
                                ⏱️ Kelola Shift
                            </a>
                            <a href="{{ route('setting.edit', $office->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm py-1 px-3 rounded transition duration-200 inline-block">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">Belum ada data kantor yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection