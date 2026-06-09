<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';

    protected $fillable = [
        'nama_material',
        'jenis_material',
        'stok_material',
        'satuan',
        'supplier'
    ];

    // Relasi ke produksi
    public function produksis()
    {
        return $this->hasMany(Produksi::class, 'id_material');
    }

    public function logs()
    {
        return $this->hasMany(MaterialLog::class, 'material_id');
    }
}