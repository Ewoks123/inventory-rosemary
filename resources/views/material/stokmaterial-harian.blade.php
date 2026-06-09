@extends('layouts.material')
@section('title', 'Rosemary Nutrition - Laporan Harian Material')

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;" class="no-print">
            <a href="{{ route('materials.index') }}" class="button-link">&larr; Kembali</a>
            <form method="GET" action="{{ route('materials.report') }}" style="display: flex; gap: 10px; align-items: center; margin: 0; flex-wrap: wrap;">
                <input type="date" name="start_date" value="{{ $startDate }}" style="padding: 8px; border-radius: 8px; border: 1px solid #ccc;" onchange="this.form.submit()" />
                <span> - </span>
                <input type="date" name="end_date" value="{{ $endDate }}" style="padding: 8px; border-radius: 8px; border: 1px solid #ccc;" onchange="this.form.submit()" />
                <button type="button" onclick="window.print()" class="btn-primary" style="padding: 8px 14px; border:none; border-radius:8px; cursor:pointer;">Cetak Laporan</button>
                <a href="{{ route('materials.report.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" style="padding: 8px 14px; border:none; border-radius:8px; cursor:pointer; text-decoration: none; background: #e74c3c; color: white;">Export PDF</a>
                <a href="{{ route('materials.report.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" style="padding: 8px 14px; border:none; border-radius:8px; cursor:pointer; text-decoration: none; background: #27ae60; color: white;">Export Excel</a>
            </form>
        </div>
        <div>
            <h2>Laporan Harian Material</h2>
            <p>Rincian mutasi dan nilai persediaan pada tanggal {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }}.</p>
        </div>
    </div>

    <div class="table-container" style="margin-top: 20px; overflow-x: auto;">
        <table id="masterReportTable" style="min-width: 1200px; border-collapse: collapse; font-size: 11px; width: 100%;">
            <thead>
                <tr>
                    <th style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 6px;">No</th>
                    <th style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 6px;">Nama Material</th>
                    
                    <th style="background-color: #27ae60; color: white; border: 1px solid #ddd; padding: 6px;">Material Masuk</th>
                    <th style="background-color: #27ae60; color: white; border: 1px solid #ddd; padding: 6px;">Satuan</th>
                    <th style="background-color: #27ae60; color: white; border: 1px solid #ddd; padding: 6px;">Nilai Material Masuk</th>
                    
                    <th style="background-color: #e74c3c; color: white; border: 1px solid #ddd; padding: 6px;">Pengeluaran</th>
                    
                    <th style="background-color: #8e44ad; color: white; border: 1px solid #ddd; padding: 6px;">Stok Akhir</th>
                    <th style="background-color: #8e44ad; color: white; border: 1px solid #ddd; padding: 6px;">Satuan</th>
                    <th style="background-color: #8e44ad; color: white; border: 1px solid #ddd; padding: 6px;">Nilai Stok Akhir</th>
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
                        
                        $masuk = $m->logs->where('type', 'in')->sum('quantity');
                        $keluar = $m->logs->where('type', 'out')->sum('quantity');

                        $stokAwalQty = $stokAwalMap[$m->id] ?? 0;
                        
                        $nilaiMasuk = $masuk * $price;

                        $stokAkhirQty = $stokAwalQty + $masuk - $keluar;
                        $nilaiAkhir = $stokAkhirQty * $price;

                        $totalMasuk += $masuk;
                        $grandTotalPersediaan += $nilaiAkhir;
                    @endphp
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $index + 1 }}</td>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: left;">{{ $m->nama_material }}</td>
                        
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center; background-color: #e8f8f5; font-weight: bold;">{{ $masuk != 0 ? rtrim(rtrim(number_format($masuk, 3, ',', '.'), '0'), ',') : '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center; background-color: #e8f8f5;">{{ $m->satuan }}</td>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: right; background-color: #e8f8f5;">Rp {{ number_format($nilaiMasuk, 0, ',', '.') }}</td>
                        
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center; background-color: #fdedec;">{{ $keluar != 0 ? rtrim(rtrim(number_format($keluar, 3, ',', '.'), '0'), ',') : '-' }}</td>
                        
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center; background-color: #f4ecf7; font-weight: bold;">{{ $stokAkhirQty != 0 ? rtrim(rtrim(number_format($stokAkhirQty, 3, ',', '.'), '0'), ',') : '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center; background-color: #f4ecf7;">{{ $m->satuan }}</td>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: right; background-color: #f4ecf7; font-weight: bold;">Rp {{ number_format($nilaiAkhir, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="border: 1px solid #ddd; text-align: right; font-weight: bold; padding: 8px;">Total Material Masuk:</td>
                    <td style="border: 1px solid #ddd; text-align: center; font-weight: bold; padding: 8px;">{{ rtrim(rtrim(number_format($totalMasuk, 3, ',', '.'), '0'), ',') }} Kg</td>
                    <td colspan="2" style="border: 1px solid #ddd; text-align: right; font-weight: bold; padding: 8px;">Grand Total Nilai Persediaan:</td>
                    <td style="border: 1px solid #ddd; background-color: #d1f2eb; text-align: right; font-weight: bold; padding: 8px;">Rp {{ number_format($grandTotalPersediaan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
