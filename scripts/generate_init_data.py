import pandas as pd
import json

# Use May 2026 for the dates
year = 2026
month = 5

def clean_float(v):
    if pd.isna(v):
        return 0
    try:
        return float(v)
    except:
        return 0

# 1. Parse Products
df_prod = pd.read_excel('Stok Produk Rosemary.xlsx')

products = []
sales = []
productions = []

for idx, row in df_prod.iterrows():
    name = row.iloc[1]
    if pd.isna(name) or name == 'NAMA PRODUK':
        continue
    
    stock_awal = clean_float(row.iloc[3])
    
    prod_obj = {
        "id": idx,
        "name": str(name),
        "category": "Umum",
        "quantity": stock_awal,
        "price": 0,
        "initialStock": stock_awal
    }
    products.append(prod_obj)

# 2. Parse Materials
df_mat = pd.read_excel('Stock Material - Rosemary.xlsx')
mat_masuk = []
mat_keluar = []

for idx, row in df_mat.iterrows():
    name = row.iloc[1]
    if pd.isna(name) or name == 'NAMA PRODUK':
        continue
        
    jumlah = clean_float(row.iloc[2])
    satuan = row.iloc[3] if not pd.isna(row.iloc[3]) else 'kg'
    
    # Just add ONE transaction for the initial stock to inventoryStockMaterials
    # so the material is registered in the system with its stock awal.
    if jumlah > 0:
        mat_masuk.append({
            "id": int(idx) + 10000,
            "date": f"{year}-{month:02d}-01",
            "name": str(name),
            "quantity": jumlah,
            "unit": str(satuan),
            "action": "in",
            "note": "Stock Awal"
        })
    else:
        # Also include materials with 0 stock so their names are registered 
        # (Though in material.js, names are only derived from transactions, so we MUST insert a 0 qty transaction)
        mat_masuk.append({
            "id": int(idx) + 10000,
            "date": f"{year}-{month:02d}-01",
            "name": str(name),
            "quantity": 0,
            "unit": str(satuan),
            "action": "in",
            "note": "Stock Awal"
        })


# Create init-data.js
js_content = f"""
// Auto-generated data initialization
(function() {{
    const products = {json.dumps(products)};
    const sales = []; // EMPTY as requested
    const productProductions = []; // EMPTY as requested

    const matMasuk = {json.dumps(mat_masuk)};
    const matKeluar = []; // EMPTY as requested

    // Override localStorage so it ALWAYS matches the latest Excel state on first load
    if (!localStorage.getItem('excel_data_loaded_v3')) {{
        localStorage.setItem('inventoryProducts', JSON.stringify(products));
        localStorage.setItem('inventorySales', JSON.stringify(sales));
        localStorage.setItem('productionDailyProducts', JSON.stringify(productProductions));
        
        localStorage.setItem('inventoryStockMaterials', JSON.stringify(matMasuk));
        localStorage.setItem('productionDailyMaterials', JSON.stringify(matKeluar));
        
        localStorage.setItem('excel_data_loaded_v3', 'true');
        console.log("Excel data successfully loaded into localStorage (Clean state).");
    }}
}})();
"""

with open('js/init-data.js', 'w', encoding='utf-8') as f:
    f.write(js_content)
    
print("init-data.js regenerated with empty transactions!")
