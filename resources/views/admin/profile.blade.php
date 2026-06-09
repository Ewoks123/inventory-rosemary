@extends('layouts.material')

@section('title', 'Profile - Rosemary Nutrition')

@push('styles')
<style>
    .profile-container {
        max-width: 600px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(109, 15, 27, 0.1);
        text-align: center;
    }
    .profile-header {
        margin-bottom: 30px;
    }
    .profile-avatar {
        width: 100px;
        height: 100px;
        background: #6d0f1b;
        color: white;
        font-size: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 15px;
    }
    .profile-info {
        text-align: left;
        margin-top: 20px;
    }
    .info-group {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    .info-label {
        font-size: 14px;
        color: #888;
        margin-bottom: 5px;
    }
    .info-value {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
</style>
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">👤</div>
        <h2>Profil Pengguna</h2>
    </div>
    
    <div class="profile-info">
        <div class="info-group">
            <div class="info-label">Nama</div>
            <div id="display-name" class="info-value">Rosemary</div>
        </div>
        <div class="info-group">
            <div class="info-label">Posisi</div>
            <div id="display-role" class="info-value">Administrator</div>
        </div>
    </div>

    <div style="margin-top: 30px;">
        <button onclick="window.history.back()" class="btn-secondary" style="background-color: #6d0f1b; color: white; padding: 10px 25px; border-radius: 8px; border: none; cursor: pointer;">Kembali</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/script.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const userData = localStorage.getItem('currentUser');
        if (userData) {
            const user = JSON.parse(userData);
            document.getElementById('display-name').textContent = user.username;
            document.getElementById('display-role').textContent = user.role || 'Admin';
        }
    });
</script>
@endpush
