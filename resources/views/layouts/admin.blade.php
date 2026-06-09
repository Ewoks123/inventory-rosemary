<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rosemary Nutrition - Inventory')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
    

</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('scr/logorose.jpeg') }}" alt="Rosemary Logo" class="logo">
                <h2>Rosemary<br>Nutrition</h2>
            </div>

            <ul>
                <li id="nav-dashboard" class="{{ Request::is('admin/dashboard') ? 'active' : '' }}" onclick="window.location.href='{{ route('admin.dashboard') }}'">Dashboard</li>
                <li id="nav-inventory" class="{{ Request::is('admin/inventory') ? 'active' : '' }}" onclick="window.location.href='{{ route('admin.inventory') }}'">Inventory</li>
                <li id="nav-settings" class="{{ Request::is('admin/setting') ? 'active' : '' }}" onclick="window.location.href='{{ route('admin.setting') }}'">Settings</li>
                <li style="margin-top: 20px;">
                    <form action="{{ route('admin.logout') }}" method="POST" id="sidebar-logout-form">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #ff9999; font-weight: bold; cursor: pointer; padding: 0; font-family: inherit; font-size: inherit;">Logout</button>
                    </form>
                </li>
            </ul>
        </aside>

        <!-- Main -->
        <main class="main">
            <!-- Navbar -->
            <div class="navbar">
                <div class="navbar-left">
                    @yield('navbar-left')
                </div>
                <input type="text" id="globalSearchInput" placeholder="Cari...">
                <div class="user">👤</div>
            </div>

            <!-- View Sections -->
            <div id="content-area">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/init-data.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('globalSearchInput');
            if(searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const term = e.target.value.toLowerCase();
                    const rows = document.querySelectorAll('table tbody tr');
                    rows.forEach(row => {
                        // Skip if it's an empty state row
                        if(row.querySelector('td[colspan]')) return;
                        
                        const text = row.textContent.toLowerCase();
                        if(text.includes(term)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
