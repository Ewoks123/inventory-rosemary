@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Edit Produksi Harian')

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        <a href="{{ route('materials.pharian') }}" class="button-link">&larr; Kembali</a>
        <div>
            <h2>Edit Produksi Harian</h2>
            <p>Material: {{ $log->material->nama_material }}</p>
        </div>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #f5c6cb; width: 100%;">
            <strong>Gagal:</strong> Kolom semua wajib di isi dengan benar.
        </div>
    @endif

    <form action="{{ route('materials.pharian.update', $log->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-grid" style="margin-top: 20px;">
            <div>
                <label for="materialDate">Tanggal</label>
                <input id="materialDate" name="date" type="date" value="{{ old('date', $log->date) }}" required />
            </div>
            <div>
                <label for="materialName">Nama Material</label>
                <input id="materialName" type="text" value="{{ $log->material->nama_material }}" readonly style="background-color: #f0f0f0;" />
                <small>Nama material tidak dapat diubah agar stok tetap sinkron.</small>
            </div>
            <div>
                <label for="materialAmount" style="font-weight: bold;">Jumlah Material Keluar (Qty)</label>
                <input id="materialAmount" name="quantity" type="number" step="0.001" min="0" value="{{ old('quantity', (float)$log->quantity) }}" required placeholder="Jumlah" style="border: 1px solid #3498db;" />
            </div>
            <div>
                <label for="materialUnit">Satuan</label>
                <input id="materialUnit" type="text" value="{{ $log->unit }}" readonly style="background-color: #f0f0f0;" />
            </div>
            <div>
                <label for="materialPrice" style="font-weight: bold;">Harga Supply / Biaya (Rp)</label>
                <input id="materialPrice" name="price" type="text" value="{{ old('price', number_format($log->price, 0, ',', '.')) }}" required oninput="formatRupiah(this)" style="border: 1px solid #27ae60;" />
            </div>
        </div>
        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="{{ route('materials.pharian') }}" class="btn-secondary" style="text-decoration: none; padding: 10px 20px; border-radius: 8px;">Batal</a>
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
