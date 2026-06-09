@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Edit Material')

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        <a href="{{ route('materials.actual') }}" class="button-link">&larr; Kembali</a>
        <div>
            <h2>Edit Material</h2>
            <p>Isi form untuk memperbarui data material.</p>
        </div>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #f5c6cb; width: 100%;">
            <strong>Gagal:</strong> Kolom semua wajib di isi dengan benar.
        </div>
    @endif

    <form action="{{ route('materials.update', $material->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-grid" style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 150px;">
                <label for="tanggal_masuk">Tanggal</label>
                <input id="tanggal_masuk" name="tanggal_masuk" type="date" value="{{ old('tanggal_masuk', $material->tanggal_masuk) }}" readonly style="background-color: #f0f0f0;" required />
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label for="nama_material">Nama Material</label>
                <input id="nama_material" type="text" value="{{ $material->nama_material }}" readonly style="background-color: #f0f0f0;" />
                <input type="hidden" name="nama_material" value="{{ $material->nama_material }}">
                <input type="hidden" name="kode_material" value="{{ $material->kode_material }}">
                <input type="hidden" name="jenis_material" value="{{ $material->jenis_material }}">
            </div>
            <div style="flex: 1; min-width: 100px;">
                <label for="stok_material">Stock</label>
                <input id="stok_material" name="stok_material" type="number" step="0.001" value="{{ old('stok_material', $material->stok_material) }}" required />
            </div>
            <div style="flex: 1; min-width: 120px;">
                <label for="satuan">Satuan</label>
                <select id="satuan" disabled style="background-color: #f0f0f0;" required>
                    <option value="kg" {{ old('satuan', $material->satuan) == 'kg' ? 'selected' : '' }}>kg</option>
                    <option value="gram" {{ old('satuan', $material->satuan) == 'gram' ? 'selected' : '' }}>gram</option>
                    <option value="pcs" {{ old('satuan', $material->satuan) == 'pcs' ? 'selected' : '' }}>pcs</option>
                </select>
                <input type="hidden" name="satuan" value="{{ $material->satuan }}">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label for="harga_produk">Suplay/Harga</label>
                <input id="harga_produk" name="supplier" type="text" value="{{ old('supplier', number_format((float)($material->supplier ?? 0), 0, ',', '.')) }}" placeholder="Harga suplay" oninput="formatRupiah(this)" />
            </div>
        </div>
        <div class="form-actions" style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="submit" class="btn-primary" style="background-color: #6d0f1b;">Simpan Perubahan</button>
            <button type="reset" class="btn-secondary" style="background-color: #f0f0f0; color: #333; border: 1px solid #ddd;">Bersihkan Form</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Format Rupiah / Ribuan
    function formatRupiah(input) {
        let value = input.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        input.value = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    }
</script>
@endpush