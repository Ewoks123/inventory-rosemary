@extends('layouts.material')
@section('title', 'Rosemary Nutrition - Summary Material')
@push('styles')
    <style>
        @media print {
            @page {
                size: landscape;
                margin: 5mm;
            }
            body {
                transform: scale(0.65);
                transform-origin: top left;
                width: 153%;
            }
            .no-print {
                display: none !important;
            }
            .container {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .main {
                margin: 0;
                padding: 0;
            }
            .form-card {
                box-shadow: none;
                border: none;
                padding: 0;
            }
            table {
                min-width: 100% !important;
            }
            .table-container {
                overflow: visible !important;
            }
        }
        .filter-section {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .filter-group label {
            font-size: 14px;
            font-weight: bold;
        }
        .filter-group select, .filter-group input {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .table-container table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        .table-container table th, .table-container table td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: center;
            font-size: 11px;
        }
        .table-container table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .table-container table td {
            vertical-align: middle;
            height: 20px;
        }
        .table-container table td:nth-child(2) {
            text-align: left;
            max-width: 150px;
        }
    </style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-actions no-print" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
            <a href="{{ route('materials.menu') }}" class="button-link">&larr; Kembali</a>
            <form method="GET" action="{{ route('materials.report') }}" style="display: flex; gap: 10px; align-items: center; margin: 0; flex-wrap: wrap;">
                <input type="date" name="start_date" value="{{ $startDate }}" style="padding: 8px; border-radius: 8px; border: 1px solid #ccc;" onchange="this.form.submit()" />
                <span> - </span>
                <input type="date" name="end_date" value="{{ $endDate }}" style="padding: 8px; border-radius: 8px; border: 1px solid #ccc;" onchange="this.form.submit()" />
                <button type="button" id="printReportBtn" class="btn-primary" style="padding: 8px 14px; border:none; border-radius:8px; cursor:pointer;">Cetak Laporan</button>
                <a href="{{ route('materials.report.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" style="padding: 8px 14px; border:none; border-radius:8px; cursor:pointer; text-decoration: none; background: #e74c3c; color: white;">Export PDF</a>
                <a href="{{ route('materials.report.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" style="padding: 8px 14px; border:none; border-radius:8px; cursor:pointer; text-decoration: none; background: #27ae60; color: white;">Export Excel</a>
            </form>
        </div>
        <div>
            <h2>Summary Material</h2>
            <p>Filter dan cetak laporan penggunaan material.</p>
        </div>
    </div>

    @php
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $daysCount = iterator_count($period);
        $startOfMonth = \Carbon\Carbon::parse($startDate)->startOfMonth()->format('Y-m-d');
        $endOfMonth = \Carbon\Carbon::parse($startDate)->endOfMonth()->format('Y-m-d');
        $isFullMonth = ($startDate === $startOfMonth && $endDate === $endOfMonth) || $daysCount >= 28;
        
        $grandTotalNilai = 0;

        $totalItems = $materials->count();
        $totalMasuk = 0;
        $totalKeluar = 0;
        
        $inByDate = [];
        $outByDate = [];
        foreach($materials as $m) {
            $inByDate[$m->id] = [];
            $outByDate[$m->id] = [];
            foreach($m->logs as $log) {
                $d = date('Y-m-d', strtotime($log->date));
                if ($log->type == 'in') {
                    $inByDate[$m->id][$d] = ($inByDate[$m->id][$d] ?? 0) + $log->quantity;
                    $totalMasuk += $log->quantity;
                } else {
                    $outByDate[$m->id][$d] = ($outByDate[$m->id][$d] ?? 0) + $log->quantity;
                    $totalKeluar += $log->quantity;
                }
            }
        }
        
        $sisaStok = $materials->sum('stok_material');
        $nilaiStok = $materials->sum(function($m) { 
            $price = is_numeric($m->supplier) ? (float) $m->supplier : 0;
            return $m->stok_material * $price; 
        });
    @endphp

    <div class="summary-row no-print" style="margin-top: 20px;">
        <div class="summary-card">
            <h3>Total Item</h3>
            <p>{{ $totalItems }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Masuk</h3>
            <p>{{ rtrim(rtrim(number_format($totalMasuk, 3, ',', '.'), '0'), ',') ?: '0' }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Keluar</h3>
            <p>{{ rtrim(rtrim(number_format($totalKeluar, 3, ',', '.'), '0'), ',') ?: '0' }}</p>
        </div>
        <div class="summary-card">
            <h3>Sisa Stok (Semua)</h3>
            <p>{{ rtrim(rtrim(number_format($sisaStok, 3, ',', '.'), '0'), ',') ?: '0' }}</p>
        </div>
        <div class="summary-card">
            <h3>Nilai Stok</h3>
            <p>Rp {{ number_format($nilaiStok, 0, ',', '.') }}</p>
        </div>
    </div>

    <div id="reportContent">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">LAPORAN MATERIAL MASUK / KELUAR</h2>
            <h3 style="margin: 5px 0;">Rosemary Nutrition</h3>
            <p id="reportPeriodText">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>

        @if($isFullMonth)
        <!-- FULL MONTH MATRIX VIEW -->
        <div class="table-container" style="overflow-x:auto;">
            <table id="masterReportTable" style="min-width: 3000px; border-collapse: collapse; font-size: 11px;">
                <thead>
                    <tr>
                        <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">No</th>
                        <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">Nama Material</th>
                        <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">Satuan</th>
                        <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">Harga Satuan</th>
                        <th colspan="{{ $daysCount }}" style="background-color: #e74c3c; color: white; border: 1px solid #ddd; padding: 4px; text-align: center;">BARANG MASUK</th>
                        <th rowspan="2" style="background-color: #e74c3c; color: white; border: 1px solid #ddd; padding: 4px;">Total Masuk</th>
                        <th colspan="{{ $daysCount }}" style="background-color: #27ae60; color: white; border: 1px solid #ddd; padding: 4px; text-align: center;">PRODUKSI (KELUAR)</th>
                        <th rowspan="2" style="background-color: #27ae60; color: white; border: 1px solid #ddd; padding: 4px;">Total Keluar</th>
                        <th rowspan="2" style="background-color: #f39c12; color: white; border: 1px solid #ddd; padding: 4px;">Sisa Stok</th>
                        <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">Total Nilai Stok</th>
                    </tr>
                    <tr id="dateHeaderRowMaster">
                        @foreach($period as $date)
                            <th style="background-color: #e74c3c; color: white; border: 1px solid #ddd; padding: 2px;">{{ $date->format('d/m') }}</th>
                        @endforeach
                        @foreach($period as $date)
                            <th style="background-color: #27ae60; color: white; border: 1px solid #ddd; padding: 2px;">{{ $date->format('d/m') }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $grandTotalMasukMatrix = 0;
                        $grandTotalKeluarMatrix = 0;
                        $grandTotalSisaMatrix = 0;
                    @endphp
                    @foreach($materials as $index => $m)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $index + 1 }}</td>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: left;">{{ $m->nama_material }}</td>
                        <td style="border: 1px solid #ddd; padding: 4px;">{{ $m->satuan }}</td>
                        @php 
                            $hargaSatuan = is_numeric($m->supplier) ? (float) $m->supplier : 0;
                            $totalNilaiStok = $m->stok_material * $hargaSatuan;
                            $grandTotalNilai += $totalNilaiStok;
                        @endphp
                        <td style="border: 1px solid #ddd; padding: 4px;">Rp {{ number_format($hargaSatuan, 0, ',', '.') }}</td>

                        @php $totalInRow = 0; @endphp
                        @foreach($period as $date)
                            @php 
                                $qty = $inByDate[$m->id][$date->format('Y-m-d')] ?? 0;
                                $totalInRow += $qty;
                            @endphp
                            <td style="border: 1px solid #ddd; padding: 2px; text-align: center;">{{ $qty ? rtrim(rtrim(number_format($qty, 3, ',', '.'), '0'), ',') : '' }}</td>
                        @endforeach
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold; background-color: #f8f9fa;">{{ rtrim(rtrim(number_format($totalInRow, 3, ',', '.'), '0'), ',') ?: '0' }}</td>

                        @php $totalOutRow = 0; @endphp
                        @foreach($period as $date)
                            @php 
                                $qty = $outByDate[$m->id][$date->format('Y-m-d')] ?? 0;
                                $totalOutRow += $qty;
                            @endphp
                            <td style="border: 1px solid #ddd; padding: 2px; text-align: center;">{{ $qty ? rtrim(rtrim(number_format($qty, 3, ',', '.'), '0'), ',') : '' }}</td>
                        @endforeach
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold; background-color: #f8f9fa;">{{ rtrim(rtrim(number_format($totalOutRow, 3, ',', '.'), '0'), ',') ?: '0' }}</td>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold; background-color: #fcf3cf;">
                            {{ rtrim(rtrim(number_format($m->stok_material, 3, ',', '.'), '0'), ',') ?: '0' }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 4px; background-color: #d1f2eb; font-weight: bold;">Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $grandTotalMasukMatrix += $totalInRow;
                        $grandTotalKeluarMatrix += $totalOutRow;
                        $grandTotalSisaMatrix += $m->stok_material;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="{{ 4 + $daysCount }}" style="text-align: right; font-weight: bold; padding: 8px;">Grand Total:</td>
                        <td style="font-weight: bold; padding: 8px; background-color: #f8f9fa; text-align: center;">{{ rtrim(rtrim(number_format($grandTotalMasukMatrix, 3, ',', '.'), '0'), ',') }}</td>
                        <td colspan="{{ $daysCount }}"></td>
                        <td style="font-weight: bold; padding: 8px; background-color: #f8f9fa; text-align: center;">{{ rtrim(rtrim(number_format($grandTotalKeluarMatrix, 3, ',', '.'), '0'), ',') }}</td>
                        <td style="font-weight: bold; padding: 8px; background-color: #fcf3cf; text-align: center;">{{ rtrim(rtrim(number_format($grandTotalSisaMatrix, 3, ',', '.'), '0'), ',') }}</td>
                        <td style="font-weight: bold; padding: 8px; background-color: #d1f2eb; text-align: center;">Rp {{ number_format($grandTotalNilai, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <!-- SIMPLIFIED VIEW FOR SHORT DATE RANGE -->
        <div class="table-container" style="overflow-x: auto;">
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
                        $grandTotalPersediaan = 0;
                        $totalMasukKg = 0;
                        $totalNilaiMasuk = 0;
                        $totalPengeluaranKg = 0;
                        $totalStokAkhirKg = 0;
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

                            $totalMasukKg += $masuk;
                            $totalNilaiMasuk += $nilaiMasuk;
                            $totalPengeluaranKg += $keluar;
                            $totalStokAkhirKg += $stokAkhirQty;
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
                        <td colspan="2" style="border: 1px solid #ddd; text-align: right; font-weight: bold; padding: 8px;">Grand Total:</td>
                        <td style="border: 1px solid #ddd; text-align: center; font-weight: bold; padding: 8px;">{{ rtrim(rtrim(number_format($totalMasukKg, 3, ',', '.'), '0'), ',') }}</td>
                        <td style="border: 1px solid #ddd;"></td>
                        <td style="border: 1px solid #ddd; text-align: right; font-weight: bold; padding: 8px;">Rp {{ number_format($totalNilaiMasuk, 0, ',', '.') }}</td>
                        <td style="border: 1px solid #ddd; text-align: center; font-weight: bold; padding: 8px;">{{ rtrim(rtrim(number_format($totalPengeluaranKg, 3, ',', '.'), '0'), ',') }}</td>
                        <td style="border: 1px solid #ddd; text-align: center; font-weight: bold; padding: 8px;">{{ rtrim(rtrim(number_format($totalStokAkhirKg, 3, ',', '.'), '0'), ',') }}</td>
                        <td style="border: 1px solid #ddd;"></td>
                        <td style="border: 1px solid #ddd; background-color: #d1f2eb; text-align: right; font-weight: bold; padding: 8px;">Rp {{ number_format($grandTotalPersediaan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.getElementById('printReportBtn')?.addEventListener('click', () => {
        window.print();
    });
</script>
@endpush
