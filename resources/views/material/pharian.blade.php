@extends('layouts.material')

@section('title', 'Rosemary Nutrition - Produksi Harian')

@section('content')
<div class="form-card">
    <div class="form-actions" style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
        <a href="{{ route('materials.menu') }}" class="button-link">&larr; Kembali</a>
        
        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; width: 100%; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; width: 100%; border: 1px solid #f5c6cb;">
                <strong>Gagal:</strong> Kolom semua wajib di isi dengan benar.
            </div>
        @endif

        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
            <div>
                <h2>Produksi Harian</h2>
                <p>Data produksi harian untuk stok material.</p>
            </div>
            <div>
                <a href="{{ route('materials.addpharian') }}" class="btn-primary" style="padding: 8px 15px; text-decoration: none; border-radius: 8px;">+ Tambah Produksi</a>
            </div>
        </div>
    </div>
</div>

<div class="summary-row">
    <div class="summary-card">
        <h3>Total Log</h3>
        <p id="summaryTotal">{{ $logs->count() }}</p>
    </div>
    <div class="summary-card">
        <h3>Total Masuk</h3>
        <p id="summaryIn">{{ rtrim(rtrim(number_format($totalIn, 3, ',', '.'), '0'), ',') ?: '0' }}</p>
    </div>
    <div class="summary-card">
        <h3>Total Keluar</h3>
        <p id="summaryOut">{{ rtrim(rtrim(number_format($totalOut, 3, ',', '.'), '0'), ',') ?: '0' }}</p>
    </div>
</div>

<div class="log-card">
    <h2>Daftar Penggunaan Material (Produksi)</h2>
    <div class="table-container">
        <table id="stockTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Material</th>
                    <th>Material Keluar</th>
                    <th>Satuan</th>
                    <th>Harga Supply (Total)</th>
                    <th>Jenis Transaksi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->date }}</td>
                    <td>{{ $log->material->nama_material }}</td>
                    <td style="color: #e74c3c; font-weight: bold;">-{{ rtrim(rtrim(number_format($log->quantity, 3, ',', '.'), '0'), ',') ?: '0' }}</td>
                    <td>{{ $log->unit }}</td>
                    <td>Rp {{ number_format($log->price, 0, ',', '.') }}</td>
                    <td>{{ $log->note }}</td>
                    <td>
                        <a href="{{ route('materials.pharian.edit', $log->id) }}" class="btn-primary" style="padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px; background-color: #f39c12;">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/material.js') }}"></script>
@endpush
