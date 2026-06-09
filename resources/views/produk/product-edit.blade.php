@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Edit Produk')

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        <a href="{{ route('produks.index') }}" class="button-link">&larr; Kembali</a>
        <div>
            <h2>Edit Produk</h2>
            <p>Perbarui informasi produk: <strong>{{ $produk->nama_produk }}</strong></p>
        </div>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #f5c6cb; width: 100%;">
            <strong>Gagal:</strong> Mohon periksa kembali inputan Anda.
        </div>
    @endif

    <form action="{{ route('produks.update', $produk->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-grid" style="margin-top: 20px;">
            <div>
                <label for="productName">Nama Produk</label>
                <input id="productName" type="text" value="{{ $produk->nama_produk }}" readonly style="background-color: #f0f0f0;" />
                <input type="hidden" name="nama_produk" value="{{ $produk->nama_produk }}">
            </div>
            <div>
                <label for="productCategory">Kategori</label>
                <select id="productCategory" name="kategori" required>
                    <option value="Makanan" {{ old('kategori', $produk->kategori) == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                    <option value="Minuman" {{ old('kategori', $produk->kategori) == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="Lainnya" {{ old('kategori', $produk->kategori) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div>
                <label for="productStock">Stok Saat Ini</label>
                <input id="productStock" name="stok_produk" type="number" min="0" value="{{ old('stok_produk', $produk->stok_produk) }}" required />
            </div>
            <div>
                <label for="productPrice">Harga Jual (Rp)</label>
                <input id="productPrice" name="harga_produk" type="text" value="{{ number_format(old('harga_produk', $produk->harga_produk), 0, ',', '.') }}" required oninput="formatRupiah(this)" />
            </div>
            <div>
                <label for="productUnit">Satuan</label>
                <select id="productUnit" name="satuan" required>
                    <option value="pcs" {{ old('satuan', $produk->satuan) == 'pcs' ? 'selected' : '' }}>pcs</option>
                    <option value="box" {{ old('satuan', $produk->satuan) == 'box' ? 'selected' : '' }}>box</option>
                    <option value="kg" {{ old('satuan', $produk->satuan) == 'kg' ? 'selected' : '' }}>kg</option>
                </select>
            </div>
        </div>
        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Format Rupiah
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
