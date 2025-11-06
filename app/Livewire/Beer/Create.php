<?php

namespace App\Livewire\Beer;

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
        try {

            $this->form->store();

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
