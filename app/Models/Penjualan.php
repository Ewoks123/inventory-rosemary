<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualans';

    protected $fillable = [
        'id_produk',
        'jumlah_terjual',
        'harga_satuan',
        'total_harga',
        'tanggal_penjualan'
    ];

    // Relasi ke produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}