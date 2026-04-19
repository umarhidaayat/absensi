@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md overflow-hidden m-4">
    <div class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">✏️ Edit Data Kantor</h2>

        <form action="{{ route('setting.update', $office->id) }}" method="POST">
            @csrf
            @method('PUT') <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kantor / Cabang</label>
                <input type="text" name="name" value="{{ $office->name }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none" placeholder="Contoh: Cabang Jakarta">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Latitude</label>
                    <input type="text" name="latitude" id="lat-input" value="{{ $office->latitude }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-50">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Longitude</label>
                    <input type="text" name="longitude" id="long-input" value="{{ $office->longitude }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-50">
                </div>
            </div>

            <button type="button" onclick="getCurrentLocationForOffice()" class="mb-4 w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded transition duration-200">
                📍 Update dengan Koordinat Lokasi Saya Saat Ini
            </button>
            <p id="location-status" class="text-sm mb-4 text-center"></p>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Radius Toleransi (Meter)</label>
                <input type="number" name="radius" value="{{ $office->radius }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            </div>

            <div class="flex justify-between">
                <a href="{{ route('setting.index') }}" class="text-gray-500 hover:text-gray-700 font-bold py-2 px-4">Batal</a>
                <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-lg transition duration-200">Update Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    function getCurrentLocationForOffice() {
        const statusText = document.getElementById('location-status');
        statusText.innerText = "Mencari lokasi...";
        statusText.className = "text-sm text-yellow-600 mb-4 text-center";

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('lat-input').value = position.coords.latitude;
                    document.getElementById('long-input').value = position.coords.longitude;
                    statusText.innerText = "Lokasi berhasil diupdate!";
                    statusText.className = "text-sm text-green-600 mb-4 text-center font-semibold";
                },
                function(error) {
                    statusText.innerText = "Gagal mengambil lokasi.";
                    statusText.className = "text-sm text-red-600 mb-4 text-center font-semibold";
                },
                { enableHighAccuracy: true }
            );
        }
    }
</script>
@endsection