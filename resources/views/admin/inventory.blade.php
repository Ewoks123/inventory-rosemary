@extends('layouts.admin')

@section('title', 'Rosemary Nutrition - Inventory')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/material.css') }}">
@endpush

@section('content')
<div id="inventory-view">
    <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
        <button id="resetDataBtn" style="background-color: #dc3545; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Reset Data</button>
    </div>
    <div class="inventory-grid">
        <div class="inventory-card">
            <h2>Stok Material</h2>
            <p>Ringkasan stok bahan baku.</p>
            <div class="inner-box">
                <a href="{{ route('materials.menu') }}">
                    <img src="{{ asset('scr/materialros.png') }}" alt="Logo" class="inventory-placeholder-logo">
                </a>
            </div>
            <a href="{{ route('materials.menu') }}" class="button-link" style="display: block; width: 100%; margin-top: 16px; text-align: center;">Lihat</a>
        </div>

        <div class="inventory-card">
            <h2>Stok Produk</h2>
            <p>Ringkasan stok produk.</p>
            <div class="inner-box">
                <a href="{{ route('produks.index') }}">
                    <img src="{{ asset('scr/stokproduk.png') }}" alt="Logo" class="inventory-placeholder-logo">
                </a>
            </div>
            <a href="{{ route('produks.index') }}" class="button-link" style="display: block; width: 100%; margin-top: 16px; text-align: center;">Lihat</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('resetDataBtn')?.addEventListener('click', () => {
        if (confirm('Apakah Anda yakin ingin mereset semua data? Tindakan ini tidak dapat dibatalkan.')) {
            localStorage.removeItem('inventoryStockMaterials');
            localStorage.removeItem('productionDailyMaterials');
            localStorage.removeItem('inventoryProducts');
            localStorage.removeItem('inventorySales');
            localStorage.removeItem('productionRecords');
            localStorage.removeItem('excel_data_loaded_v3');
            alert('Semua data telah direset.');
            location.reload();
        }
    });
</script>
@endpush
