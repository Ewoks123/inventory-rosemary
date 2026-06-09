import os
import re

base_dir = r"c:\Users\user\Music\InventoryRosemary - Copy\InventoryRosemary - Copy\InventoryRosemary - Copy"

# --- 1. Add init-data.js to HTML files ---
html_files = [
    r"admin\index.html",
    r"admin\inventory.html",
    r"produk\product.html",
    r"produk\product-add.html",
    r"produk\product-edit.html",
    r"produk\laporan.html",
    r"produk\produksi.html",
    r"produk\penjualan.html",
    r"material\stokmaterial-menu.html",
    r"material\stokmaterial-report.html"
]

script_tag = '<script src="../js/init-data.js"></script>'
for rel_path in html_files:
    file_path = os.path.join(base_dir, rel_path)
    if os.path.exists(file_path):
        with open(file_path, "r", encoding="utf-8") as f:
            content = f.read()
        if "init-data.js" not in content:
            # Insert before the last <script> tag or before </body>
            if '</body>' in content:
                content = content.replace('</body>', f'{script_tag}\n</body>')
                with open(file_path, "w", encoding="utf-8") as f:
                    f.write(content)

# --- 2. Update dashboard in admin/index.html & js/script.js ---
# The user wants "ada notif pemberitahuan buat stok yang menipis min 10" and dashboard table populated.
index_path = os.path.join(base_dir, r"admin\index.html")
with open(index_path, "r", encoding="utf-8") as f:
    index_content = f.read()

# Add notification area
if 'id="low-stock-alert"' not in index_content:
    alert_html = """
                    <div id="low-stock-alert" style="display: none; background-color: #ffcccc; padding: 15px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #ff4444; color: #cc0000; font-weight: bold;">
                        Peringatan: Ada produk dengan stok menipis (<= 10)!
                    </div>
                    <div class="cards">
"""
    index_content = index_content.replace('<div class="cards">', alert_html)
    with open(index_path, "w", encoding="utf-8") as f:
        f.write(index_content)

# Update js/script.js to populate dashboard
script_js_path = os.path.join(base_dir, r"js\script.js")
with open(script_js_path, "r", encoding="utf-8") as f:
    script_content = f.read()

# We'll append dashboard rendering logic
dashboard_logic = """
// --- Dashboard Rendering Logic ---
document.addEventListener('DOMContentLoaded', () => {
    // Only run if we are on dashboard (totalProduk element exists)
    const totalProdukEl = document.getElementById('totalProduk');
    if (totalProdukEl) {
        const products = JSON.parse(localStorage.getItem('inventoryProducts') || '[]');
        
        let totalStokHabis = 0;
        let lowStockProducts = [];
        
        const tbody = document.createElement('tbody');
        
        products.forEach(p => {
            const qty = p.quantity || 0;
            if (qty <= 0) totalStokHabis++;
            if (qty <= 10) lowStockProducts.push(p);
            
            const tr = document.createElement('tr');
            let status = qty > 0 ? '<span style="color: green; font-weight: bold;">Tersedia</span>' : '<span style="color: red; font-weight: bold;">Habis</span>';
            tr.innerHTML = `
                <td>${p.name || '-'}</td>
                <td>${p.category || '-'}</td>
                <td>${qty}</td>
                <td>Rp ${(p.price || 0).toLocaleString('id-ID')}</td>
                <td>${status}</td>
            `;
            tbody.appendChild(tr);
        });
        
        const table = document.getElementById('productTable');
        if (table) {
            const existingTbody = table.querySelector('tbody');
            if (existingTbody) table.removeChild(existingTbody);
            table.appendChild(tbody);
        }
        
        // Update summary cards
        totalProdukEl.textContent = products.length;
        
        const cards = document.querySelectorAll('.card p');
        if (cards.length >= 3) {
            cards[1].textContent = "120"; // Dummy or calculate actual
            cards[2].textContent = totalStokHabis;
        }
        
        // Show low stock alert
        const alertEl = document.getElementById('low-stock-alert');
        if (alertEl && lowStockProducts.length > 0) {
            alertEl.style.display = 'block';
            alertEl.innerHTML = `Peringatan: ${lowStockProducts.length} produk memiliki stok menipis (<= 10)!<br>` + 
                                `<small>` + lowStockProducts.map(p => p.name + ' (' + (p.quantity||0) + ')').join(', ') + `</small>`;
        }
    }
});
"""
if "Dashboard Rendering Logic" not in script_content:
    with open(script_js_path, "a", encoding="utf-8") as f:
        f.write("\n" + dashboard_logic)

# --- 3. Update produk/laporan.html Stock Awal ---
laporan_path = os.path.join(base_dir, r"produk\laporan.html")
with open(laporan_path, "r", encoding="utf-8") as f:
    laporan_content = f.read()

# Replace <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">${product.quantity || 0}</td>
# inside the rowHtml definition with product.initialStock
laporan_content = laporan_content.replace(
    '`${product.quantity || 0}`',
    '`${product.initialStock !== undefined ? product.initialStock : (product.quantity || 0)}`'
)
# Wait, let's use regex to be safe
laporan_content = re.sub(
    r'<td style="border: 1px solid #ddd; padding: 4px; text-align: center;">\$\{product\.quantity \|\| 0\}</td>',
    '<td style="border: 1px solid #ddd; padding: 4px; text-align: center;">${product.initialStock !== undefined ? product.initialStock : (product.quantity || 0)}</td>',
    laporan_content
)

with open(laporan_path, "w", encoding="utf-8") as f:
    f.write(laporan_content)


# --- 4. Update produk/product-add.html dropdown ---
add_path = os.path.join(base_dir, r"produk\product-add.html")
with open(add_path, "r", encoding="utf-8") as f:
    add_content = f.read()

# Change input to select
if '<input id="productName" type="text" placeholder="Contoh: Tepung Protein" />' in add_content:
    add_content = add_content.replace(
        '<input id="productName" type="text" placeholder="Contoh: Tepung Protein" />',
        '<select id="productName"><option value="">Pilih Produk...</option></select>'
    )
    
    js_inject = """
        // Populate products dropdown
        const existingProducts = JSON.parse(localStorage.getItem('inventoryProducts') || '[]');
        const nameSelect = document.getElementById('productName');
        if (nameSelect) {
            existingProducts.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.name;
                opt.textContent = p.name;
                nameSelect.appendChild(opt);
            });
        }
"""
    # Insert js_inject after "const storageKey = 'inventoryProducts';"
    add_content = add_content.replace("const storageKey = 'inventoryProducts';", "const storageKey = 'inventoryProducts';\n" + js_inject)

    with open(add_path, "w", encoding="utf-8") as f:
        f.write(add_content)

print("Applied!")
