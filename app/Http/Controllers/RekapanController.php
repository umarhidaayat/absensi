<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Office;
use Carbon\Carbon;

class RekapanController extends Controller
{
    // Fungsi bantuan untuk mendapatkan default tanggal (Smart Cut-Off 25 - 24)
    private function getDefaultDateRange()
    {
        $now = Carbon::now();
        if ($now->day >= 25) {
            // Jika hari ini tanggal 25 atau lebih: 25 Bulan Ini - 24 Bulan Depan
            $start = $now->copy()->day(25)->toDateString();
            $end = $now->copy()->addMonth()->day(24)->toDateString();
        } else {
            // Jika hari ini sebelum tanggal 25: 25 Bulan Lalu - 24 Bulan Ini
            $start = $now->copy()->subMonth()->day(25)->toDateString();
            $end = $now->copy()->day(24)->toDateString();
        }
        return ['start' => $start, 'end' => $end];
    }

    public function index(Request $request)
    {
        $defaultDates = $this->getDefaultDateRange();
        $startDate = $request->input('start_date', $defaultDates['start']);
        $endDate = $request->input('end_date', $defaultDates['end']);
        
        if (auth()->user()->role === 'admin') {
            $users = User::where('role', 'karyawan')->get();
        } else {
            $users = User::where('id', auth()->id())->get();
        }

        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])->get();

        return view('attendance.rekapan', compact('users', 'attendances', 'startDate', 'endDate'));
    }

    public function detail(Request $request, $id)
    {
        if (auth()->user()->role === 'karyawan' && auth()->id() != $id) {
            abort(403, 'Akses Ditolak! Anda hanya diizinkan melihat data absensi Anda sendiri.');
        }

        $user = User::findOrFail($id);
        
        $defaultDates = $this->getDefaultDateRange();
        $startDate = $request->input('start_date', $defaultDates['start']);
        $endDate = $request->input('end_date', $defaultDates['end']);

        $attendances = Attendance::where('user_id', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy('date'); 

        $offices = Office::with('shifts')->get();

        $dates = [];
        $currentDate = Carbon::parse($startDate);
        $lastDate = Carbon::parse($endDate);

        while ($currentDate->lte($lastDate)) {
            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }

        return view('attendance.detail', compact('user', 'attendances', 'startDate', 'endDate', 'dates', 'offices'));
    }

    public function updateDetail(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Hanya Admin yang dapat mengubah log absensi.');
        }

        $logs = $request->input('logs', []);

        foreach ($logs as $date => $data) {
            $timeIn = $data['time_in'] ?? null;
            $timeOut = $data['time_out'] ?? null;
            $shiftInfo = $data['shift_info'] ?? null;
            $status = $data['status'] ?? null;

            if (empty($timeIn) && empty($timeOut) && empty($status)) {
                Attendance::where('user_id', $id)->where('date', $date)->delete();
                continue; 
            }

            $finalStatus = !empty($status) ? $status : 'present';

            Attendance::updateOrCreate(
                ['user_id' => $id, 'date' => $date],
                [
                    'time_in' => !empty($timeIn) ? $timeIn . ':00' : null,
                    'time_out' => !empty($timeOut) ? $timeOut . ':00' : null,
                    'location' => !empty($shiftInfo) ? $shiftInfo : '-', 
                    'status' => $finalStatus
                ]
            );
        }

        return back()->with('success', 'Perubahan log absensi & status berhasil disimpan ke database!');
    }
}