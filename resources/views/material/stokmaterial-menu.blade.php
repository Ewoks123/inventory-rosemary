@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Pilih Stok Material')

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        <div style="display: flex; justify-content: space-between; width: 100%;">
            <a href="{{ route('admin.inventory') }}" class="button-link">&larr; Kembali</a>
            <button id="resetDataBtn" style="background-color: #dc3545; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Reset Data</button>
        </div>
        <div>
            <h2>Pilih Jenis Stok</h2>
            <p>Pilih antara Produksi Harian atau Stok Material.</p>
        </div>
    </div>

    <div class="summary-row" style="margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
        <a href="{{ route('materials.actual') }}" class="button-link" style="display: block; text-align: center;">Stok Material</a>
        <a href="{{ route('materials.pharian') }}" class="button-link" style="display: block; text-align: center;">Produksi Harian</a>
        <a href="{{ route('materials.report') }}" class="button-link" style="display: block; text-align: center; background-color: #28a745;">Summary</a>
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