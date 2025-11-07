<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'website',
        'phone',
        'opening_hours_json',
    ];

    protected function casts(): array
    {
        return [
            'opening_hours_json' => 'array',
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function beers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Beer::class)
            ->using(BeerStore::class)
            ->withPivot(['price', 'url', 'promo_label'])
            ->withTimestamps();
    }

    public function catalogItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CatalogItem::class);
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function coverImage(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_cover', true);
    }
}
