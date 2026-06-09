@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Riwayat Stok Material')

@section('content')
<div class="log-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Riwayat Transaksi Material</h2>
        <a href="{{ route('materials.actual') }}" class="btn-secondary">Kembali</a>
    </div>
    <div class="table-container">
        <table id="historyTable">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Material</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/material.js') }}"></script>
@endpush