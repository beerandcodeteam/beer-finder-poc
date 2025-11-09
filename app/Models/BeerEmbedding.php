<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeerEmbedding extends Model
{
    protected $fillable = [
        'beer_id',
        'text',
        'metadata',
        'embedding',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'json',
            'embedding' => 'json',
        ];
    }
}
