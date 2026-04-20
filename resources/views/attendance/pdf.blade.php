<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Absensi - {{ $user->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; margin: 0; }
        .subtitle { font-size: 14px; margin: 5px 0 0 0; color: #555; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px 0; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        table.data th { background-color: #f4f4f4; font-weight: bold; }
        table.data td.left { text-align: left; }
        
        .weekend { background-color: #ffeeee; }
        .text-red { color: #d32f2f; font-weight: bold; }
        .text-green { color: #2e7d32; font-weight: bold; }
        .text-blue { color: #1565c0; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <p class="title">REKAPITULASI DETAIL ABSENSI KARYAWAN</p>
        <p class="subtitle">Sistem Absensi</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Nama Karyawan</strong></td>
            <td width="2%">:</td>
            <td width="33%">{{ strtoupper($user->name) }}</td>
            <td width="15%"><strong>Periode</strong></td>
            <td width="2%">:</td>
            <td width="33%">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Hari / Tanggal</th>
                <th>Status</th>
                <th>Kantor & Shift</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Telat (Mnt)</th>
                <th>Kelebihan (Mnt)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dates as $date)
                @php
                    $dateStr = $date->format('Y-m-d');
                    $isWeekend = $date->isWeekend(); 
                    $absen = $attendances[$dateStr] ?? null;
                    
                    // Mencari Jadwal Shift untuk perhitungan PHP
                    $shiftStart = null;
                    $shiftEnd = null;
                    if($absen && $absen->location && $absen->location !== '-'){
                        foreach($offices as $office) {
                            foreach($office->shifts as $shift) {
                                if($office->name . ' - ' . $shift->name === $absen->location) {
                                    $shiftStart = \Carbon\Carbon::parse($shift->start_time);
                                    $shiftEnd = \Carbon\Carbon::parse($shift->end_time);
                                }
                            }
                        }
                    }

                    // Menghitung telat dan lembur di server-side (PHP)
                    $late = 0; $over = 0;
                    if ($absen && $absen->time_in && $shiftStart) {
                        $in = \Carbon\Carbon::parse($absen->time_in);
                        if ($in->gt($shiftStart)) $late = $in->diffInMinutes($shiftStart);
                    }
                    if ($absen && $absen->time_out && $shiftEnd) {
                        $out = \Carbon\Carbon::parse($absen->time_out);
                        if ($out->gt($shiftEnd)) $over = $out->diffInMinutes($shiftEnd);
                    }
                @endphp

                <tr class="{{ $isWeekend ? 'weekend' : '' }}">
                    <td class="left">
                        <strong class="{{ $isWeekend ? 'text-red' : '' }}">{{ $date->translatedFormat('l') }}</strong><br>
                        <span style="font-size: 10px; color: #555;">{{ $date->translatedFormat('d M Y') }}</span>
                    </td>
                    <td>{{ $absen ? ucfirst($absen->status) : '-' }}</td>
                    <td class="left">{{ $absen ? $absen->location : '-' }}</td>
                    <td class="{{ $absen && $absen->time_in ? 'text-green' : '' }}">
                        {{ $absen && $absen->time_in ? substr($absen->time_in, 0, 5) : '-' }}
                    </td>
                    <td class="{{ $absen && $absen->time_out ? 'text-red' : '' }}">
                        {{ $absen && $absen->time_out ? substr($absen->time_out, 0, 5) : '-' }}
                    </td>
                    <td class="text-red">{{ $late > 0 ? $late : '-' }}</td>
                    <td class="text-blue">{{ $over > 0 ? '+' . $over : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>