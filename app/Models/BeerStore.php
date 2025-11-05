<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeerStore extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'beer_store';

    protected $fillable = [
        'beer_id',
        'store_id',
        'price',
        'url',
        'promo_label',
    ];

    public function beer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Beer::class);
    }

    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
