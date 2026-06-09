<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produks';

    protected $fillable = [
        'nama_produk',
        'kategori',
        'stok_produk',
        'harga_produk',
        'satuan',
        'tanggal_input'
    ];

    // Relasi ke penjualan
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'id_produk');
    }

    // Relasi ke produksi
    public function produksis()
    {
        return $this->hasMany(Produksi::class, 'id_produk');
    }
}