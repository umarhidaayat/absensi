<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Office;
use Carbon\Carbon;

class BonusController extends Controller
{
    // Fungsi bantuan untuk mendapatkan default tanggal (Smart Cut-Off 25 - 24)
    private function getDefaultDateRange()
    {
        $now = Carbon::now();
        if ($now->day >= 25) {
            $start = $now->copy()->day(25)->toDateString();
            $end = $now->copy()->addMonth()->day(24)->toDateString();
        } else {
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

        $rateLate = $request->input('rate_late', 166);         
        $rateEarly = $request->input('rate_early', 0);       
        $rateOverMin = $request->input('rate_over_min', 299); 
        $rateOverDay = $request->input('rate_over_day', 55000); 

        $users = User::where('role', 'karyawan')->get();
        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])->get();
        $offices = Office::with('shifts')->get();

        $shiftLookup = [];
        foreach ($offices as $office) {
            foreach ($office->shifts as $shift) {
                $label = $office->name . ' - ' . $shift->name;
                $shiftLookup[$label] = $shift;
            }
        }

        return view('attendance.bonus', compact(
            'users', 'attendances', 'startDate', 'endDate', 
            'rateLate', 'rateEarly', 'rateOverMin', 'rateOverDay', 'shiftLookup'
        ));
    }
}