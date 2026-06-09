@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Input Produksi')

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        <a href="{{ route('produks.index') }}" class="button-link">&larr; Kembali</a>
        <div>
            <h2>Input Produksi</h2>
            <p>Tambah jumlah produksi (stok masuk).</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #c3e6cb; width: 100%;">
            <strong>Sukses:</strong> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #f5c6cb; width: 100%;">
            <strong>Gagal:</strong> 
            <ul style="margin-left: 20px; margin-top: 5px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('produksis.store') }}" method="POST">
        @csrf
        <div class="form-grid" style="margin-top: 20px; display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label for="productionDate">Tanggal</label>
                <input id="productionDate" name="tanggal_produksi" type="date" required value="{{ date('Y-m-d') }}" />
            </div>
            <div style="flex: 2; min-width: 250px;">
                <label for="productionProduct">Pilih Produk</label>
                <select id="productionProduct" name="id_produk" required>
                    <option value="">Pilih Produk...</option>
                    @foreach($produks as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_produk }} (Stok: {{ $p->stok_produk }} {{ $p->satuan }})</option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label for="productionAmount">Jumlah Produksi</label>
                <input id="productionAmount" name="jumlah_produksi" type="number" min="1" required placeholder="Jumlah produksi" />
            </div>
        </div>
        <div class="form-actions" style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="submit" class="btn-primary" style="background-color: #6d0f1b;">Simpan Produksi</button>
            <button type="reset" class="btn-secondary" style="background-color: #f0f0f0; color: #333; border: 1px solid #ddd;">Bersihkan Form</button>
        </div>
    </form>
</div>
@endsection