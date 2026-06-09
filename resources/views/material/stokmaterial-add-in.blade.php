@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Tambah Material Masuk')

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px;">
        <a href="{{ route('materials.actual') }}" class="button-link">&larr; Kembali</a>
        <div>
            <h2 id="formTitle">Tambah Material Masuk</h2>
            <p>Isi form untuk menambahkan material masuk (stok material).</p>
        </div>
    </div>

    <div class="form-grid" style="margin-top: 20px;">
        <div>
            <label for="productionDate">Tanggal</label>
            <input id="productionDate" type="date" />
        </div>
        <div>
            <label for="productionName">Nama Material</label>
            <select id="productionName">
                <option value="">Pilih Material</option>
            </select>
        </div>
        <div>
            <label for="productionStock">Stock</label>
            <input id="productionStock" type="number" min="0" placeholder="Jumlah stock" />
        </div>
        <div>
            <label for="productionUnit">Satuan</label>
            <select id="productionUnit">
                <option value="kg">kg</option>
                <option value="gram">gram</option>
            </select>
        </div>
        <div>
            <label for="productionSupply">Suplay/Harga</label>
            <input id="productionSupply" type="text" placeholder="Harga suplay" oninput="formatRupiah(this)" />
        </div>
    </div>
    <div class="form-actions" style="margin-top: 20px;">
        <button id="addProductionBtn" class="btn-primary">Simpan Material Masuk</button>
        <button id="resetFormBtn" class="btn-secondary" type="button">Bersihkan Form</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Set type to 'in' (Material Masuk - tidak ke produksi harian)
    const type = 'in';
    
    // Set form title for Material Masuk
    const formTitle = document.getElementById('formTitle');
    if (formTitle) {
        formTitle.innerText = 'Tambah Material Masuk';
    }

    // Populate Material dropdown with fixed list from Excel
    let materialNames = [
        'Almond Milk (almonesia)', 'Almond Milk (club sehat)', 'Almond Milk (JF)', 'Almond Powder', 'Almond Skinless', 'Almond Slice', 'Apricot', 'ARA', 'Baby Jackfruit Floss ', 'Baked Cashew', 'Baked Cashew Gladly', 'Blueberry', 'Chia Seed', 'Cholestrol shot', 'Coconut Flakes', 'Coconut Sugar', 'Colagen', 'Cranberry', 'Daging Sapi Kriuk ', 'Edamame', 'Edamame (LIDIA)', 'Gojiberry', 'Golden Raisin', 'Golden raisin jumbo biasa', 'Golden Raisin Jumbo CS', 'Granola', 'Hazelnut', 'Honey Garlic', 'Jamur ', 'Keripik apel', 'keripik nanas', 'Keripik Nangka', 'Keripik Pisang', 'Keripik Salak', 'Ketan Hitam', 'Kiwi', 'Kremesan Hati Ayam ', 'Kurma ', 'Macadamia', 'Matcha', 'Natural Almond', 'Okra ', 'Pistachio', 'Pistachio non baked', 'pumpkin Chips', 'Pumpkin Seed', 'Roasted Almond', 'Roasted Almond (Bu ani)', 'Sayur Buncis', 'Sayur Jagung', 'Sayur Kentang/singkong', 'Sayur Ubi madu', 'Sayur Ubi ungu', 'Sayur Wortel', 'Serbuk Daging ', 'Serbuk Telur ', 'STROBERI', 'Sunflower Seed', 'Walnut'
    ];
    materialNames = materialNames.map(name => name.trim());
    materialNames.sort((a, b) => a.localeCompare(b, 'id', { sensitivity: 'base' }));
    const materialSelect = document.getElementById('productionName');
    materialNames.forEach((name, index) => {
        const option = document.createElement('option');
        option.value = name;
        option.textContent = name;
        materialSelect.appendChild(option);
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

    document.getElementById('addProductionBtn')?.addEventListener('click', () => {
        const date = document.getElementById('productionDate').value;
        const name = document.getElementById('productionName').value;
        const stock = document.getElementById('productionStock').value;
        const unit = document.getElementById('productionUnit').value;
        let supply = document.getElementById('productionSupply').value.replace(/\./g, '');

        if (!date || !name || !stock) {
            alert('Mohon isi semua field!');
            return;
        }

        const materials = JSON.parse(localStorage.getItem('inventoryStockMaterials') || '[]');
        const qtyIn = parseFloat(stock);
        const materialIndex = materialNames.indexOf(name) + 1; // Kode mulai dari 1
        
        // Push new record (untuk mempertahankan riwayat per tanggal)
        materials.push({
            id: Date.now(),
            code: materialIndex.toString(),
            date: date,
            name: name,
            quantity: qtyIn,
            unit: unit || 'kg',
            supply: supply || 0,
            production: 'Stok Material',
            isProductionDaily: false,
            history: [{
                date: date,
                name: name,
                action: type,
                quantity: qtyIn,
                note: 'Material Masuk - Stok Material'
            }]
        });
        localStorage.setItem('inventoryStockMaterials', JSON.stringify(materials));

        alert('Material Masuk berhasil ditambahkan!');
        window.location.href = '{{ route('materials.actual') }}';
    });

    document.getElementById('resetFormBtn')?.addEventListener('click', () => {
        document.getElementById('productionDate').value = '';
        document.getElementById('productionName').value = '';
        document.getElementById('productionStock').value = '';
        document.getElementById('productionUnit').value = '';
        document.getElementById('productionSupply').value = '';
    });
</script>
@endpush