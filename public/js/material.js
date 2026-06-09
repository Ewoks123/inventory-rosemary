const storageKey = 'inventoryStockMaterials';
const productionDailyKey = 'productionDailyMaterials';

function getSortedTransactionsWithBalance() {
    const stockIn = JSON.parse(localStorage.getItem('inventoryStockMaterials') || '[]');
    const stockOut = JSON.parse(localStorage.getItem('productionDailyMaterials') || '[]');
    
    // Combine all
    let all = [];
    stockIn.forEach((item, index) => all.push({...item, type: 'in', originalIndex: index}));
    stockOut.forEach((item, index) => all.push({...item, type: 'out', originalIndex: index}));
    
    // Sort by date then by id/timestamp
    all.sort((a, b) => {
        const dateA = new Date(a.date);
        const dateB = new Date(b.date);
        if (dateA - dateB !== 0) return dateA - dateB;
        return a.id - b.id;
    });

    // Calculate running balance per material name
    const balances = {};
    return all.map(item => {
        const name = (item.name || '').toLowerCase().trim();
        if (!balances[name]) balances[name] = 0;
        
        // Convert to grams for internal calculation if needed
        const unit = (item.unit || 'kg').toLowerCase();
        let qty = parseFloat(item.quantity || 0);
        let qtyInGrams = (unit === 'kg') ? qty * 1000 : qty;

        if (item.type === 'in') balances[name] += qtyInGrams;
        else balances[name] -= qtyInGrams;

        // Always return display balance in KG (conversion: grams / 1000)
        const displayBalanceInKg = balances[name] / 1000;

        return {
            ...item,
            runningBalance: parseFloat(displayBalanceInKg.toFixed(3))
        };
    });
}

function getStorageKey() {
    const path = window.location.pathname;
    // pharian pages use production daily storage
    if (path.includes('pharian')) {
        return productionDailyKey;
    }
    // stokmaterial-add.html (produksi harian) uses production daily storage
    if (path.includes('stokmaterial-add.html') && !path.includes('stokmaterial-add-in.html')) {
        return productionDailyKey;
    }
    // Default: stok material storage
    return storageKey;
}

function getMaterials() {
    const key = getStorageKey();
    const saved = localStorage.getItem(key);
    return saved ? JSON.parse(saved) : [];
}

function saveMaterials(materials) {
    const key = getStorageKey();
    localStorage.setItem(key, JSON.stringify(materials));
}

function getQueryParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

function formatDate(value) {
    if (!value) return '-';
    const date = new Date(value);
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
}

function getStatusLabel(item) {
    const latest = item.history && item.history.length ? item.history[0] : null;
    if (!latest) return '-';
    return latest.action === 'in' ? 'Masuk' : 'Keluar';
}

function populateEditForm(code) {
    const materials = getMaterials();
    const material = materials.find(item => item.code.toLowerCase() === code.toLowerCase());
    if (!material) return;
    editCode = material.code;

    const setValue = (id, value) => {
        const element = document.getElementById(id);
        if (element) element.value = value;
    };

    setValue('materialDate', material.date || '');
    setValue('materialProduction', material.production || '');
    setValue('materialCode', material.code || '');
    setValue('materialName', material.name || '');
    setValue('materialBatch', material.batchNo || '');
    setValue('materialSize', material.size || '');
    setValue('materialAmount', material.quantity || '');
    setValue('materialExpired', material.expired || '');
    setValue('materialDistribution', material.distribution || '');
    setValue('materialAction', 'in');

    const formTitle = document.querySelector('.form-card h2');
    if (formTitle) formTitle.innerText = 'Edit Stok Material';
    const submitButton = document.getElementById('addMaterialBtn');
    if (submitButton) submitButton.textContent = 'Simpan Perubahan';
}

function updateSummary(materials) {
    const summaryTotal = document.getElementById('totalMaterial');
    const summaryIn = document.getElementById('materialIn');
    const summaryOut = document.getElementById('materialOut');

    let totalIn = 0;
    let totalOut = 0;

    materials.forEach(item => {
        const latest = item.history && item.history.length ? item.history[0] : null;
        if (latest) {
            if (latest.action === 'in') totalIn++;
            else if (latest.action === 'out') totalOut++;
        }
    });

    if (summaryTotal) summaryTotal.innerText = materials.length;
    if (summaryIn) summaryIn.innerText = totalIn;
    if (summaryOut) summaryOut.innerText = totalOut;
}

function renderMaterialTable(materials, filter = 'out') {
    const tbody = document.querySelector('#stockTable tbody');
    if (!tbody) return;
    tbody.innerHTML = '';

    const allWithBalance = getSortedTransactionsWithBalance();
    const filtered = allWithBalance.filter(item => item.type === 'out');

    filtered.forEach((item, index) => {
        const sisaStok = item.runningBalance;
        const stockIn = JSON.parse(localStorage.getItem('inventoryStockMaterials') || '[]');
        const originalItem = stockIn.find(i => i.name.toLowerCase() === item.name.toLowerCase());
        const unitPrice = originalItem ? parseFloat(originalItem.supply) : 0;
        const totalValue = sisaStok * unitPrice;
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${formatDate(item.date)}</td>
            <td>${item.name || '-'}</td>
            <td>${item.quantity || 0} ${item.unit || 'kg'}</td>
            <td>${sisaStok}</td>
            <td>kg</td>
            <td>${totalValue ? 'Rp ' + totalValue.toLocaleString('id-ID') : '-'}</td>
            <td>Keluar</td>
            <td class="action-buttons">
                <button class="btn-primary btn-edit" data-code="${item.code || item.id}">Edit</button>
                <button class="btn-secondary btn-delete" data-id="${item.id}" data-type="${item.type}">Hapus</button>
            </td>
        `;
        tbody.appendChild(row);
    });

    tbody.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', (event) => {
            const code = event.target.dataset.code;
            window.location.href = `pharian-edit.html?edit=${encodeURIComponent(code)}`;
        });
    });

    // Add event listeners for Delete in Material Table
    tbody.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', (event) => {
            const id = event.target.dataset.id;
            const type = event.target.dataset.type;
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                const key = type === 'in' ? 'inventoryStockMaterials' : 'productionDailyMaterials';
                let data = JSON.parse(localStorage.getItem(key) || '[]');
                data = data.filter(item => String(item.id) !== String(id));
                localStorage.setItem(key, JSON.stringify(data));
                renderPage();
            }
        });
    });
}

function renderHistory(materials) {
    const historyBody = document.querySelector('#historyTable tbody');
    if (!historyBody) return;
    historyBody.innerHTML = '';

    const history = materials.flatMap(item => item.history || []);
    history.sort((a, b) => new Date(b.date) - new Date(a.date));

    history.forEach(record => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${formatDate(record.date)}</td>
            <td>${record.name}</td>
            <td>${record.action === 'in' ? 'Masuk' : 'Keluar'}</td>
            <td>${record.quantity}</td>
            <td>${record.note || '-'}</td>
        `;
        historyBody.appendChild(row);
    });
}

function renderStockActualTable(materials, filter = 'in') {
    const tbody = document.querySelector('#stockTable tbody');
    if (!tbody) return;
    tbody.innerHTML = '';

    const allWithBalance = getSortedTransactionsWithBalance();

    allWithBalance.forEach((item, index) => {
        const sisaStok = item.runningBalance;
        const unitPrice = parseFloat(item.supply) || 0;
        const totalValue = sisaStok * unitPrice;
        const masukQty = item.type === 'in' ? item.quantity : '';
        const keluarQty = item.type === 'out' ? item.quantity : '';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${formatDate(item.date)}</td>
            <td>${item.name || '-'}</td>
            <td>${masukQty}</td>
            <td>${keluarQty}</td>
            <td>${sisaStok}</td>
            <td>kg</td>
            <td>${totalValue ? 'Rp ' + totalValue.toLocaleString('id-ID') : '-'}</td>
            <td class="action-buttons">
                <button class="btn-primary btn-edit-stock" 
                    data-type="${item.type}" 
                    data-index="${item.originalIndex}" 
                    data-code="${item.code}">Edit</button>
                <button class="btn-secondary btn-delete-stock" 
                    data-id="${item.id}" 
                    data-type="${item.type}">Hapus</button>
            </td>
        `;
        tbody.appendChild(row);
    });

    tbody.querySelectorAll('.btn-edit-stock').forEach(button => {
        button.addEventListener('click', (event) => {
            const btn = event.currentTarget;
            const type = btn.dataset.type;
            const index = btn.dataset.index;
            const code = btn.dataset.code;

            if (type === 'in') {
                window.location.href = `stokmaterial-edit.html?edit=${encodeURIComponent(index)}`;
            } else {
                window.location.href = `pharian-edit.html?edit=${encodeURIComponent(code)}`;
            }
        });
    });

    tbody.querySelectorAll('.btn-delete-stock').forEach(button => {
        button.addEventListener('click', (event) => {
            const id = event.target.dataset.id;
            const type = event.target.dataset.type;
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                const key = type === 'in' ? 'inventoryStockMaterials' : 'productionDailyMaterials';
                let data = JSON.parse(localStorage.getItem(key) || '[]');
                data = data.filter(item => String(item.id) !== String(id));
                localStorage.setItem(key, JSON.stringify(data));
                const materials = JSON.parse(localStorage.getItem('inventoryStockMaterials') || '[]');
                renderStockActualTable(materials, 'in');
                updateStockSummary(materials);
            }
        });
    });
}

function updateStockSummary(materials) {
    const allWithBalance = getSortedTransactionsWithBalance();
    const uniqueMaterials = [...new Set(allWithBalance.map(item => item.name).filter(name => name))];

    const totalIn = allWithBalance
        .filter(item => item.type === 'in')
        .reduce((sum, item) => sum + parseFloat(item.quantity || 0), 0);
    const totalOut = allWithBalance
        .filter(item => item.type === 'out')
        .reduce((sum, item) => sum + parseFloat(item.quantity || 0), 0);

    const latestBalancePerMaterial = allWithBalance.reduce((acc, item) => {
        const name = (item.name || '').toLowerCase().trim();
        if (!name) return acc;
        acc[name] = item.runningBalance;
        return acc;
    }, {});
    const totalStock = Object.values(latestBalancePerMaterial).reduce((sum, value) => sum + parseFloat(value || 0), 0);

    const stockIn = JSON.parse(localStorage.getItem('inventoryStockMaterials') || '[]');
    const totalSupplyValue = stockIn.reduce((sum, item) => {
        const qty = parseFloat(item.quantity || 0);
        const price = parseFloat(item.supply || 0);
        return sum + (qty * price);
    }, 0);

    const summaryTotal = document.getElementById('summaryTotal');
    const summaryIn = document.getElementById('summaryIn');
    const summaryOut = document.getElementById('summaryOut');
    const summarySupply = document.getElementById('summarySupply');

    if (summaryTotal) summaryTotal.innerText = uniqueMaterials.length;
    if (summaryIn) summaryIn.innerText = `${totalIn} kg`;
    if (summaryOut) summaryOut.innerText = `${totalOut} kg`;
    if (summarySupply) summarySupply.innerText = totalSupplyValue ? `Rp ${totalSupplyValue.toLocaleString('id-ID')}` : 'Rp 0';
}

function renderPage(filter = 'out') {
    const materials = getMaterials();
    updateSummary(materials);
    renderMaterialTable(materials, filter);
    renderHistory(materials);
}

function resetForm() {
    const setValue = (id, value) => {
        const element = document.getElementById(id);
        if (element) element.value = value;
    };

    setValue('materialDate', '');
    setValue('materialProduction', '');
    setValue('materialCode', '');
    setValue('materialName', '');
    setValue('materialBatch', '');
    setValue('materialSize', '');
    setValue('materialAmount', '');
    setValue('materialExpired', '');
    setValue('materialDistribution', '');
    setValue('materialAction', 'in');
}

document.addEventListener('DOMContentLoaded', () => {
    console.log("MATERIAL JS JALAN ✅ (Rendering handled by Blade)");
    // renderPage();

    const filterAction = document.getElementById('filterAction');
    if (filterAction) {
        filterAction.addEventListener('change', (event) => {
            const filter = event.target.value;
            // Check if we're on stokmaterial-actual page
            if (window.location.pathname.includes('stokmaterial-actual.html')) {
                const materials = getMaterials();
                renderStockActualTable(materials, filter);
                updateStockSummary(materials);
            } else {
                renderPage(filter);
            }
        });
    }

    if (window.location.pathname.includes('stokmaterial-actual.html')) {
        // const materials = getMaterials();
        // renderStockActualTable(materials, 'in');
        // updateStockSummary(materials);
    }

    const addButton = document.getElementById('addMaterialBtn');
    if (addButton) {
        const editParam = getQueryParam('edit');
        if (editParam) {
            populateEditForm(editParam);
        }

        addButton.addEventListener('click', () => {
            const dateValue = document.getElementById('materialDate').value;
            const production = document.getElementById('materialProduction').value.trim();
            const code = document.getElementById('materialCode').value.trim();
            const name = document.getElementById('materialName').value.trim();
            const batchNo = document.getElementById('materialBatch').value.trim();
            const size = document.getElementById('materialSize').value.trim();
            const quantity = Number(document.getElementById('materialAmount').value);
            const expired = document.getElementById('materialExpired').value;
            const distribution = document.getElementById('materialDistribution').value.trim();
            const action = document.getElementById('materialAction').value;
            const date = dateValue || new Date().toISOString().split('T')[0];

            if (!date || !production || !code || !name || !batchNo || !size || quantity <= 0 || !expired || !distribution) {
                alert('Isi semua bidang dengan benar sebelum menyimpan.');
                return;
            }

            const materials = getMaterials();
            const originalCode = editCode || code;
            let material = materials.find(item => item.code.toLowerCase() === originalCode.toLowerCase());

            if (!material) {
                material = {
                    date,
                    production,
                    code,
                    name,
                    batchNo,
                    size,
                    quantity,
                    expired,
                    distribution,
                    balance: 0,
                    lastIn: null,
                    lastOut: null,
                    totalIn: 0,
                    totalOut: 0,
                    history: []
                };
                materials.push(material);
            }

            if (originalCode.toLowerCase() !== code.toLowerCase()) {
                const duplicate = materials.find(item => item.code.toLowerCase() === code.toLowerCase() && item.code.toLowerCase() !== originalCode.toLowerCase());
                if (duplicate) {
                    alert('Kode material sudah digunakan oleh data lain. Gunakan kode unik.');
                    return;
                }
            }

            material.date = date;
            material.production = production;
            material.code = code;
            material.name = name;
            material.batchNo = batchNo;
            material.size = size;
            material.expired = expired;
            material.distribution = distribution;

            if (action === 'in') {
                material.quantity = quantity;
                material.balance += quantity;
                material.lastIn = date;
                material.totalIn += quantity;
            } else {
                material.quantity = quantity;
                material.balance -= quantity;
                material.lastOut = date;
                material.totalOut += quantity;
            }

            material.history.unshift({
                date,
                production,
                code,
                name,
                batchNo,
                size,
                quantity,
                expired,
                distribution,
                action,
                note: action === 'in' ? 'Material masuk' : 'Material keluar'
            });

            saveMaterials(materials);
            renderPage();
            if (editCode) {
                alert('Stok berhasil diperbarui!');
                window.location.href = 'pharian.html';
                return;
            }
            alert('Stok berhasil ditambahkan!');
            resetForm();
        });

        const resetButton = document.getElementById('resetFormBtn');
        if (resetButton) resetButton.addEventListener('click', resetForm);
    }

    // Event untuk halaman riwayat
    if (window.location.pathname.includes('stokmaterial-history.html')) {
        const materials = getMaterials();
        renderHistory(materials);
    }

    // Event untuk logo dan back button
    const logoLink = document.getElementById('logo-link');
    const backBtn = document.getElementById('back-btn');
    if (logoLink) {
        logoLink.addEventListener('click', () => {
            window.location.href = 'index.html'; // Dashboard
        });
    }
    if (backBtn) {
        backBtn.addEventListener('click', () => {
            window.location.href = 'inventory.html'; // Kembali ke inventory
        });
    }

    const navDashboard = document.getElementById('nav-dashboard');
    const navInventory = document.getElementById('nav-inventory');
    const navSettings = document.getElementById('nav-settings') || Array.from(document.querySelectorAll('.sidebar li')).find(i => i.textContent.includes('Settings'));

    if (navDashboard) navDashboard.addEventListener('click', () => {
        window.location.href = '../admin/index.html';
    });
    if (navInventory) navInventory.addEventListener('click', () => {
        window.location.href = '../admin/inventory.html';
    });
    if (navSettings) navSettings.addEventListener('click', () => {
        window.location.href = '../admin/setting.html';
    });
});

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
