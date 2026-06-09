<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rosemary Nutrition')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material.css') }}">
    @stack('styles')
    

</head>
<body>
    <div class="container">
        <main class="main">
            <div class="navbar">
                <div class="logo-section" style="cursor: pointer; display: flex; align-items: center; gap: 10px;" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                    <img src="{{ asset('scr/logorose.jpeg') }}" alt="Rosemary Logo" class="logo">
                    <span>Rosemary Nutrition</span>
                </div>
                <input type="text" id="globalSearchInput" placeholder="Cari...">
                <div class="user">👤</div>
            </div>

            @yield('content')
        </main>
    </div>

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
