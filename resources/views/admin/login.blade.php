<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rosemary Nutrition</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('scr/logorose.jpeg') }}" alt="Logo Rosemary" class="logo">
                <h1>Rosemary<br>Nutrition</h1>
            </div>
            <p class="subtitle">Inventory Management System</p>
            
            <form method="POST" action="{{ route('admin.authenticate') }}">
                @csrf
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" value="{{ old('username') }}" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn-login">Masuk ke Dashboard</button>
            </form>
            
            @if($errors->any())
            <div id="errorMessage" class="error-message">
                {{ $errors->first() }}
            </div>
            @endif
        </div>
    </div>
</body>
</html>
