<?php

namespace App\Livewire\Beer;

use App\Models\Beer;
use App\Services\BeerService;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class Index extends Component
{

    use WithPagination;
    public string $sortBy = '';
    public string $sortDirection = '';
    public array $filters = [];

    protected BeerService $beerService;

    public function boot(BeerService $beerService): void
    {
        $this->beerService = $beerService;
    }

    public function sort($sortBy)
    {
        $this->sortBy = $sortBy;
        $this->sortDirection = !empty($this->sortDirection) && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->resetPage();
    }

    public function filter()
    {

        $this->validate([
            'filters.name' => 'nullable|string|min:3|max:255',
            'filters.prop_filter' => 'nullable',
            'filters.prop_filter_rule' => 'required_with:filters.prop_filter',
            'filters.prop_filter_value' => 'required_with:filters.prop_filter_rule'
        ]);

        try {
            $this->resetPage();
        } catch (\Exception $e)
        {
            Toaster::error("Erro ao aplicar o filtro {$e->getMessage()}");
        }
    }

    public function remove(Beer $beer)
    {
        $beer->delete();
        Toaster::info("{$beer->name} removida com sucesso!");
    }


    public function render()
    {
        return view('livewire.beer.index', [
            'beers' => $this->beerService->getBeers($this->filters, $this->sortBy, $this->sortDirection),
        ]);
    }
}
