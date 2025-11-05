<?php

namespace App\Services;

use App\Models\Beer;

class BeerService
{

    public function getBeers(array $filters, string $sortBy, string $sortDirection)
    {
        $query = Beer::query();

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['prop_filter'])) {
            $query->where($filters['prop_filter'], $filters['prop_filter_rule'], $filters['prop_filter_value']);
        }

        // Ordenando
        if ($sortBy && $sortDirection) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->get();
    }

    public function store($data): Beer
    {
        return Beer::create($data);
    }

}
