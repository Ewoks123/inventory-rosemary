<!DOCTYPE html>
<html>
<head>
    <title>Laporan Material</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8px; }
        table { border-collapse: collapse; width: 100%; table-layout: auto; }
        th, td { border: 1px solid #000; padding: 2px; text-align: center; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>
    @php
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $daysCount = iterator_count($period);
        $grandTotalNilai = 0;

        $inByDate = [];
        $outByDate = [];
        foreach($materials as $m) {
            $inByDate[$m->id] = [];
            $outByDate[$m->id] = [];
            foreach($m->logs as $log) {
                $d = date('Y-m-d', strtotime($log->date));
                if ($log->type == 'in') {
                    $inByDate[$m->id][$d] = ($inByDate[$m->id][$d] ?? 0) + $log->quantity;
                } else {
                    $outByDate[$m->id][$d] = ($outByDate[$m->id][$d] ?? 0) + $log->quantity;
                }
            }
        }
    @endphp
    <table style="border-collapse: collapse; font-size: 8px; width: 100%;">
        <thead>
            <tr>
                <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #000; padding: 4px;">No</th>
                <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #000; padding: 4px;">Nama Material</th>
                <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #000; padding: 4px;">Satuan</th>
                <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #000; padding: 4px;">Harga Satuan</th>
                <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #000; padding: 4px;">Total Nilai Stok</th>
                <th colspan="{{ $daysCount }}" style="background-color: #e74c3c; color: white; border: 1px solid #000; padding: 4px; text-align: center;">BARANG MASUK</th>
                <th rowspan="2" style="background-color: #e74c3c; color: white; border: 1px solid #000; padding: 4px;">Total Masuk</th>
                <th colspan="{{ $daysCount }}" style="background-color: #27ae60; color: white; border: 1px solid #000; padding: 4px; text-align: center;">PRODUKSI (KELUAR)</th>
                <th rowspan="2" style="background-color: #27ae60; color: white; border: 1px solid #000; padding: 4px;">Total Keluar</th>
                <th rowspan="2" style="background-color: #f39c12; color: white; border: 1px solid #000; padding: 4px;">Sisa Stok</th>
            </tr>
            <tr>
                @foreach($period as $date)
                    <th style="background-color: #e74c3c; color: white; border: 1px solid #000; padding: 2px;">{{ $date->format('d/m') }}</th>
                @endforeach
                @foreach($period as $date)
                    <th style="background-color: #27ae60; color: white; border: 1px solid #000; padding: 2px;">{{ $date->format('d/m') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($materials as $index => $m)
            @php 
                $hargaSatuan = is_numeric($m->supplier) ? (float) $m->supplier : 0;
                $totalNilaiStok = $m->stok_material * $hargaSatuan;
                $grandTotalNilai += $totalNilaiStok;
                $totalIn = 0;
                $totalOut = 0;
            @endphp
            <tr>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: left;">{{ $m->nama_material }}</td>
                <td style="border: 1px solid #000; padding: 4px;">{{ $m->satuan }}</td>
                <td style="border: 1px solid #000; padding: 4px;">{{ $hargaSatuan }}</td>
                <td style="border: 1px solid #000; padding: 4px; background-color: #d1f2eb; font-weight: bold;">{{ $totalNilaiStok }}</td>

                @foreach($period as $date)
                    @php 
                        $qty = $inByDate[$m->id][$date->format('Y-m-d')] ?? 0;
                        $totalIn += $qty;
                    @endphp
                    <td style="border: 1px solid #000; padding: 2px; text-align: center;">{{ $qty ? number_format($qty, 0, '', '') : '' }}</td>
                @endforeach
                <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold; background-color: #f8f9fa;">{{ number_format($totalIn, 0, '', '') }}</td>

                @foreach($period as $date)
                    @php 
                        $qty = $outByDate[$m->id][$date->format('Y-m-d')] ?? 0;
                        $totalOut += $qty;
                    @endphp
                    <td style="border: 1px solid #000; padding: 2px; text-align: center;">{{ $qty ? number_format($qty, 0, '', '') : '' }}</td>
                @endforeach
                <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold; background-color: #f8f9fa;">{{ number_format($totalOut, 0, '', '') }}</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold; background-color: #fcf3cf;">
                    {{ number_format($m->stok_material, 0, '', '') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold; padding: 8px;">Grand Total Nilai Seluruh Stok:</td>
                <td style="font-weight: bold; padding: 8px; background-color: #d1f2eb;">{{ $grandTotalNilai }}</td>
                <td colspan="{{ ($daysCount * 2) + 3 }}"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
