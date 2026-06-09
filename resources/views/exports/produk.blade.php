<!DOCTYPE html>
<html>
<head>
    <title>Laporan Produk</title>
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
    <table style="border-collapse: collapse; font-size: 8px; width: 100%;">
        <thead>
            <tr>
                <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #000; padding: 4px;">No</th>
                <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #000; padding: 4px;">Nama Produk</th>
                <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #000; padding: 4px;">Harga Satuan</th>
                <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #000; padding: 4px;">Stock Awal</th>
                <th colspan="{{ $daysCount }}" style="background-color: #27ae60; color: white; border: 1px solid #000; padding: 4px; text-align: center;">PRODUKSI</th>
                <th rowspan="2" style="background-color: #27ae60; color: white; border: 1px solid #000; padding: 4px;">Total Prod</th>
                <th colspan="{{ $daysCount }}" style="background-color: #e74c3c; color: white; border: 1px solid #000; padding: 4px; text-align: center;">PENJUALAN</th>
                <th rowspan="2" style="background-color: #e74c3c; color: white; border: 1px solid #000; padding: 4px;">Total Penjualan</th>
                <th rowspan="2" style="background-color: #f1c40f; color: #333; border: 1px solid #000; padding: 4px;">Stock Akhir</th>
                <th rowspan="2" style="background-color: #f1c40f; color: #333; border: 1px solid #000; padding: 4px;">Total Nilai Stok</th>
            </tr>
            <tr>
                @foreach($period as $date)
                    <th style="background-color: #27ae60; color: white; border: 1px solid #000; padding: 2px;">{{ $date->format('d/m') }}</th>
                @endforeach
                @foreach($period as $index => $date)
                    @php
                        $colors = ['#c0392b', '#d35400', '#e67e22', '#f39c12', '#e74c3c'];
                        $bg = ($index + 1) <= 7 ? $colors[0] : (($index + 1) <= 14 ? $colors[1] : (($index + 1) <= 21 ? $colors[2] : (($index + 1) <= 28 ? $colors[3] : $colors[4])));
                    @endphp
                    <th style="background-color: {{ $bg }}; color: white; border: 1px solid #000; padding: 2px;">{{ $date->format('d/m') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($produks as $index => $product)
            @php 
                $totalProd = $produksis->where('id_produk', $product->id)->sum('jumlah_produksi');
                $totalPenjualan = $sales->where('id_produk', $product->id)->sum('jumlah_terjual');
                $stokAwal = $product->stok_produk - $totalProd + $totalPenjualan;
                $nilaiStok = $product->stok_produk * $product->harga_produk;
                $grandTotalNilai += $nilaiStok;
            @endphp
            <tr>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000; padding: 4px;">{{ $product->nama_produk }}</td>
                <td style="border: 1px solid #000; padding: 4px;">{{ $product->harga_produk }}</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $stokAwal == 0 ? '-' : $stokAwal }}</td>
                
                @foreach($period as $date)
                    @php 
                        $qty = $prodByDate[$product->id][$date->format('Y-m-d')] ?? 0;
                    @endphp
                    <td style="border: 1px solid #000; padding: 2px; text-align: center;">{{ $qty ?: '' }}</td>
                @endforeach
                <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">{{ $totalProd }}</td>

                @foreach($period as $idx => $date)
                    @php 
                        $qty = $salesByDate[$product->id][$date->format('Y-m-d')] ?? 0;
                        $bColors = ['#f5b7b1', '#f5cba7', '#fad7a1', '#fdebd0', '#fadbd8'];
                        $bg = ($idx + 1) <= 7 ? $bColors[0] : (($idx + 1) <= 14 ? $bColors[1] : (($idx + 1) <= 21 ? $bColors[2] : (($idx + 1) <= 28 ? $bColors[3] : $bColors[4])));
                    @endphp
                    <td style="border: 1px solid #000; padding: 2px; text-align: center; background-color: {{ $bg }};">{{ $qty ?: '' }}</td>
                @endforeach
                <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">{{ $totalPenjualan }}</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold; background-color: #fcf3cf;">{{ $product->stok_produk == 0 ? '-' : $product->stok_produk }}</td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold; background-color: #d1f2eb;">{{ $nilaiStok }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold; padding: 8px;">Grand Total Nilai Seluruh Stok:</td>
                <td colspan="{{ ($daysCount * 2) + 3 }}"></td>
                <td style="font-weight: bold; padding: 8px; background-color: #d1f2eb; text-align: center;">{{ $grandTotalNilai }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
