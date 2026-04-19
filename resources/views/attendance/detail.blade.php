@extends('layouts.app')

@section('content')
<div class="w-full">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <a href="{{ route('rekapan', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-indigo-600 font-bold hover:underline flex items-center gap-2 mb-2 text-sm">
                <span>&larr;</span> Kembali ke Rekap
            </a>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Detail Log Absensi</h2>
            <p class="text-gray-500 text-sm font-medium mt-1">Karyawan: <span class="text-indigo-600 font-black uppercase">{{ $user->name }}</span></p>
        </div>
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 text-xs font-bold text-gray-600">
            Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl mb-6 font-semibold shadow-sm text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('rekapan.updateDetail', $user->id) }}" method="POST">
        @csrf
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/80">
                        <tr class="text-[10px] uppercase font-black text-gray-500 tracking-widest">
                            <th class="p-4 text-left">Hari / Tanggal</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-left">Kantor & Shift</th>
                            <th class="p-4 text-center">Absen Masuk</th>
                            <th class="p-4 text-center">Absen Pulang</th>
                            <th class="p-4 text-center text-rose-500">Telat (Mnt)</th>
                            <th class="p-4 text-center text-indigo-500">Kelebihan (Mnt)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($dates as $date)
                            @php
                                $dateStr = $date->format('Y-m-d');
                                $isWeekend = $date->isWeekend(); 
                                $absen = $attendances[$dateStr] ?? null;
                                $rowBg = $isWeekend ? 'bg-rose-50/50' : 'hover:bg-indigo-50/30';
                            @endphp

                            <tr class="{{ $rowBg }} transition duration-150">
                                <td class="p-3 text-sm">
                                    <span class="font-bold {{ $isWeekend ? 'text-rose-600' : 'text-gray-800' }}">
                                        {{ $date->translatedFormat('l') }}
                                    </span><br>
                                    <span class="text-xs text-gray-500">{{ $date->translatedFormat('d M Y') }}</span>
                                </td>

                                <td class="p-3">
                                    <select name="logs[{{ $dateStr }}][status]" class="w-full border-2 border-gray-100 rounded-lg p-2 text-xs font-bold focus:border-indigo-500 outline-none text-gray-700 bg-transparent disabled:opacity-70 disabled:bg-gray-50 disabled:cursor-not-allowed" {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                                        <option value="">- Kosong -</option>
                                        <option value="present" {{ ($absen && in_array($absen->status, ['present', 'hadir'])) ? 'selected' : '' }}>Hadir</option>
                                        <option value="alpha" {{ ($absen && $absen->status === 'alpha') ? 'selected' : '' }}>Alpha</option>
                                        <option value="izin" {{ ($absen && $absen->status === 'izin') ? 'selected' : '' }}>Izin</option>
                                        <option value="sakit" {{ ($absen && $absen->status === 'sakit') ? 'selected' : '' }}>Sakit</option>
                                        <option value="cuti" {{ ($absen && $absen->status === 'cuti') ? 'selected' : '' }}>Cuti</option>
                                    </select>
                                </td>
                                
                                <td class="p-3">
                                    <select id="shift_{{ $dateStr }}" name="logs[{{ $dateStr }}][shift_info]" onchange="calculateTime('{{ $dateStr }}')" class="w-full border-2 border-gray-100 rounded-lg p-2 text-xs font-bold focus:border-indigo-500 outline-none text-gray-700 bg-transparent disabled:opacity-70 disabled:bg-gray-50 disabled:cursor-not-allowed" {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                                        <option value="" data-start="" data-end="">-- Kosong / Libur --</option>
                                        @foreach($offices as $office)
                                            <optgroup label="{{ $office->name }}">
                                            @foreach($office->shifts as $shift)
                                                @php 
                                                    $shiftLabel = $office->name . ' - ' . $shift->name; 
                                                    $isSelected = ($absen && $absen->location === $shiftLabel) ? 'selected' : '';
                                                @endphp
                                                <option value="{{ $shiftLabel }}" data-start="{{ substr($shift->start_time,0,5) }}" data-end="{{ substr($shift->end_time,0,5) }}" {{ $isSelected }}>
                                                    {{ $shift->name }} ({{ substr($shift->start_time,0,5) }}-{{ substr($shift->end_time,0,5) }})
                                                </option>
                                            @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="p-3 text-center">
                                    <input type="time" id="in_{{ $dateStr }}" name="logs[{{ $dateStr }}][time_in]" value="{{ $absen ? substr($absen->time_in,0,5) : '' }}" onchange="calculateTime('{{ $dateStr }}')" class="border-2 border-gray-100 rounded-lg p-2 text-xs font-bold text-emerald-700 outline-none focus:border-emerald-500 bg-transparent w-full max-w-[120px] disabled:opacity-70 disabled:bg-gray-50 disabled:cursor-not-allowed" {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                                </td>

                                <td class="p-3 text-center">
                                    <input type="time" id="out_{{ $dateStr }}" name="logs[{{ $dateStr }}][time_out]" value="{{ $absen ? substr($absen->time_out,0,5) : '' }}" onchange="calculateTime('{{ $dateStr }}')" class="border-2 border-gray-100 rounded-lg p-2 text-xs font-bold text-rose-700 outline-none focus:border-rose-500 bg-transparent w-full max-w-[120px] disabled:opacity-70 disabled:bg-gray-50 disabled:cursor-not-allowed" {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                                </td>

                                <td class="p-3 text-center">
                                    <span id="late_{{ $dateStr }}" class="text-xs font-black text-rose-600">0 mnt</span>
                                </td>

                                <td class="p-3 text-center">
                                    <span id="over_{{ $dateStr }}" class="text-xs font-black text-indigo-600">0 mnt</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->role === 'admin')
        <div class="flex flex-col sm:flex-row justify-end gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner">
            <button type="submit" class="px-8 py-3.5 bg-indigo-600 text-white rounded-xl font-black shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Simpan Perubahan Log
            </button>
        </div>
        @endif
    </form>
</div>

<script>
    function timeToMinutes(timeStr) {
        if (!timeStr) return 0;
        let parts = timeStr.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    }

    function calculateTime(dateStr) {
        let shiftSelect = document.getElementById('shift_' + dateStr);
        let timeIn = document.getElementById('in_' + dateStr).value;
        let timeOut = document.getElementById('out_' + dateStr).value;
        
        let lateDisplay = document.getElementById('late_' + dateStr);
        let overDisplay = document.getElementById('over_' + dateStr);

        let selectedOption = shiftSelect.options[shiftSelect.selectedIndex];
        let shiftStart = selectedOption.getAttribute('data-start');
        let shiftEnd = selectedOption.getAttribute('data-end');

        lateDisplay.innerText = '0 mnt';
        overDisplay.innerText = '0 mnt';

        if (timeIn && shiftStart) {
            let diffMasuk = timeToMinutes(timeIn) - timeToMinutes(shiftStart);
            if (diffMasuk > 0) {
                lateDisplay.innerText = diffMasuk + ' mnt';
            }
        }

        if (timeOut && shiftEnd) {
            let diffPulang = timeToMinutes(timeOut) - timeToMinutes(shiftEnd);
            if (diffPulang > 0) {
                overDisplay.innerText = '+' + diffPulang + ' mnt';
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        @foreach($dates as $date)
            calculateTime('{{ $date->format("Y-m-d") }}');
        @endforeach
    });
</script>
@endsection