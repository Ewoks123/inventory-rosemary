@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Stok Produk')

@push('styles')
<style>
    .summary-row {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }
    .summary-card {
        flex: 1;
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #eee;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .summary-card h3 {
        margin: 0;
        color: #6d0f1b;
        font-size: 16px;
        font-weight: 600;
    }
    .summary-card p {
        margin: 10px 0 0;
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }
    .table-container {
        margin-top: 25px;
        background: white;
        border-radius: 12px;
        overflow: hidden;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th {
        background: #6d0f1b;
        color: white;
        text-align: left;
        padding: 12px 15px;
        font-weight: 600;
    }
    td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        color: #444;
    }
    .btn-action-edit {
        background: #f39c12;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
    }
    .btn-action-delete {
        background: #e74c3c;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }
    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .nav-buttons {
        display: flex;
        gap: 10px;
    }
    .nav-btn {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s;
    }
    .btn-tambah { background: #6d0f1b; color: white; }
    .btn-produksi { background: #27ae60; color: white; }
    .btn-penjualan { background: #f8f9fa; color: #333; border: 1px solid #ddd; }
    .btn-laporan { background: #f8f9fa; color: #333; border: 1px solid #ddd; }
    
    .btn-back {
        display: inline-block;
        background: #6d0f1b;
        color: white;
        padding: 10px 30px;
        border-radius: 6px;
        text-decoration: none;
        margin-top: 20px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="header-actions">
        <div>
            <h2>Stok Produk</h2>
            <p>Kelola produk jadi.</p>
        </div>
        <div class="nav-buttons">
            <a href="{{ route('produks.create') }}" class="nav-btn btn-tambah">+ Tambah Produk</a>
            <a href="{{ route('produksis.index') }}" class="nav-btn btn-produksi">Input Produksi</a>
            <a href="{{ route('penjualans.index') }}" class="nav-btn btn-penjualan">Input Penjualan</a>
            <a href="{{ route('produks.report') }}" class="nav-btn btn-laporan">Laporan</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <div class="summary-row">
        <div class="summary-card">
            <h3>Total Produk</h3>
            <p id="totalProducts">{{ $produks->count() }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Stok</h3>
            <p id="totalStock">{{ $produks->sum('stok_produk') }}</p>
        </div>
        <div class="summary-card">
            <h3>Nilai Stok</h3>
            @php 
                $nilaiTotal = $produks->sum(function($p) {
                    return $p->stok_produk * $p->harga_produk;
                });
            @endphp
            <p id="totalValue">Rp {{ number_format($nilaiTotal, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produks as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->nama_produk }}</td>
                    <td>{{ $p->kategori }}</td>
                    <td>{{ ($p->stok_produk == 0 && $p->harga_produk == 0) ? '-' : number_format($p->stok_produk, 0, '', '') . ' ' . $p->satuan }}</td>
                    <td>{{ ($p->stok_produk == 0 && $p->harga_produk == 0) ? '-' : 'Rp ' . number_format($p->harga_produk, 0, ',', '.') }}</td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="{{ route('produks.edit', $p->id) }}" class="btn-action-edit">Edit</a>
                            <form action="{{ route('produks.destroy', $p->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action-delete">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($produks->count() == 0)
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">Belum ada data produk.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<a href="{{ route('admin.inventory') }}" class="btn-back">Kembali</a>
@endsection