<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $table = 'produksis';

    protected $fillable = [
        'id_produk',
        'jumlah_produksi',
        'tanggal_produksi'
    ];

    // Relasi ke produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    // Relasi ke material
    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material');
    }
}