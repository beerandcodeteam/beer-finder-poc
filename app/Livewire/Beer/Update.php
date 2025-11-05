<?php

namespace App\Livewire\Beer;

use App\Livewire\Forms\BeerForm;
use App\Models\Beer;
use App\Services\BeerService;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Update extends Component
{

    public BeerForm $form;
    public Beer $beer;

    protected BeerService $beerService;

    public function boot(BeerService $beerService): void
    {
        $this->beerService = $beerService;
    }

    public function mount(Beer $beer): void
    {
        $this->beer = $beer;
        $this->form->setBeer($beer);
    }

    public function save()
    {
        try {

            $this->form->update();
            return redirect(route('beers.index'))
                ->success("{$this->form->name} Criada com sucesso!");

        } catch (\Exception $e) {
            Toaster::error($e->getMessage());
        }

    }
    public function render()
    {
        return view('livewire.beer.update');
    }
}
