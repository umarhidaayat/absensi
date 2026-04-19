<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Office;

class AttendanceController extends Controller
{
    // Menampilkan halaman ceklok dengan data dinamis
    public function ceklok()
    {
        $offices = Office::with('shifts')->get();
        
        $userId = auth()->id();
        $today = now()->toDateString();
        $absenHariIni = Attendance::where('user_id', $userId)->where('date', $today)->first();
        
        return view('attendance.ceklok', compact('offices', 'absenHariIni')); 
    }

    // Memproses data absen yang dikirim
    public function store(Request $request)
    {
        // 1. Tambahkan validasi shift_id agar terbaca
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'office_id' => 'required|exists:offices,id',
            'shift_id' => 'required|exists:shifts,id', // <-- Ini yang baru
            'tipe_absen' => 'required|in:IN,OUT',
        ]);

        $office = Office::findOrFail($request->office_id);
        
        // 2. Cari nama shift yang dipilih karyawan
        $shift = \App\Models\Shift::findOrFail($request->shift_id);
        
        // 3. Gabungkan namanya agar persis seperti format di halaman Admin
        $shiftLabel = $office->name . ' - ' . $shift->name;

        // Pengecekan Radius Jarak GPS
        $distance = $this->calculateDistance(
            $request->latitude, 
            $request->longitude, 
            $office->latitude, 
            $office->longitude
        );

        if ($distance > $office->radius) {
            return back()->with('error', 'Gagal absen! Anda berada ' . round($distance) . ' meter dari lokasi ' . $office->name . '. Maksimal radius adalah ' . $office->radius . ' meter.');
        }

        $userId = auth()->id();
        $today = now()->toDateString();
        $timeNow = now()->toTimeString();

        if ($request->tipe_absen === 'IN') {
            $cekAbsen = Attendance::where('user_id', $userId)->where('date', $today)->first();
            if ($cekAbsen) {
                return back()->with('error', 'Gagal: Anda sudah melakukan Absen Masuk hari ini.');
            }

            // SIMPAN DATA
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'time_in' => $timeNow,
                'location' => $shiftLabel, // <-- UBAH DISINI: Simpan nama shift, bukan koordinat GPS
                'status' => 'present'
            ]);
            
            return back()->with('success', '📍 Berhasil Absen Masuk! Selamat bekerja di ' . $office->name . ' (' . $shift->name . ').');
            
        } else {
            $absen = Attendance::where('user_id', $userId)->where('date', $today)->first();
            
            if (!$absen) {
                return back()->with('error', 'Gagal: Anda belum Absen Masuk hari ini.');
            }
            if ($absen->time_out) {
                return back()->with('error', 'Gagal: Anda sudah melakukan Absen Pulang hari ini.');
            }

            $absen->update([
                'time_out' => $timeNow
            ]);

            return back()->with('success', '🏠 Berhasil Absen Pulang! Hati-hati di jalan.');
        }
    }

    // Fungsi Haversine untuk menghitung jarak (dalam meter)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; 
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            
        return $angle * $earthRadius;
    }
}