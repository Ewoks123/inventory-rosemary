function sortTable(n) {
    let table = document.getElementById("productTable");
    let rows = table.rows;
    let switching = true;
    let dir = "asc";

    while (switching) {
        switching = false;
        let shouldSwitch;

        for (let i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            let x = rows[i].getElementsByTagName("TD")[n];
            let y = rows[i + 1].getElementsByTagName("TD")[n];

            if (dir === "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            } else {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
        }

        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        } else {
            if (dir === "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}

// Navigation Logic
document.addEventListener("DOMContentLoaded", () => {
    const navDashboard = document.getElementById("nav-dashboard");
    const navInventory = document.getElementById("nav-inventory");
    const dashboardView = document.getElementById("dashboard-view");
    const inventoryView = document.getElementById("inventory-view");
    const sidebarItems = document.querySelectorAll(".sidebar li");

    const isDashboardPage = !!dashboardView && !inventoryView;
    const isInventoryPage = !!inventoryView && !dashboardView;

    function showView(viewToShow) {
        if (dashboardView) dashboardView.classList.add("hidden");
        if (inventoryView) inventoryView.classList.add("hidden");

        if (viewToShow) {
            viewToShow.classList.remove("hidden");
        }
    }

    function setActiveLink(clickedItem) {
        sidebarItems.forEach(item => item.classList.remove("active"));
        if (clickedItem) clickedItem.classList.add("active");
    }

    if (isDashboardPage) {
        setActiveLink(navDashboard);
    } else if (isInventoryPage) {
        setActiveLink(navInventory);
    }

    const navSettings = document.getElementById("nav-settings") || Array.from(sidebarItems).find(i => i.textContent.includes('Settings'));
    
    function getRootPath() {
        const path = window.location.pathname;
        if (path.includes('/material/') || path.includes('/produk/')) return '../admin/';
        return '';
    }

    /*
    if (navDashboard) {
        navDashboard.addEventListener("click", () => {
            window.location.href = getRootPath() + "index.html";
        });
    }

    if (navInventory) {
        navInventory.addEventListener("click", () => {
            window.location.href = getRootPath() + "inventory.html";
        });
    }

    if (navSettings) {
        navSettings.addEventListener("click", () => {
            window.location.href = getRootPath() + "setting.html";
        });
    }
    */

    // User Menu / Profile Dropdown Logic
    const userIcon = document.querySelector(".user");
    if (userIcon) {
        // Create container if it doesn't exist
        if (!userIcon.parentElement.classList.contains('user-menu-container')) {
            const container = document.createElement('div');
            container.className = 'user-menu-container';
            userIcon.parentNode.insertBefore(container, userIcon);
            
            const dropdown = document.createElement('div');
            dropdown.className = 'user-dropdown';
            dropdown.innerHTML = `
                <div id="dropdown-profile">Profile</div>
                <form action="/admin/logout" method="POST" id="logout-form" style="display:none;">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content || ''}">
                </form>
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
                window.location.href = '/admin/profile';
            });

            document.getElementById('dropdown-logout')?.addEventListener('click', () => {
                if (confirm("Apakah Anda yakin ingin keluar?")) {
                    localStorage.removeItem("isLoggedIn");
                    localStorage.removeItem("user");
                    
                    // Trigger Laravel logout
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/logout';
                    const token = document.createElement('input');
                    token.type = 'hidden';
                    token.name = '_token';
                    token.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    form.appendChild(token);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    }

    // Sidebar Logout Logic - Handled by Blade now
    /*
    const logoutBtn = document.getElementById("logout-btn");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", () => {
            if (confirm("Apakah Anda yakin ingin keluar?")) {
                localStorage.removeItem("isLoggedIn");
                localStorage.removeItem("user");
                window.location.href = "login.html";
            }
        });
    }
    */

    // --- Dashboard Rendering Logic (Handled by Blade) ---
});
