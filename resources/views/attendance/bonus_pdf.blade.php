<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Bonus & Potongan Karyawan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .subtitle { font-size: 13px; margin: 5px 0 0 0; color: #555; }
        .info { margin-bottom: 15px; font-size: 12px; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #ccc; padding: 8px; }
        table.data th { background-color: #f4f4f4; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 10px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .text-red { color: #d32f2f; }
        .text-green { color: #2e7d32; }
        .text-blue { color: #1565c0; }
        .text-orange { color: #e65100; }
        .bg-light { background-color: #f9fafb; }
    </style>
</head>
<body>

    <div class="header">
        <p class="title">Laporan Bonus & Potongan Karyawan</p>
        <p class="subtitle">Sistem Absensi</p>
    </div>

    <div class="info">
        <strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Karyawan</th>
                <th width="15%" class="text-red">Potongan Telat</th>
                <th width="15%" class="text-green">Bonus Awal</th>
                <th width="15%" class="text-blue">Bonus Lembur (Jam)</th>
                <th width="15%" class="text-orange">Bonus Lembur (Hari)</th>
                <th width="15%" class="bg-light">Total Bersih</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($users as $user)
                @php
                    $userAbsen = $attendances->where('user_id', $user->id);
                    $t_lateMins = 0; $t_earlyMins = 0; $t_overMins = 0; $t_overDays = 0;

                    foreach($userAbsen as $absen) {
                        if (\Carbon\Carbon::parse($absen->date)->isWeekend() && $absen->time_in) {
                            $t_overDays++;
                        }
                        $shift = $shiftLookup[$absen->location] ?? null;
                        if($shift && $absen->time_in) {
                            $inParts = explode(':', $absen->time_in);
                            $inMins = ((int)$inParts[0] * 60) + (int)$inParts[1];
                            $startParts = explode(':', $shift->start_time);
                            $startMins = ((int)$startParts[0] * 60) + (int)$startParts[1];

                            if ($inMins > $startMins) {
                                $t_lateMins += ($inMins - $startMins); 
                            } elseif ($inMins < $startMins) {
                                $t_earlyMins += ($startMins - $inMins); 
                            }
                            if($absen->time_out) {
                                $outParts = explode(':', $absen->time_out);
                                $outMins = ((int)$outParts[0] * 60) + (int)$outParts[1];
                                $endParts = explode(':', $shift->end_time);
                                $endMins = ((int)$endParts[0] * 60) + (int)$endParts[1];

                                if ($outMins > $endMins) {
                                    $t_overMins += ($outMins - $endMins); 
                                }
                            }
                        }
                    }

                    $potonganTelat = $t_lateMins * $rateLate;
                    $bonusAwal = $t_earlyMins * $rateEarly;
                    $bonusLebihJam = $t_overMins * $rateOverMin;
                    $bonusLemburHari = $t_overDays * $rateOverDay;
                    $netTotal = ($bonusAwal + $bonusLebihJam + $bonusLemburHari) - $potonganTelat;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="font-bold">{{ strtoupper($user->name) }}</td>
                    <td class="text-right">
                        <span style="font-size: 9px; color: #888; display:block;">{{ $t_lateMins }} Mnt</span>
                        <span class="text-red font-bold">-Rp {{ number_format($potonganTelat, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right">
                        <span style="font-size: 9px; color: #888; display:block;">{{ $t_earlyMins }} Mnt</span>
                        <span class="text-green font-bold">+Rp {{ number_format($bonusAwal, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right">
                        <span style="font-size: 9px; color: #888; display:block;">{{ $t_overMins }} Mnt</span>
                        <span class="text-blue font-bold">+Rp {{ number_format($bonusLebihJam, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right">
                        <span style="font-size: 9px; color: #888; display:block;">{{ $t_overDays }} Hari</span>
                        <span class="text-orange font-bold">+Rp {{ number_format($bonusLemburHari, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right bg-light font-bold {{ $netTotal >= 0 ? '' : 'text-red' }}">
                        Rp {{ number_format($netTotal, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada data absensi untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>