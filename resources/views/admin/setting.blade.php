@extends('layouts.admin')

@section('title', 'Settings - Rosemary Nutrition')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/material.css') }}">
<style>
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr;
        max-width: 600px;
        gap: 25px;
        margin-top: 20px;
    }
    .settings-card {
        background: white;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .settings-card h3 {
        margin-bottom: 20px;
        color: #6d0f1b;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        font-size: 14px;
    }
    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div id="settings-view">
    <h2>Pengaturan Sistem</h2>
    <p>Kelola keamanan dan pengguna sistem.</p>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #c3e6cb; max-width: 600px;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #f5c6cb; max-width: 600px;">
            <strong>Gagal:</strong> {{ $errors->first() }}
        </div>
    @endif

    <div class="settings-grid">
        <!-- Ganti Password -->
        <div class="settings-card">
            <h3>Ganti Password</h3>
            <form action="{{ route('admin.update_password') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Password Saat Ini</label>
                    <input type="password" name="current_password" placeholder="********" required>
                </div>
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" placeholder="********" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" placeholder="********" required>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">Perbarui Password</button>
            </form>
        </div>
    </div>
</div>
@endsection