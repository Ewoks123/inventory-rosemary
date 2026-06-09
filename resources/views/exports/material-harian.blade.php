<!DOCTYPE html>
<html>
<head>
    <title>Laporan Harian Material</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        table { border-collapse: collapse; width: 100%; table-layout: auto; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Harian Material ({{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }})</h2>
    <table style="border-collapse: collapse; font-size: 10px; width: 100%;">
        <thead>
            <tr>
                <th style="background-color: #3498db; color: white;">No</th>
                <th style="background-color: #3498db; color: white;">Nama Material</th>
                
                <th style="background-color: #f39c12; color: white;">Stok Awal</th>
                <th style="background-color: #f39c12; color: white;">Satuan</th>
                <th style="background-color: #f39c12; color: white;">Nilai Stok Awal</th>
                
                <th style="background-color: #27ae60; color: white;">Material Masuk</th>
                <th style="background-color: #27ae60; color: white;">Satuan</th>
                <th style="background-color: #27ae60; color: white;">Nilai Material Masuk</th>
                
                <th style="background-color: #e74c3c; color: white;">Pengeluaran</th>
                
                <th style="background-color: #8e44ad; color: white;">Stok Akhir</th>
                <th style="background-color: #8e44ad; color: white;">Satuan</th>
                <th style="background-color: #8e44ad; color: white;">Nilai Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAwal = 0;
                $totalMasuk = 0;
                $totalKeluar = 0;
                $grandTotalPersediaan = 0;
            @endphp
            @foreach($materials as $index => $m)
                @php 
                    $price = is_numeric($m->supplier) ? (float) $m->supplier : 0;
                    
                    // Get daily movements
                    $masuk = $m->logs->where('type', 'in')->sum('quantity');
                    $keluar = $m->logs->where('type', 'out')->sum('quantity');

                    // Stok Awal (before this day)
                    $stokAwalQty = $stokAwalMap[$m->id] ?? 0;
                    $nilaiAwal = $stokAwalQty * $price;
                    
                    // Nilai Masuk
                    $nilaiMasuk = $masuk * $price;

                    // Stok Akhir (end of this day)
                    $stokAkhirQty = $stokAwalQty + $masuk - $keluar;
                    $nilaiAkhir = $stokAkhirQty * $price;

                    $totalAwal += $nilaiAwal;
                    $totalMasuk += $nilaiMasuk;
                    $grandTotalPersediaan += $nilaiAkhir;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $m->nama_material }}</td>
                    
                    <td>{{ $stokAwalQty != 0 ? number_format($stokAwalQty, 3, ',', '.') : '-' }}</td>
                    <td>{{ $m->satuan }}</td>
                    <td style="text-align: right;">Rp {{ number_format($nilaiAwal, 0, ',', '.') }}</td>
                    
                    <td style="background-color: #e8f8f5; font-weight: bold;">{{ $masuk != 0 ? number_format($masuk, 3, ',', '.') : '-' }}</td>
                    <td style="background-color: #e8f8f5;">{{ $m->satuan }}</td>
                    <td style="background-color: #e8f8f5; text-align: right;">Rp {{ number_format($nilaiMasuk, 0, ',', '.') }}</td>
                    
                    <td style="background-color: #fdedec;">{{ $keluar != 0 ? number_format($keluar, 3, ',', '.') : '-' }}</td>
                    
                    <td style="background-color: #f4ecf7; font-weight: bold;">{{ $stokAkhirQty != 0 ? number_format($stokAkhirQty, 3, ',', '.') : '-' }}</td>
                    <td style="background-color: #f4ecf7;">{{ $m->satuan }}</td>
                    <td style="background-color: #f4ecf7; text-align: right; font-weight: bold;">Rp {{ number_format($nilaiAkhir, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total Stok Awal:</td>
                <td style="text-align: right; font-weight: bold;">Rp {{ number_format($totalAwal, 0, ',', '.') }}</td>
                <td colspan="2" style="text-align: right; font-weight: bold;">Total Material Masuk:</td>
                <td style="text-align: right; font-weight: bold;">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total Nilai Persediaan:</td>
                <td style="background-color: #d1f2eb; text-align: right; font-weight: bold;">Rp {{ number_format($grandTotalPersediaan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
