<?php

namespace App\Livewire\Store;

use App\Livewire\Forms\StoreForm;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    public StoreForm $form;

    public function save(): void
    {
        $store = $this->form->store();

        Toaster::success('Loja criada com sucesso!');

        $this->redirect(route('stores.index'), navigate: true);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.store.create');
    }
}
