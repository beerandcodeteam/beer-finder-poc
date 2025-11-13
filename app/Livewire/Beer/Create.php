<?php

namespace App\Livewire\Beer;

use App\Jobs\ProcessBeer;
use App\Livewire\Forms\BeerForm;
use App\Services\BeerService;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    public BeerForm $form;

    protected BeerService $beerService;

    public function boot(BeerService $beerService): void
    {
        $this->beerService = $beerService;
    }

    public function save()
    {
        $this->authorize('create', \App\Models\Beer::class);

        try {

            $beer = $this->form->store();

            dispatch(new ProcessBeer(beer: $beer));

            return redirect(route('beers.index'))
                ->success("{$this->form->name} Criada com sucesso!");

        } catch (\Exception $e) {
            Toaster::error($e->getMessage());
        }

    }

    public function render()
    {
        return view('livewire.beer.create');
    }
}
