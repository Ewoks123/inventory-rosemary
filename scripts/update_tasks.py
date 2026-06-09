import os
import re
import json

base_dir = r"c:\Users\user\Music\InventoryRosemary - Copy\InventoryRosemary - Copy\InventoryRosemary - Copy"

# ==========================================
# 1. Update material/stokmaterial-report.html
# ==========================================
stok_report_path = os.path.join(base_dir, "material", "stokmaterial-report.html")
with open(stok_report_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace the tables
new_table_html = """
                <div id="reportContent">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h2 style="margin: 0;">LAPORAN MATERIAL MASUK / KELUAR</h2>
                        <h3 style="margin: 5px 0;">Rosemary Nutrition</h3>
                        <p id="reportPeriodText">Periode: Semua</p>
                    </div>

                    <div class="table-container" style="overflow-x:auto;">
                        <table id="masterReportTable" style="min-width: 3000px; border-collapse: collapse; font-size: 11px;">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">No</th>
                                    <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">Nama Material</th>
                                    <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">Satuan</th>
                                    <th rowspan="2" style="background-color: #3498db; color: white; border: 1px solid #ddd; padding: 4px;">Harga</th>
                                    <th colspan="31" style="background-color: #e74c3c; color: white; border: 1px solid #ddd; padding: 4px; text-align: center;">BARANG MASUK</th>
                                    <th rowspan="2" style="background-color: #e74c3c; color: white; border: 1px solid #ddd; padding: 4px;">Total Masuk</th>
                                    <th colspan="31" style="background-color: #27ae60; color: white; border: 1px solid #ddd; padding: 4px; text-align: center;">PRODUKSI (KELUAR)</th>
                                    <th rowspan="2" style="background-color: #27ae60; color: white; border: 1px solid #ddd; padding: 4px;">Total Keluar</th>
                                </tr>
                                <tr id="dateHeaderRowMaster">
                                    <!-- Date headers injected here -->
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
"""

content = re.sub(r'<div id="reportContent">.*?</div>\s*</div>\s*</main>', new_table_html + '\n            </div>\n        </main>', content, flags=re.DOTALL)

js_logic = """
    <script>
        function initDateHeaders() {
            const dateHeaderRow = document.getElementById('dateHeaderRowMaster');
            
            // Masuk Days
            for (let i = 1; i <= 31; i++) {
                const th = document.createElement('th');
                th.textContent = i;
                th.style.backgroundColor = '#e74c3c';
                th.style.color = 'white';
                th.style.border = '1px solid #ddd';
                th.style.padding = '2px';
                dateHeaderRow.appendChild(th);
            }
            
            // Keluar Days
            for (let i = 1; i <= 31; i++) {
                const th = document.createElement('th');
                th.textContent = i;
                th.style.backgroundColor = '#27ae60';
                th.style.color = 'white';
                th.style.border = '1px solid #ddd';
                th.style.padding = '2px';
                dateHeaderRow.appendChild(th);
            }
        }

        window.addEventListener('DOMContentLoaded', initDateHeaders);

        const yearSelect = document.getElementById('filterYear');
        const currentYear = new Date().getFullYear();
        for (let i = currentYear; i >= currentYear - 5; i--) {
            const opt = document.createElement('option');
            opt.value = i;
            opt.textContent = i;
            yearSelect.appendChild(opt);
        }
        yearSelect.value = '';

        function generateReport() {
            const month = document.getElementById('filterMonth').value;
            const year = document.getElementById('filterYear').value;
            
            let periodText = "Periode: Semua";
            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            if (month !== "") periodText = "Periode: " + monthNames[parseInt(month)] + " " + (year || "");
            else if (year) periodText = "Periode: " + year;
            document.getElementById('reportPeriodText').textContent = periodText;

            const stockIn = JSON.parse(localStorage.getItem('inventoryStockMaterials') || '[]');
            const stockOut = JSON.parse(localStorage.getItem('productionDailyMaterials') || '[]');
            
            function filterByDate(records) {
                return records.filter(item => {
                    if (!item.date) return false;
                    const dateObj = new Date(item.date);
                    const m = dateObj.getMonth();
                    const y = dateObj.getFullYear();
                    let match = true;
                    if (month !== "" && m != month) match = false;
                    if (year && y != year) match = false;
                    return match;
                });
            }

            const filteredIn = filterByDate(stockIn);
            const filteredOut = filterByDate(stockOut);

            const allNames = new Set([...filteredIn.map(i => i.name), ...filteredOut.map(i => i.name)]);
            const materials = Array.from(allNames).filter(n => n);

            const tbody = document.querySelector('#masterReportTable tbody');
            tbody.innerHTML = '';
            
            let index = 1;
            materials.sort().forEach(materialName => {
                const inRecords = filteredIn.filter(i => i.name === materialName);
                const outRecords = filteredOut.filter(i => i.name === materialName);
                
                const unit = (inRecords[0] || outRecords[0] || {}).unit || 'kg';
                const supply = parseFloat((inRecords[0] || outRecords[0] || {}).supply || 0);

                let rowHtml = `
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">${index++}</td>
                    <td style="border: 1px solid #ddd; padding: 4px;">${materialName}</td>
                    <td style="border: 1px solid #ddd; padding: 4px;">${unit}</td>
                    <td style="border: 1px solid #ddd; padding: 4px;">${supply ? 'Rp ' + supply.toLocaleString('id-ID') : '-'}</td>
                `;

                // MASUK
                let totalIn = 0;
                for (let day = 1; day <= 31; day++) {
                    const dayQty = inRecords.filter(t => new Date(t.date).getDate() === day).reduce((sum, t) => sum + parseFloat(t.quantity||0), 0);
                    totalIn += dayQty;
                    rowHtml += `<td style="border: 1px solid #ddd; padding: 2px; text-align: center;">${dayQty || ''}</td>`;
                }
                rowHtml += `<td style="border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold;">${totalIn}</td>`;

                // KELUAR
                let totalOut = 0;
                for (let day = 1; day <= 31; day++) {
                    const dayQty = outRecords.filter(t => new Date(t.date).getDate() === day).reduce((sum, t) => sum + parseFloat(t.quantity||0), 0);
                    totalOut += dayQty;
                    rowHtml += `<td style="border: 1px solid #ddd; padding: 2px; text-align: center;">${dayQty || ''}</td>`;
                }
                rowHtml += `<td style="border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold;">${totalOut}</td>`;

                const tr = document.createElement('tr');
                tr.innerHTML = rowHtml;
                tbody.appendChild(tr);
            });
        }

        window.addEventListener('DOMContentLoaded', generateReport);
    </script>
"""

content = re.sub(r'<script>\s*// Generate date headers.*?</script>', js_logic, content, flags=re.DOTALL)

with open(stok_report_path, "w", encoding="utf-8") as f:
    f.write(content)


# ==========================================
# 2. Update produk/laporan.html (Week Colors)
# ==========================================
laporan_path = os.path.join(base_dir, "produk", "laporan.html")
with open(laporan_path, "r", encoding="utf-8") as f:
    lap_content = f.read()

new_initDateHeaders = """
        function getWeekColor(day, isHeader = false) {
            // Colors for header
            const hColors = ['#c0392b', '#d35400', '#e67e22', '#f39c12', '#e74c3c'];
            // Colors for body (lighter)
            const bColors = ['#f5b7b1', '#f5cba7', '#fad7a1', '#fdebd0', '#fadbd8'];
            
            const arr = isHeader ? hColors : bColors;
            if (day <= 7) return arr[0];
            if (day <= 14) return arr[1];
            if (day <= 21) return arr[2];
            if (day <= 28) return arr[3];
            return arr[4];
        }

        function initDateHeaders() {
            const dateHeaderRow = document.getElementById('dateHeaderRow');
            dateHeaderRow.innerHTML = '';
            
            // Produksi Days
            for (let i = 1; i <= 31; i++) {
                const th = document.createElement('th');
                th.textContent = i;
                th.style.backgroundColor = '#27ae60';
                th.style.color = 'white';
                th.style.border = '1px solid #ddd';
                th.style.padding = '2px';
                dateHeaderRow.appendChild(th);
            }
            
            // Penjualan Days (colored by week)
            for (let i = 1; i <= 31; i++) {
                const th = document.createElement('th');
                th.textContent = i;
                th.style.backgroundColor = getWeekColor(i, true);
                th.style.color = 'white';
                th.style.border = '1px solid #ddd';
                th.style.padding = '2px';
                dateHeaderRow.appendChild(th);
            }
        }
"""
lap_content = re.sub(r'function initDateHeaders\(\) \{.*?\n        \}', new_initDateHeaders.strip(), lap_content, flags=re.DOTALL)

# In renderMasterTable, update the penjualan cells
penj_loop_old = """
                // PENJUALAN COLUMNS (1-31)
                let totalPenjualan = 0;
                for (let day = 1; day <= 31; day++) {
                    const daySales = productSales.filter(s => new Date(s.date).getDate() === day);
                    const qty = daySales.reduce((sum, s) => sum + s.quantity, 0);
                    totalPenjualan += qty;
                    rowHtml += `<td style="border: 1px solid #ddd; padding: 2px; text-align: center;">${qty || ''}</td>`;
                }
"""

penj_loop_new = """
                // PENJUALAN COLUMNS (1-31)
                let totalPenjualan = 0;
                for (let day = 1; day <= 31; day++) {
                    const daySales = productSales.filter(s => new Date(s.date).getDate() === day);
                    const qty = daySales.reduce((sum, s) => sum + s.quantity, 0);
                    totalPenjualan += qty;
                    const bgColor = getWeekColor(day, false);
                    rowHtml += `<td style="border: 1px solid #ddd; padding: 2px; text-align: center; background-color: ${bgColor};">${qty || ''}</td>`;
                }
"""
lap_content = lap_content.replace(penj_loop_old.strip(), penj_loop_new.strip())

with open(laporan_path, "w", encoding="utf-8") as f:
    f.write(lap_content)


# ==========================================
# 3. Update js/product.js (Edit & Hapus) and container width
# ==========================================
product_js_path = os.path.join(base_dir, "js", "product.js")
with open(product_js_path, "r", encoding="utf-8") as f:
    prod_js = f.read()

# Update table rendering
action_buttons = """<td style="padding: 10px; border: 1px solid #ddd; text-align: center; white-space: nowrap;">
                    <button onclick="editProduct(${index})" class="btn-primary" style="padding: 5px 10px; border-radius: 5px; cursor: pointer; border: none; background-color: #f39c12; color: white;">Edit</button>
                    <button onclick="deleteProduct(${index})" class="btn-primary" style="padding: 5px 10px; border-radius: 5px; cursor: pointer; border: none; background-color: #e74c3c; color: white;">Hapus</button>
                    <button onclick="goToDetail(${index})" class="btn-primary" style="padding: 5px 10px; border-radius: 5px; cursor: pointer; border: none; background-color: #6d0f1b; color: white;">Lihat Detail</button>
                </td>"""

prod_js = re.sub(
    r'<td style="padding: 10px; border: 1px solid #ddd; text-align: center;">\s*<button onclick="goToDetail\(\$\{index\}\)".*?</td>',
    action_buttons,
    prod_js,
    flags=re.DOTALL
)

# Add edit and delete functions
functions_to_add = """
// ================= EDIT & HAPUS =================
function editProduct(index) {
    const products = getProducts();
    const p = products[index];
    if (!p) return;

    const newCategory = prompt("Masukkan Kategori baru:", p.category || '');
    if (newCategory === null) return;
    
    const newStock = prompt("Masukkan Stok baru:", p.quantity || 0);
    if (newStock === null) return;
    
    const newPrice = prompt("Masukkan Harga baru (angka saja):", p.price || 0);
    if (newPrice === null) return;

    products[index].category = newCategory;
    products[index].quantity = parseInt(newStock) || 0;
    products[index].price = parseFloat(newPrice) || 0;

    localStorage.setItem(productStorageKey, JSON.stringify(products));
    renderProducts();
    renderProductSummary();
    alert("Produk berhasil diedit!");
}

function deleteProduct(index) {
    if (confirm("Apakah Anda yakin ingin menghapus produk ini?")) {
        const products = getProducts();
        products.splice(index, 1);
        localStorage.setItem(productStorageKey, JSON.stringify(products));
        renderProducts();
        renderProductSummary();
    }
}
"""

if "function editProduct" not in prod_js:
    prod_js += "\n" + functions_to_add

with open(product_js_path, "w", encoding="utf-8") as f:
    f.write(prod_js)


# Fix width for product.css to make sure it's 100% and not to the left
css_path = os.path.join(base_dir, "css", "product.css")
if os.path.exists(css_path):
    with open(css_path, "r", encoding="utf-8") as f:
        css = f.read()
    if ".product-container {" in css:
        # Check if display is grid, if so, we can disable it so table takes full width
        css = re.sub(r'(\.product-container\s*\{[^}]*display:\s*grid;)', r'/*\1*/ display: block;', css)
    with open(css_path, "w", encoding="utf-8") as f:
        f.write(css)

print("Tasks applied!")
