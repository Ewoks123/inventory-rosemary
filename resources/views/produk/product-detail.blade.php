@extends('layouts.material')

@section('title', 'Detail Produk')

@section('content')
<div class="form-card">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('produks.index') }}" class="button-link">&larr; Kembali</a>
    </div>
    <div id="productDetail"></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/product.js') }}"></script>
@endpush