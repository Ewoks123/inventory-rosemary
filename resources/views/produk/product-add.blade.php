@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Tambah Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        <a href="{{ route('produks.index') }}" class="button-link">&larr; Kembali</a>
        <div>
            <h2>Tambah Produk</h2>
            <p>Isi form untuk menambahkan produk baru ke sistem.</p>
        </div>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #f5c6cb; width: 100%;">
            <strong>Gagal:</strong> Kolom semua wajib di isi dengan benar.
        </div>
    @endif

    <form action="{{ route('produks.store') }}" method="POST">
        @csrf
        <div class="form-grid" style="margin-top: 20px;">
            <div>
                <label for="productName">Nama Produk</label>
                <input list="productList" id="productName" name="nama_produk" required placeholder="Pilih atau ketik nama produk baru" autocomplete="off" value="{{ old('nama_produk') }}" />
                <datalist id="productList">
                    @foreach($allProduks as $pName)
                        <option value="{{ $pName }}">
                    @endforeach
                </datalist>
            </div>
            <div>
                <label for="productCategory">Kategori</label>
                <select id="productCategory" name="kategori" required>
                    <option value="Makanan" {{ old('kategori') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                    <option value="Minuman" {{ old('kategori') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    <option value="Umum" {{ old('kategori') == 'Umum' ? 'selected' : '' }}>Umum</option>
                </select>
            </div>
            <div>
                <label for="productStock">Stok Awal</label>
                <input id="productStock" name="stok_produk" type="number" min="0" required placeholder="Jumlah stok awal" value="{{ old('stok_produk', 0) }}" />
            </div>
            <div>
                <label for="productUnit">Satuan</label>
                <select id="productUnit" name="satuan" required>
                    <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>pcs</option>
                    <option value="box" {{ old('satuan') == 'box' ? 'selected' : '' }}>box</option>
                    <option value="kg" {{ old('satuan') == 'kg' ? 'selected' : '' }}>kg</option>
                </select>
            </div>
            <div>
                <label for="productPrice">Harga Jual</label>
                <input id="productPrice" name="harga_produk" type="text" required placeholder="Harga per unit" value="{{ old('harga_produk', '') }}" oninput="formatRupiah(this)" />
            </div>
        </div>
        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" class="btn-primary">Simpan Produk</button>
            <button class="btn-secondary" type="reset">Bersihkan Form</button>
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