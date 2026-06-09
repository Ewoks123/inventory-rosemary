@extends('layouts.admin')

@section('title', 'Rosemary Nutrition - Dashboard')

@section('content')
<!-- Dashboard View -->
<div id="dashboard-view">
    
    <div class="cards">
        <div class="card">
            <h3>Total Produk</h3>
            <p id="totalProduk">{{ $products->count() }}</p>
        </div>
        <div class="card">
            <h3>Stok Material</h3>
            <p>{{ \App\Models\Material::count() }}</p>
        </div>
        <div class="card">
            <h3>Stok Habis</h3>
            <p>{{ $totalStokHabis }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <h2>Daftar Produk</h2>
        <table id="productTable">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Nama Produk</th>
                    <th onclick="sortTable(1)">Kategori</th>
                    <th onclick="sortTable(2)">Stok</th>
                    <th onclick="sortTable(3)">Harga</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                <tr>
                    <td>{{ $p->nama_produk }}</td>
                    <td>{{ $p->kategori }}</td>
                    <td>{{ ($p->stok_produk == 0 && $p->harga_produk == 0) ? '-' : $p->stok_produk }}</td>
                    <td>{{ ($p->stok_produk == 0 && $p->harga_produk == 0) ? '-' : 'Rp ' . number_format($p->harga_produk, 0, ',', '.') }}</td>
                    <td>
                        @if($p->stok_produk == 0 && $p->harga_produk == 0)
                            <span style="color: #999;">-</span>
                        @elseif($p->stok_produk > 10)
                            <span style="color: green; font-weight: bold;">Tersedia</span>
                        @elseif($p->stok_produk > 0)
                            <span style="color: #e67e22; font-weight: bold;">Peringatan kurang dari 10</span>
                        @else
                            <span style="color: red; font-weight: bold;">Habis</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection