@extends('layouts.app')

@section('content')
<div class="w-full">
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Perhitungan Bonus & Potongan</h2>
        <p class="text-gray-500 text-sm font-medium mt-1">Kalkulasi otomatis berdasarkan jam absen dan pengaturan tarif</p>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6 border border-gray-100">
        <form action="{{ route('bonus') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 pb-6 border-b border-gray-100">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border-2 border-gray-100 rounded-xl p-2.5 text-sm font-semibold focus:border-indigo-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Tanggal Sampai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border-2 border-gray-100 rounded-xl p-2.5 text-sm font-semibold focus:border-indigo-500 outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-2">Potongan Telat / Menit</label>
                    <div class="flex items-center border-2 border-rose-100 rounded-xl overflow-hidden focus-within:border-rose-500 transition-all">
                        <span class="bg-rose-50 px-3 py-2.5 text-sm font-bold text-rose-600">Rp</span>
                        <input type="number" name="rate_late" value="{{ $rateLate }}" class="w-full p-2.5 text-sm font-bold text-gray-700 outline-none bg-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-2">Bonus Awal / Menit</label>
                    <div class="flex items-center border-2 border-emerald-100 rounded-xl overflow-hidden focus-within:border-emerald-500 transition-all">
                        <span class="bg-emerald-50 px-3 py-2.5 text-sm font-bold text-emerald-600">Rp</span>
                        <input type="number" name="rate_early" value="{{ $rateEarly }}" class="w-full p-2.5 text-sm font-bold text-gray-700 outline-none bg-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-indigo-500 uppercase tracking-widest mb-2">Lembur / Menit</label>
                    <div class="flex items-center border-2 border-indigo-100 rounded-xl overflow-hidden focus-within:border-indigo-500 transition-all">
                        <span class="bg-indigo-50 px-3 py-2.5 text-sm font-bold text-indigo-600">Rp</span>
                        <input type="number" name="rate_over_min" value="{{ $rateOverMin }}" class="w-full p-2.5 text-sm font-bold text-gray-700 outline-none bg-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-amber-500 uppercase tracking-widest mb-2">Lembur Hari Libur</label>
                    <div class="flex items-center border-2 border-amber-100 rounded-xl overflow-hidden focus-within:border-amber-500 transition-all">
                        <span class="bg-amber-50 px-3 py-2.5 text-sm font-bold text-amber-600">Rp</span>
                        <input type="number" name="rate_over_day" value="{{ $rateOverDay }}" class="w-full p-2.5 text-sm font-bold text-gray-700 outline-none bg-transparent">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white p-3.5 rounded-xl hover:bg-indigo-700 font-bold text-sm transition shadow-md shadow-indigo-100 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Kalkulasi Ulang
            </button>
        </form>
    </div>
    
    <div class="flex justify-between items-end mb-4 px-1">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Rekap Data Karyawan</h3>
        </div>
        <a href="{{ route('bonus.exportPdf', request()->query()) }}" target="_blank" class="bg-rose-500 hover:bg-rose-600 text-white px-5 py-2.5 rounded-xl shadow-sm shadow-rose-200 text-sm font-bold transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export ke PDF
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/80">
                    <tr class="text-[10px] uppercase font-black text-gray-500 tracking-widest">
                        <th class="px-5 py-4 text-left">Nama Karyawan</th>
                        <th class="px-3 py-4 text-right text-rose-500">Potongan Telat</th>
                        <th class="px-3 py-4 text-right text-emerald-500">Bonus Awal</th>
                        <th class="px-3 py-4 text-right text-indigo-500">Bonus Kelebihan Jam</th>
                        <th class="px-3 py-4 text-right text-amber-500">Bonus Lembur Hari</th>
                        <th class="px-5 py-4 text-right text-gray-900 bg-gray-100">Total Diterima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                        @php
                            $userAbsen = $attendances->where('user_id', $user->id);
                            
                            $t_lateMins = 0; $t_earlyMins = 0; $t_overMins = 0; $t_overDays = 0;

                            foreach($userAbsen as $absen) {
                                // Hitung Lembur Hari Libur (Sabtu & Minggu)
                                if (\Carbon\Carbon::parse($absen->date)->isWeekend() && $absen->time_in) {
                                    $t_overDays++;
                                }

                                // Proses menit dari shift (Matematika Murni agar tidak kena bug minus)
                                $shift = $shiftLookup[$absen->location] ?? null;
                                if($shift && $absen->time_in) {
                                    // Pecah jam masuk jadi total menit dari jam 00:00
                                    $inParts = explode(':', $absen->time_in);
                                    $inMins = ((int)$inParts[0] * 60) + (int)$inParts[1];

                                    // Pecah jadwal shift mulai jadi total menit
                                    $startParts = explode(':', $shift->start_time);
                                    $startMins = ((int)$startParts[0] * 60) + (int)$startParts[1];

                                    // Hitung telat / awal murni pakai angka
                                    if ($inMins > $startMins) {
                                        $t_lateMins += ($inMins - $startMins); // Telat
                                    } elseif ($inMins < $startMins) {
                                        $t_earlyMins += ($startMins - $inMins); // Datang Awal
                                    }

                                    // Proses Pulang
                                    if($absen->time_out) {
                                        $outParts = explode(':', $absen->time_out);
                                        $outMins = ((int)$outParts[0] * 60) + (int)$outParts[1];

                                        $endParts = explode(':', $shift->end_time);
                                        $endMins = ((int)$endParts[0] * 60) + (int)$endParts[1];

                                        if ($outMins > $endMins) {
                                            $t_overMins += ($outMins - $endMins); // Kelebihan Jam (Lembur)
                                        }
                                    }
                                }
                            }

                            // Konversi ke Rupiah (Pasti positif karena menitnya sudah pasti angka positif)
                            $potonganTelat = $t_lateMins * $rateLate;
                            $bonusAwal = $t_earlyMins * $rateEarly;
                            $bonusLebihJam = $t_overMins * $rateOverMin;
                            $bonusLemburHari = $t_overDays * $rateOverDay;

                            // Total Bersih (Bonus - Potongan)
                            $netTotal = ($bonusAwal + $bonusLebihJam + $bonusLemburHari) - $potonganTelat;
                        @endphp
                        
                        <tr class="hover:bg-indigo-50/30 transition duration-150">
                            <td class="px-5 py-4 font-bold text-gray-800 text-sm whitespace-nowrap">
                                {{ $user->name }}
                            </td>
                            <td class="px-3 py-4 text-right">
                                <span class="text-xs text-rose-500 font-bold block">{{ $t_lateMins }} Mnt</span>
                                <span class="text-sm font-black text-rose-700">-Rp {{ number_format($potonganTelat, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-3 py-4 text-right">
                                <span class="text-xs text-emerald-500 font-bold block">{{ $t_earlyMins }} Mnt</span>
                                <span class="text-sm font-black text-emerald-700">+Rp {{ number_format($bonusAwal, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-3 py-4 text-right">
                                <span class="text-xs text-indigo-500 font-bold block">{{ $t_overMins }} Mnt</span>
                                <span class="text-sm font-black text-indigo-700">+Rp {{ number_format($bonusLebihJam, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-3 py-4 text-right">
                                <span class="text-xs text-amber-500 font-bold block">{{ $t_overDays }} Hari</span>
                                <span class="text-sm font-black text-amber-700">+Rp {{ number_format($bonusLemburHari, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-4 text-right bg-gray-50">
                                <span class="text-base font-black {{ $netTotal >= 0 ? 'text-indigo-600' : 'text-rose-600' }}">
                                    Rp {{ number_format($netTotal, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-8 text-gray-400 font-medium text-sm">Belum ada data absensi untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection