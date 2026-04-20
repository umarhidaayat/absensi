@extends('layouts.app')

@section('content')
<div class="w-full">
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Rekap Absensi</h2>
        <p class="text-gray-500 text-sm font-medium mt-1">Laporan Rekapitulasi Kehadiran</p>
    </div>
    
    <div class="bg-white p-5 rounded-2xl shadow-sm mb-6 border border-gray-100">
        <form action="{{ route('rekapan') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border-2 border-gray-100 rounded-xl p-2.5 text-sm font-semibold focus:border-indigo-500 outline-none transition-all">
            </div>
            <div class="w-full md:w-1/3">
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Tanggal Sampai</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border-2 border-gray-100 rounded-xl p-2.5 text-sm font-semibold focus:border-indigo-500 outline-none transition-all">
            </div>
            <div class="w-full md:w-1/3">
                <button type="submit" class="w-full bg-indigo-600 text-white p-2.5 rounded-xl hover:bg-indigo-700 font-bold text-sm transition shadow-md shadow-indigo-100 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Tampilkan Rekap
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/80">
                    <tr class="text-[10px] uppercase font-black text-gray-500 tracking-widest">
                        <th class="px-6 py-4 text-left">Nama Karyawan</th>
                        <th class="px-2 py-4 text-center w-16" title="Hadir (Masuk Kerja)">Hadir</th>
                        <th class="px-2 py-4 text-center w-16 text-rose-500" title="Alpha (Tanpa Keterangan)">Alpha</th>
                        <th class="px-2 py-4 text-center w-16 text-blue-500" title="Izin">Izin</th>
                        <th class="px-2 py-4 text-center w-16 text-amber-500" title="Sakit">Sakit</th>
                        <th class="px-2 py-4 text-center w-16 text-purple-500" title="Cuti">Cuti</th>
                        <th class="px-6 py-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                        @php
                            $userAbsen = $attendances->where('user_id', $user->id);
                            
                            $totalHadir = $userAbsen->whereIn('status', ['present', 'hadir'])->count();
                            $totalAlpha = $userAbsen->where('status', 'alpha')->count();
                            $totalIzin  = $userAbsen->where('status', 'izin')->count();
                            $totalSakit = $userAbsen->where('status', 'sakit')->count();
                            $totalCuti  = $userAbsen->where('status', 'cuti')->count();
                        @endphp
                        <tr class="hover:bg-indigo-50/30 transition duration-150">
                            <td class="px-6 py-4 font-bold text-gray-800 whitespace-nowrap text-sm">
                                {{ $user->name }}
                            </td>
                            
                            <td class="px-2 py-4 text-center">
                                <div class="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-xs font-black {{ $totalHadir > 0 ? 'bg-emerald-100 text-emerald-700' : 'text-gray-400' }}">
                                    {{ $totalHadir }}
                                </div>
                            </td>

                            <td class="px-2 py-4 text-center">
                                <div class="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-xs font-black {{ $totalAlpha > 0 ? 'bg-rose-100 text-rose-700' : 'text-gray-400' }}">
                                    {{ $totalAlpha }}
                                </div>
                            </td>

                            <td class="px-2 py-4 text-center">
                                <div class="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-xs font-black {{ $totalIzin > 0 ? 'bg-blue-100 text-blue-700' : 'text-gray-400' }}">
                                    {{ $totalIzin }}
                                </div>
                            </td>

                            <td class="px-2 py-4 text-center">
                                <div class="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-xs font-black {{ $totalSakit > 0 ? 'bg-amber-100 text-amber-700' : 'text-gray-400' }}">
                                    {{ $totalSakit }}
                                </div>
                            </td>

                            <td class="px-2 py-4 text-center">
                                <div class="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-xs font-black {{ $totalCuti > 0 ? 'bg-purple-100 text-purple-700' : 'text-gray-400' }}">
                                    {{ $totalCuti }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('rekapan.detail', ['id' => $user->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold hover:bg-indigo-600 hover:text-white transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-8 text-gray-400 font-medium text-sm">Belum ada data karyawan terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection