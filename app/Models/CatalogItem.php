<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'url',
        'description',
        'ingredients',
        'price',
    ];

    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
