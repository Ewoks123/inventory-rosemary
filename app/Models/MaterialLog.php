<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialLog extends Model
{
    protected $fillable = [
        'material_id',
        'type',
        'quantity',
        'unit',
        'price',
        'date',
        'note'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
