@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Tambah Produksi Harian')

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        <a href="{{ route('materials.pharian') }}" class="button-link">&larr; Kembali</a>
        <div>
            <h2 id="formTitle">Tambah Produksi Harian - Material Keluar Produksi</h2>
            <p>Isi form untuk menambahkan produksi material.</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #c3e6cb; width: 100%;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #f5c6cb; width: 100%;">
            <strong>Gagal:</strong> Kolom semua wajib di isi dengan benar.
        </div>
    @endif

    <form action="{{ route('materials.storepharian') }}" method="POST">
        @csrf
        <div class="form-grid" style="margin-top: 20px;">
            <div>
                <label for="productionDate">Tanggal</label>
                <input id="productionDate" name="date" type="date" required value="{{ date('Y-m-d') }}" />
            </div>
            <div>
                <label for="productionName">Nama Material</label>
                <select id="productionName" name="material_id" required>
                    <option value="">Pilih Material</option>
                    @foreach($materials as $m)
                        <option value="{{ $m->id }}" data-price="{{ $m->supplier }}">{{ $m->nama_material }} (Stok: {{ rtrim(rtrim(number_format($m->stok_material, 3, ',', '.'), '0'), ',') }} {{ $m->satuan }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="productionStock">Jumlah Keluar</label>
                <input id="productionStock" name="quantity" type="number" step="0.001" min="0" required placeholder="Jumlah stock" />
            </div>
            <div>
                <label for="productionUnit">Satuan</label>
                <select id="productionUnit" name="unit">
                    <option value="kg">kg</option>
                    <option value="gram">gram</option>
                </select>
                <small>Pilih satuan material (Default: kg)</small>
            </div>
            <div>
                <label for="productionSupply">Harga Supply (Total)</label>
                <input id="productionSupply" name="price" type="text" placeholder="Harga total" required oninput="formatRupiah(this)" />
            </div>
        </div>
        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" class="btn-primary">Simpan Produksi</button>
            <button id="resetFormBtn" class="btn-secondary" type="reset">Bersihkan Form</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Auto-fill material price
    document.getElementById('productionName')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const priceInput = document.getElementById('productionSupply');
        if (selectedOption && selectedOption.value) {
            let val = parseFloat(selectedOption.getAttribute('data-price'));
            if (!isNaN(val) && val > 0) {
                priceInput.value = val;
                formatRupiah(priceInput);
            } else {
                priceInput.value = '';
            }
        } else {
            priceInput.value = '';
        }
    });

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
