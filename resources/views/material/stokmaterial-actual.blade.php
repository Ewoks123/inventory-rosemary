@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Stok Material')

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
        <a href="{{ route('materials.menu') }}" class="button-link">&larr; Kembali</a>
        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
            <div>
                <h2>Stok Material</h2>
                <p>Tabel ringkas stok material.</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('materials.addin') }}" class="btn-secondary" style="padding: 8px 15px; text-decoration: none; border-radius: 8px;">+ Material Masuk</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; width: 100%;">
            {{ session('success') }}
        </div>
    @endif

    <div class="summary-row" style="margin-top: 20px;">
        <div class="summary-card">
            <h3>Total Material</h3>
            <p id="summaryTotal">{{ $materials->count() }} Items</p>
        </div>
        <div class="summary-card">
            <h3>Material Masuk</h3>
            <p id="summaryIn">{{ rtrim(rtrim(number_format($totalIn, 3, ',', '.'), '0'), ',') }}</p>
        </div>
        <div class="summary-card">
            <h3>Material Keluar</h3>
            <p id="summaryOut">{{ rtrim(rtrim(number_format($totalOut, 3, ',', '.'), '0'), ',') }}</p>
        </div>
        <div class="summary-card">
            <h3>Sisa Stok</h3>
            <p id="summarySisa">{{ rtrim(rtrim(number_format($materials->sum('stok_material'), 3, ',', '.'), '0'), ',') }}</p>
            <small style="color: #2980b9;">Total Unit Tersedia</small>
        </div>
        <div class="summary-card">
            <h3>Total Nilai Stok</h3>
            <p id="summarySisaHarga" style="font-size: 1.2rem; margin-top: 10px;">Rp {{ number_format($materials->sum(function($m) { return $m->stok_material * (float)$m->supplier; }), 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="table-container" style="margin-top: 20px; overflow-x:auto;">
        <table id="stockTable" style="min-width:950px;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Material</th>
                    <th>Material Masuk</th>
                    <th>Material Keluar</th>
                    <th>Sisa Stok</th>
                    <th>Satuan</th>
                    <th>Harga Supply</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materials as $index => $m)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $m->nama_material }}</td>
                    <td>{{ rtrim(rtrim(number_format($m->total_in ?? 0, 3, ',', '.'), '0'), ',') ?: '0' }}</td>
                    <td>{{ rtrim(rtrim(number_format($m->total_out ?? 0, 3, ',', '.'), '0'), ',') ?: '0' }}</td>
                    <td style="font-weight: bold; color: {{ $m->stok_material <= 10 ? '#e67e22' : 'green' }};">
                        {{ rtrim(rtrim(number_format($m->stok_material, 3, ',', '.'), '0'), ',') ?: '0' }}
                    </td>
                    <td>{{ $m->satuan }}</td>
                    <td>Rp {{ number_format((float)($m->supplier ?? 0), 0, ',', '.') }}</td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="{{ route('materials.edit', $m->id) }}" class="btn-primary" style="padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px; background-color: #f39c12;">Edit</a>
                            <form action="{{ route('materials.destroy', $m->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus material ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-primary" style="padding: 5px 10px; border: none; border-radius: 5px; font-size: 12px; background-color: #e74c3c; cursor: pointer;">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/material.js') }}"></script>
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('produksiDropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('produksiDropdown');
        const button = event.target.closest('button');
        if (!button && dropdown) {
            dropdown.style.display = 'none';
        }
    });
</script>
@endpush