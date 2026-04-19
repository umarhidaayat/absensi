<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Office;
use App\Models\Shift;

class SettingController extends Controller
{
    public function index()
    {
        $offices = Office::all(); 
        return view('settings.index', compact('offices'));
    }

    public function create()
    {
        return view('settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'radius' => 'required|integer|min:10',
        ]);

        Office::create($request->all());

        return redirect()->route('setting.index')->with('success', 'Kantor baru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $office = Office::findOrFail($id);
        return view('settings.edit', compact('office'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'radius' => 'required|integer|min:10',
        ]);

        $office = Office::findOrFail($id);
        $office->update($request->all());

        return redirect()->route('setting.index')->with('success', 'Data kantor berhasil diperbarui!');
    }

    public function shiftIndex(Office $office)
    {
        $shifts = $office->shifts;
        return view('settings.shift', compact('office', 'shifts'));
    }

    public function shiftStore(Request $request, Office $office)
    {
        $request->validate([
            'name' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $office->shifts()->create($request->all());

        return back()->with('success', 'Shift berhasil ditambahkan!');
    }

    public function shiftDestroy(Shift $shift)
    {
        $shift->delete();
        return back()->with('success', 'Shift berhasil dihapus!');
    }
}