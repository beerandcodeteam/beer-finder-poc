<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tagline',
        'description',
        'first_brewed_date',
        'abv',
        'ibu',
        'ebc',
        'ph',
        'volume',
        'ingredients',
        'brewer_tips',
    ];

    protected function casts(): array
    {
        return [
            'first_brewed_date' => 'date',
            'abv' => 'decimal:2',
            'ph' => 'decimal:2',
        ];
    }

    public function stores(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Store::class)
            ->using(BeerStore::class)
            ->withPivot(['price', 'url', 'promo_label', 'deleted_at'])
            ->withTimestamps();
    }

    public function image(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function coverImage(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_cover', true);
    }
}
