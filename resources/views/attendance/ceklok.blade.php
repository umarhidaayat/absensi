@extends('layouts.app')

@section('content')

@php
    // Cek status hari ini
    $sudahMasuk = $absenHariIni ? true : false;
    $sudahPulang = ($absenHariIni && $absenHariIni->time_out) ? true : false;
@endphp

<div class="max-w-3xl mx-auto bg-white p-5 sm:p-8 md:p-12 rounded-[2rem] shadow-xl text-center border border-gray-100 mt-4 sm:mt-10">
    <p class="text-gray-400 text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] mb-2 sm:mb-4">Sistem Absensi</p>
    <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 mb-6 sm:mb-8 tracking-tight">Halo, {{ auth()->user()->name ?? 'Karyawan' }}!</h2>
    
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 text-sm sm:text-base text-left font-semibold">
            ⚠️ {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 text-sm sm:text-base text-left font-semibold">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="my-6 sm:my-10 p-6 sm:p-10 bg-indigo-50 rounded-[1.5rem] sm:rounded-[2rem] border border-indigo-100/50">
        <p class="text-4xl sm:text-5xl md:text-6xl font-mono font-black text-indigo-600 tracking-tighter" id="current-time-display">--:--:--</p>
        <p class="text-indigo-400 text-xs sm:text-sm mt-3 sm:mt-4 font-bold uppercase tracking-widest" id="current-date-display">Memuat Tanggal...</p>
    </div>
    
    <form id="ceklok-form" action="{{ route('ceklok.store') }}" method="POST">
        @csrf
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="hidden" name="tipe_absen" id="tipe_absen">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8 sm:mb-10 text-left">
            <div class="space-y-2">
                <label class="text-[10px] sm:text-xs font-bold text-gray-500 uppercase ml-1">Pilih Lokasi Kantor</label>
                <select name="office_id" id="office-select" onchange="handleOfficeSelect()" required class="w-full p-3 sm:p-4 bg-gray-50 border-2 border-gray-100 rounded-xl sm:rounded-2xl focus:border-indigo-500 outline-none transition-all font-semibold cursor-pointer text-sm sm:text-base">
                    <option value="">-- Pilih Kantor --</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] sm:text-xs font-bold text-gray-500 uppercase ml-1">Pilih Jam Kerja / Shift</label>
                <select name="shift_id" id="shift-select" onchange="checkButtonState()" required class="w-full p-3 sm:p-4 bg-gray-50 border-2 border-gray-100 rounded-xl sm:rounded-2xl focus:border-indigo-500 outline-none transition-all font-semibold disabled:opacity-50 cursor-pointer text-sm sm:text-base" disabled>
                    <option value="">-- Pilih Kantor Dulu --</option>
                </select>
            </div>
        </div>
        
        <p id="location-status" class="text-xs sm:text-sm font-bold text-amber-500 mb-6 hidden">📍 Sedang memproses lokasi Anda...</p>

        @if($sudahMasuk)
            <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600">
                Status Hari Ini: <span class="text-emerald-600 font-bold">Hadir</span> (Masuk: {{ substr($absenHariIni->time_in, 0, 5) }} WITA)
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <button id="btn-in" type="button" onclick="submitCeklok('IN')" 
                class="flex-1 py-4 sm:py-6 font-black text-base sm:text-xl rounded-xl sm:rounded-2xl transition-all shadow-lg text-white bg-gray-300 shadow-none cursor-not-allowed opacity-60" disabled>
                {{ $sudahMasuk ? '✅ SUDAH MASUK' : '📍 ABSEN MASUK' }}
            </button>

            <button id="btn-out" type="button" onclick="submitCeklok('OUT')" 
                class="flex-1 py-4 sm:py-6 font-black text-base sm:text-xl rounded-xl sm:rounded-2xl transition-all shadow-lg text-white bg-gray-300 shadow-none cursor-not-allowed opacity-60" disabled>
                {{ $sudahPulang ? '✅ SUDAH PULANG' : '🏠 ABSEN PULANG' }}
            </button>
        </div>
    </form>
</div>

<script>
    // Script Jam Real-time
    function updateClock() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date-display').textContent = now.toLocaleDateString('id-ID', options);
        document.getElementById('current-time-display').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
    }
    setInterval(updateClock, 1000);
    updateClock();

    const officesData = @json($offices);

    // Fungsi saat Cabang dipilih
    function handleOfficeSelect() {
        const officeId = document.getElementById('office-select').value;
        const shiftSelect = document.getElementById('shift-select');
        
        shiftSelect.innerHTML = '<option value="">-- Pilih Shift --</option>';
        
        if (officeId) {
            const selectedOffice = officesData.find(office => office.id == officeId);
            if (selectedOffice && selectedOffice.shifts.length > 0) {
                selectedOffice.shifts.forEach(shift => {
                    const option = document.createElement('option');
                    option.value = shift.id;
                    option.textContent = `${shift.name} (${shift.start_time} - ${shift.end_time})`;
                    shiftSelect.appendChild(option);
                });
                shiftSelect.disabled = false;
            } else {
                shiftSelect.innerHTML = '<option value="">-- Tidak ada shift --</option>';
                shiftSelect.disabled = true;
            }
        } else {
            shiftSelect.disabled = true;
        }

        // Panggil fungsi untuk nge-cek tombol setiap kali kantor berubah
        checkButtonState();
    }

    // FUNGSI BARU: Mengontrol nyala/mati tombol berdasarkan dropdown
    function checkButtonState() {
        const office = document.getElementById('office-select').value;
        const shift = document.getElementById('shift-select').value;
        const btnIn = document.getElementById('btn-in');
        const btnOut = document.getElementById('btn-out');

        const sudahMasuk = {{ $sudahMasuk ? 'true' : 'false' }};
        const sudahPulang = {{ $sudahPulang ? 'true' : 'false' }};
        
        // Kondisi True jika kedua dropdown sudah terisi
        const isReady = office !== "" && shift !== ""; 

        // Style dasar untuk tombol
        const baseClass = "flex-1 py-4 sm:py-6 font-black text-base sm:text-xl rounded-xl sm:rounded-2xl transition-all shadow-lg text-white ";
        const disabledClass = baseClass + "bg-gray-300 shadow-none cursor-not-allowed opacity-60";
        const btnInActive = baseClass + "bg-emerald-500 shadow-emerald-200 hover:bg-emerald-600 active:scale-95";
        const btnOutActive = baseClass + "bg-rose-500 shadow-rose-200 hover:bg-rose-600 active:scale-95";

        // Kontrol Tombol Absen Masuk
        if (!sudahMasuk) {
            if (isReady) {
                btnIn.disabled = false;
                btnIn.className = btnInActive;
            } else {
                btnIn.disabled = true;
                btnIn.className = disabledClass;
            }
        }

        // Kontrol Tombol Absen Pulang
        if (sudahMasuk && !sudahPulang) {
            if (isReady) {
                btnOut.disabled = false;
                btnOut.className = btnOutActive;
            } else {
                btnOut.disabled = true;
                btnOut.className = disabledClass;
            }
        }
    }

    // Proses Absen dengan Efek Loading
    function submitCeklok(type) {
        const btnIn = document.getElementById('btn-in');
        const btnOut = document.getElementById('btn-out');
        const statusText = document.getElementById('location-status');

        statusText.classList.remove('hidden');
        statusText.textContent = "⏳ Memproses koordinat GPS Anda...";
        statusText.classList.replace('text-red-500', 'text-amber-500');

        if (type === 'IN') {
            btnIn.innerHTML = '⏳ MEMPROSES...';
            btnIn.disabled = true;
            btnIn.classList.add('cursor-not-allowed', 'opacity-60');
        } else {
            btnOut.innerHTML = '⏳ MEMPROSES...';
            btnOut.disabled = true;
            btnOut.classList.add('cursor-not-allowed', 'opacity-60');
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    document.getElementById('tipe_absen').value = type;
                    document.getElementById('ceklok-form').submit();
                },
                function(error) {
                    // Revert tombol jika GPS ditolak/gagal
                    checkButtonState();
                    
                    statusText.textContent = "❌ Gagal mendapatkan lokasi. Pastikan GPS HP menyala dan diizinkan browser.";
                    statusText.classList.replace('text-amber-500', 'text-red-500');
                    alert('Sistem membutuhkan akses lokasi untuk memastikan Anda berada di area kantor.');
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        } else {
            alert("Browser Anda tidak mendukung layanan lokasi GPS.");
        }
    }

    // Panggil sekali saat halaman dimuat (agar memastikan tombol state-nya benar)
    document.addEventListener("DOMContentLoaded", checkButtonState);
</script>
@endsection