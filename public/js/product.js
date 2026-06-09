const productStorageKey = 'inventoryProducts';

// ================= GET DATA =================
function getProducts() {
    const data = localStorage.getItem(productStorageKey);
    return data ? JSON.parse(data) : [];
}

// ================= FORMAT DATE =================
function formatDate(value) {
    if (!value) return '-';
    const date = new Date(value);
    return date.toLocaleDateString('id-ID');
}

function formatCurrency(value) {
    return 'Rp ' + (value || 0).toLocaleString('id-ID');
}

// ================= RENDER SUMMARY =================
function renderProductSummary() {
    const products = getProducts();
    
    const totalProducts = products.length;
    const totalStock = products.reduce((sum, p) => sum + (p.quantity || 0), 0);
    const totalValue = products.reduce((sum, p) => sum + ((p.quantity || 0) * (p.cost || 0)), 0);

    const totalEl = document.getElementById('totalProducts');
    const stockEl = document.getElementById('totalStock');
    const valueEl = document.getElementById('totalValue');

    if (totalEl) totalEl.textContent = totalProducts;
    if (stockEl) stockEl.textContent = totalStock;
    if (valueEl) valueEl.textContent = formatCurrency(totalValue);
}

// ================= RENDER PRODUK =================
function renderProducts() {
    const container = document.querySelector('.product-container');
    if (!container) return;

    const products = getProducts().filter(p => (p.quantity || 0) > 0);

    if (products.length === 0) {
        container.innerHTML = '<div style="text-align: center; padding: 40px; color: #888;"><p>Belum ada stok produk yang diinput. Gunakan "Input Produksi" untuk menambah stok.</p></div>';
        return;
    }

    container.innerHTML = '';

    let tableHTML = `
        <table class="styled-table" style="width:100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">No</th>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">Nama Produk</th>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">Kategori</th>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">Stok</th>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">Harga</th>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">Aksi</th>
                </tr>
            </thead>
            <tbody>
    `;

    products.forEach((item, index) => {
        tableHTML += `
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">${index + 1}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">${item.name || '-'}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">${item.category || '-'}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">${item.quantity || 0} ${item.unit || 'pcs'}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">${formatCurrency(item.price || 0)}</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: center; white-space: nowrap;">
                    <button onclick="editProduct(${index})" class="btn-primary" style="padding: 5px 10px; border-radius: 5px; cursor: pointer; border: none; background-color: #f39c12; color: white;">Edit</button>
                    <button onclick="deleteProduct(${index})" class="btn-primary" style="padding: 5px 10px; border-radius: 5px; cursor: pointer; border: none; background-color: #e74c3c; color: white;">Hapus</button>

                </td>
            </tr>
        `;
    });

    tableHTML += `</tbody></table>`;
    container.innerHTML = tableHTML;
}


// ================= SEARCH =================
function searchProduct(keyword) {
    const products = getProducts();

    if (!keyword) {
        renderProducts();
        return;
    }

    const filtered = products.filter(item =>
        (item.name || '').toLowerCase().includes(keyword.toLowerCase()) ||
        (item.code || '').toLowerCase().includes(keyword.toLowerCase()) ||
        (item.category || '').toLowerCase().includes(keyword.toLowerCase())
    );

    renderFiltered(filtered);
}

function renderFiltered(products) {
    const container = document.querySelector('.product-container');
    if (!container) return;

    if (products.length === 0) {
        container.innerHTML = '<div style="text-align: center; padding: 40px; color: #888;"><p>Produk tidak ditemukan.</p></div>';
        return;
    }

    container.innerHTML = '';

    let tableHTML = `
        <table class="styled-table" style="width:100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">No</th>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">Nama Produk</th>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">Kategori</th>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">Stok</th>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">Harga</th>
                    <th style="background-color: #6d0f1b; color: white; padding: 10px; border: 1px solid #ddd;">Aksi</th>
                </tr>
            </thead>
            <tbody>
    `;

    products.forEach((item, index) => {
        tableHTML += `
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">${index + 1}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">${item.name || '-'}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">${item.category || '-'}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">${item.quantity || 0} ${item.unit || 'pcs'}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">${formatCurrency(item.price || 0)}</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: center; white-space: nowrap;">
                    <button onclick="editProduct(${index})" class="btn-primary" style="padding: 5px 10px; border-radius: 5px; cursor: pointer; border: none; background-color: #f39c12; color: white;">Edit</button>
                    <button onclick="deleteProduct(${index})" class="btn-primary" style="padding: 5px 10px; border-radius: 5px; cursor: pointer; border: none; background-color: #e74c3c; color: white;">Hapus</button>

                </td>
            </tr>
        `;
    });

    tableHTML += `</tbody></table>`;
    container.innerHTML = tableHTML;
}

// ================= INIT =================
document.addEventListener('DOMContentLoaded', () => {
    console.log("PRODUCT JS JALAN ✅ (Rendering handled by Blade)");

    // renderProducts();
    // renderProductSummary();

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchProduct(e.target.value);
        });
    }
});

// ================= EDIT & HAPUS =================
function editProduct(index) {
    window.location.href = 'product-edit.html?id=' + index;
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

// User Menu / Profile Dropdown Logic (Global)
document.addEventListener('DOMContentLoaded', () => {
    const userIcon = document.querySelector(".user");
    if (userIcon) {
        if (!userIcon.parentElement.classList.contains('user-menu-container')) {
            const container = document.createElement('div');
            container.className = 'user-menu-container';
            userIcon.parentNode.insertBefore(container, userIcon);
            
            const dropdown = document.createElement('div');
            dropdown.className = 'user-dropdown';
            dropdown.innerHTML = `
                <div id="dropdown-profile">Profile</div>
                <div id="dropdown-logout">Logout</div>
            `;
            
            container.appendChild(userIcon);
            container.appendChild(dropdown);

            userIcon.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });

            document.addEventListener('click', () => {
                dropdown.classList.remove('show');
            });

            document.getElementById('dropdown-profile')?.addEventListener('click', () => {
                const path = window.location.pathname;
                if (path.includes('/material/') || path.includes('/produk/')) {
                    window.location.href = '../admin/profile.html';
                } else if (path.includes('/admin/')) {
                    window.location.href = 'profile.html';
                } else {
                    window.location.href = 'admin/profile.html';
                }
            });

            document.getElementById('dropdown-logout')?.addEventListener('click', () => {
                if (confirm("Apakah Anda yakin ingin keluar?")) {
                    localStorage.removeItem("isLoggedIn");
                    localStorage.removeItem("user");
                    
                    const path = window.location.pathname;
                    if (path.includes('/material/') || path.includes('/produk/')) {
                        window.location.href = '../admin/login.html';
                    } else if (path.includes('/admin/')) {
                        window.location.href = 'login.html';
                    } else {
                        window.location.href = 'admin/login.html';
                    }
                }
            });
        }
    }
});
