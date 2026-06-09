@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Input Penjualan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        <a href="{{ route('produks.index') }}" class="button-link">&larr; Kembali</a>
        <div>
            <h2>Input Penjualan</h2>
            <p>Catat penjualan produk.</p>
        </div>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #f5c6cb; width: 100%;">
            <strong>Gagal:</strong> {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('penjualans.store') }}" method="POST">
        @csrf
        <div class="form-grid" style="margin-top: 20px;">
            <div>
                <label for="salesDate">Tanggal</label>
                <input id="salesDate" name="tanggal_penjualan" type="date" required value="{{ date('Y-m-d') }}" />
            </div>
            <div>
                <label for="salesWeek">Minggu</label>
                <select id="salesWeek" disabled style="background-color: #f5f5f5;">
                    <option value="Week 1">Week 1 (1-7)</option>
                    <option value="Week 2">Week 2 (8-14)</option>
                    <option value="Week 3">Week 3 (15-21)</option>
                    <option value="Week 4">Week 4 (22-31)</option>
                </select>
            </div>
            <div>
                <label for="salesProduct">Pilih Produk</label>
                <select id="salesProduct" name="id_produk" required>
                    <option value="">Pilih Produk...</option>
                    @foreach($produks as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->harga_produk }}">{{ $p->nama_produk }} (Stok: {{ rtrim(rtrim(number_format($p->stok_produk, 3, ',', '.'), '0'), ',') ?: '0' }} {{ $p->satuan }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="salesQuantity">Jumlah Penjualan</label>
                <input id="salesQuantity" name="jumlah_terjual" type="number" min="1" required placeholder="Jumlah terjual" />
            </div>
            <div>
                <label for="salesPrice">Harga Satuan</label>
                <input id="salesPrice" name="harga_satuan" type="text" required placeholder="Harga per unit" oninput="formatRupiah(this); calculateTotal();" />
            </div>
            <div>
                <label for="salesTotal">Total Harga</label>
                <input id="salesTotal" type="text" placeholder="Total otomatis" readonly style="background-color: #f5f5f5;" />
            </div>
        </div>
        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" class="btn-primary">Simpan Penjualan</button>
            <button type="reset" class="btn-secondary">Bersihkan Form</button>
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

    function parseRupiah(val) {
        if (!val) return 0;
        return parseInt(val.toString().replace(/\./g, '')) || 0;
    }

    // Auto-fill product price
    document.getElementById('salesProduct')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const priceInput = document.getElementById('salesPrice');
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
        calculateTotal();
    });

    // Hitung total otomatis
    document.getElementById('salesQuantity')?.addEventListener('input', calculateTotal);

    function calculateTotal() {
        const qty = parseInt(document.getElementById('salesQuantity').value) || 0;
        const price = parseRupiah(document.getElementById('salesPrice').value);
        const total = qty * price;
        const totalInput = document.getElementById('salesTotal');
        totalInput.value = total;
        formatRupiah(totalInput);
    }

    function getWeeklyPeriod(dateString) {
        if (!dateString) return 'Week 1';
        const date = new Date(dateString);
        const day = date.getDate();
        if (day >= 1 && day <= 7) return 'Week 1';
        if (day >= 8 && day <= 14) return 'Week 2';
        if (day >= 15 && day <= 21) return 'Week 3';
        return 'Week 4';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const dateInput = document.getElementById('salesDate');
        const weekSelect = document.getElementById('salesWeek');

        if (dateInput && weekSelect) {
            weekSelect.value = getWeeklyPeriod(dateInput.value);
            dateInput.addEventListener('change', () => {
                weekSelect.value = getWeeklyPeriod(dateInput.value);
            });
        }
    });
</script>
@endpush