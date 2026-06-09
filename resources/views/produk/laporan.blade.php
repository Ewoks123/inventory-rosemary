@extends('layouts.material')
@section('title', 'Rosemary Nutrition - Laporan')

@push('styles')
    <style>
        @media print {
            @page {
                size: landscape;
                margin: 5mm;
            }
            body {
                transform: scale(0.5);
                transform-origin: top left;
                width: 200%;
            }
            .no-print {
                display: none !important;
            }
            .form-card {
                box-shadow: none;
                border: none;
                padding: 0;
            }
            .table-container {
                overflow: visible !important;
            }
            #masterReportTable {
                min-width: 100% !important;
            }
        }
    </style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        @php
        $reportRoute = $reportRoute ?? 'produks.report';
        $reportPdfRoute = $reportPdfRoute ?? 'produks.report.pdf';
        $reportExcelRoute = $reportExcelRoute ?? 'produks.report.excel';
        $reportBackRoute = $reportBackRoute ?? 'produks.index';
    @endphp
    <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;" class="no-print">
            <a href="{{ route($reportBackRoute) }}" class="button-link">&larr; Kembali</a>
            <form method="GET" action="{{ route($reportRoute) }}" style="display: flex; gap: 10px; align-items: center; margin: 0; flex-wrap: wrap;">
                <input type="date" name="start_date" value="{{ $startDate }}" style="padding: 8px; border-radius: 8px; border: 1px solid #ccc;" onchange="this.form.submit()" />
                <span> - </span>
                <input type="date" name="end_date" value="{{ $endDate }}" style="padding: 8px; border-radius: 8px; border: 1px solid #ccc;" onchange="this.form.submit()" />
                <button type="button" id="printReportBtn" class="btn-primary" style="padding: 8px 14px; border:none; border-radius:8px; cursor:pointer;">Cetak Laporan</button>
                <a href="{{ route($reportPdfRoute, ['start_date' => $startDate, 'end_date' => $endDate]) }}" style="padding: 8px 14px; border:none; border-radius:8px; cursor:pointer; text-decoration: none; background: #e74c3c; color: white;">Export PDF</a>
                <a href="{{ route($reportExcelRoute, ['start_date' => $startDate, 'end_date' => $endDate]) }}" style="padding: 8px 14px; border:none; border-radius:8px; cursor:pointer; text-decoration: none; background: #27ae60; color: white;">Export Excel</a>
            </form>
        </div>
        <div>
            <h2>Laporan Penjualan</h2>
            <p>Riwayat dan ringkasan penjualan.</p>
        </div>
    </div>

    <div class="summary-row" style="margin-top: 20px;">
        <div class="summary-card">
            <h3>Total Penjualan</h3>
            <p id="totalSales">{{ $sales->count() }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Item Terjual</h3>
            <p id="totalItems">{{ $sales->sum('jumlah_terjual') }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Pendapatan</h3>
            <p id="totalRevenue">Rp {{ number_format($sales->sum('total_harga'), 0, ',', '.') }}</p>
        </div>
        <div class="summary-card">
            <h3>Sisa Stok</h3>
            <p id="totalRemainingStock">{{ $produks->sum('stok_produk') }}</p>
        </div>
        <div class="summary-card">
            <h3>Nilai Sisa Stok</h3>
            <p id="totalRemainingValue">Rp {{ number_format($produks->sum(function($p) { return $p->stok_produk * $p->harga_produk; }), 0, ',', '.') }}</p>
        </div>
    </div>



    @php
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $daysCount = iterator_count($period);
        
        $grandTotalNilai = 0;
        $grandTotalProd = 0;
        $grandTotalSales = 0;
        $grandTotalStockAkhir = 0;

        $grandTotalProdByDate = [];
        $grandTotalSalesByDate = [];
        foreach($period as $date) {
            $grandTotalProdByDate[$date->format('Y-m-d')] = 0;
            $grandTotalSalesByDate[$date->format('Y-m-d')] = 0;
        }

        $prodByDate = [];
        foreach($produksis as $p) {
            $d = date('Y-m-d', strtotime($p->tanggal_produksi));
            if(!isset($prodByDate[$p->id_produk])) $prodByDate[$p->id_produk] = [];
            $prodByDate[$p->id_produk][$d] = ($prodByDate[$p->id_produk][$d] ?? 0) + $p->jumlah_produksi;
        }

        $salesByDate = [];
        foreach($sales as $s) {
            $d = date('Y-m-d', strtotime($s->tanggal_penjualan));
            if(!isset($salesByDate[$s->id_produk])) $salesByDate[$s->id_produk] = [];
            $salesByDate[$s->id_produk][$d] = ($salesByDate[$s->id_produk][$d] ?? 0) + $s->jumlah_terjual;
        }
    @endphp

    <div class="table-container" style="margin-top: 20px; overflow-x: auto;">
        <table id="masterReportTable" style="min-width: 1500px; border-collapse: collapse; font-size: 10px;">
            <thead>
                <tr>
                    <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">No</th>
                    <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">Nama Produk</th>
                    <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">Harga Satuan</th>
                    <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">Stock Awal</th>
                    <th colspan="{{ $daysCount }}" style="background-color: #27ae60; color: white; border: 1px solid #ddd; padding: 4px; text-align: center;">PRODUKSI</th>
                    <th rowspan="2" style="background-color: #27ae60; color: white; border: 1px solid #ddd; padding: 4px;">Total Prod</th>
                    <th colspan="{{ $daysCount }}" style="background-color: #e74c3c; color: white; border: 1px solid #ddd; padding: 4px; text-align: center;">PENJUALAN</th>
                    <th rowspan="2" style="background-color: #e74c3c; color: white; border: 1px solid #ddd; padding: 4px;">Total Penjualan</th>
                    <th rowspan="2" style="background-color: #f1c40f; color: #333; border: 1px solid #ddd; padding: 4px;">Stock Akhir</th>
                    <th rowspan="2" style="background-color: #f1c40f; color: #333; border: 1px solid #ddd; padding: 4px;">Total Nilai Stok</th>
                </tr>
                <tr id="dateHeaderRow">
                    @foreach($period as $date)
                        <th style="background-color: #27ae60; color: white; border: 1px solid #ddd; padding: 2px;">{{ $date->format('d') }}</th>
                    @endforeach
                    @foreach($period as $index => $date)
                        @php
                            $colors = ['#c0392b', '#d35400', '#e67e22', '#f39c12', '#e74c3c'];
                            $bg = ($index + 1) <= 7 ? $colors[0] : (($index + 1) <= 14 ? $colors[1] : (($index + 1) <= 21 ? $colors[2] : (($index + 1) <= 28 ? $colors[3] : $colors[4])));
                        @endphp
                        <th style="background-color: {{ $bg }}; color: white; border: 1px solid #ddd; padding: 2px;">{{ $date->format('d') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($produks as $index => $product)
                @php 
                    $totalProd = $produksis->where('id_produk', $product->id)->sum('jumlah_produksi');
                    $totalPenjualan = $sales->where('id_produk', $product->id)->sum('jumlah_terjual');
                    $stokAwal = $product->stok_produk - $totalProd + $totalPenjualan;
                @endphp
                <tr>
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #ddd; padding: 4px;">{{ $product->nama_produk }}</td>
                    <td style="border: 1px solid #ddd; padding: 4px;">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $stokAwal == 0 ? '-' : $stokAwal }}</td>
                    
                    @foreach($period as $date)
                        @php 
                            $d = $date->format('Y-m-d');
                            $qty = $prodByDate[$product->id][$d] ?? 0;
                            $grandTotalProdByDate[$d] += $qty;
                        @endphp
                        <td style="border: 1px solid #ddd; padding: 2px; text-align: center;">{{ $qty ?: '' }}</td>
                    @endforeach
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold;">{{ $totalProd }}</td>

                    @foreach($period as $idx => $date)
                        @php 
                            $d = $date->format('Y-m-d');
                            $qty = $salesByDate[$product->id][$d] ?? 0;
                            $grandTotalSalesByDate[$d] += $qty;
                            $bColors = ['#f5b7b1', '#f5cba7', '#fad7a1', '#fdebd0', '#fadbd8'];
                            $bg = ($idx + 1) <= 7 ? $bColors[0] : (($idx + 1) <= 14 ? $bColors[1] : (($idx + 1) <= 21 ? $bColors[2] : (($idx + 1) <= 28 ? $bColors[3] : $bColors[4])));
                        @endphp
                        <td style="border: 1px solid #ddd; padding: 2px; text-align: center; background-color: {{ $bg }};">{{ $qty ?: '' }}</td>
                    @endforeach
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold;">{{ $totalPenjualan }}</td>
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold; background-color: #fcf3cf;">{{ $product->stok_produk == 0 ? '-' : $product->stok_produk }}</td>
                    @php
                        $grandTotalProd += $totalProd;
                        $grandTotalSales += $totalPenjualan;
                        $grandTotalStockAkhir += $product->stok_produk;

                        $nilaiStok = $product->stok_produk * $product->harga_produk;
                        $grandTotalNilai += $nilaiStok;
                    @endphp
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold; background-color: #d1f2eb;">Rp {{ number_format($nilaiStok, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold; padding: 8px;">Grand Total:</td>
                    
                    @foreach($period as $date)
                        <td style="text-align: center; font-weight: bold; padding: 4px; background-color: #e8f8f5;">{{ $grandTotalProdByDate[$date->format('Y-m-d')] ?: '' }}</td>
                    @endforeach
                    <td style="text-align: center; font-weight: bold; padding: 8px; background-color: #d4efdf;">{{ $grandTotalProd ?: '0' }}</td>

                    @foreach($period as $date)
                        <td style="text-align: center; font-weight: bold; padding: 4px; background-color: #fdedec;">{{ $grandTotalSalesByDate[$date->format('Y-m-d')] ?: '' }}</td>
                    @endforeach
                    <td style="text-align: center; font-weight: bold; padding: 8px; background-color: #fadbd8;">{{ $grandTotalSales ?: '0' }}</td>

                    <td style="text-align: center; font-weight: bold; padding: 8px; background-color: #fcf3cf;">{{ $grandTotalStockAkhir ?: '0' }}</td>
                    <td style="text-align: center; font-weight: bold; padding: 8px; background-color: #d1f2eb;">Rp {{ number_format($grandTotalNilai, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
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