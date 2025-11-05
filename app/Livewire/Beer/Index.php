<?php

namespace App\Livewire\Beer;

use App\Models\Beer;
use App\Services\BeerService;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Index extends Component
{

    public $beers = [];
    public string $sortBy = '';
    public string $sortDirection = '';
    public array $filters = [];

    protected BeerService $beerService;

    public function boot(BeerService $beerService): void
    {
        $this->beerService = $beerService;
    }

    public function mount()
    {
        $this->beers = $this->beerService->getBeers([], $this->sortBy, $this->sortDirection);
    }

    public function sort($sortBy)
    {
        $this->sortBy = $sortBy;
        $this->sortDirection = !empty($this->sortDirection) && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->beers = $this->beerService->getBeers($this->filters, $this->sortBy, $this->sortDirection);
    }

    public function filter()
    {
        try {
            $this->validate([
                'filters.name' => 'nullable|string|min:3|max:255',
                'filters.prop_filter' => 'nullable',
                'filters.prop_filter_rule' => 'required_with:filters.prop_filter',
                'filters.prop_filter_value' => 'required_with:filters.prop_filter_rule'
            ]);
            $this->beers = $this->beerService->getBeers($this->filters, $this->sortBy, $this->sortDirection);
        } catch (\Exception $e)
        {
            Toaster::error("Erro ao aplicar o filtro");
        }
    }

    public function remove(Beer $beer)
    {
        $beer->delete();
        Toaster::info("{$beer->name} removida com sucesso!");
        $this->filter();
    }


    public function render()
    {
        return view('livewire.beer.index');
    }
}
