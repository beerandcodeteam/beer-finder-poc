<?php

namespace App\Livewire\Store;

use App\Livewire\Forms\StoreForm;
use App\Models\Store;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Update extends Component
{
    public StoreForm $form;

    public Store $store;

    public function mount(Store $store): void
    {
        $this->store = $store;
        $this->form->setStore($store);
    }

    public function save(): void
    {
        $this->form->update();

        Toaster::success('Loja atualizada com sucesso!');

        $this->redirect(route('stores.index'), navigate: true);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.store.update');
    }
}
