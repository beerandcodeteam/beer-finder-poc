<?php

namespace App\Livewire\Forms;

use App\Models\Beer;
use App\Services\BeerService;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;
use Livewire\Form;
use Masmerise\Toaster\Toaster;

class BeerForm extends Form
{

    public ?Beer $beer = null;

    public string $name = '';
    public string $tagline = '';
    public string $description = '';
    public string $first_brewed_date = '';
    public string $abv = '';
    public string $ibu = '';
    public string $ebc = '';
    public string $ph = '';
    public string $volume = '';
    public string $ingredients = '';
    public string $brewer_tips = '';
    protected BeerService $beerService;

    public function setBeer(Beer $beer): void
    {
        $this->beer = $beer;
        $this->name = $beer->name;
        $this->tagline = $beer->tagline;
        $this->description = $beer->description;
        $this->first_brewed_date = $beer->first_brewed_date->format('Y-m-d');
        $this->abv = (string) $beer->abv;
        $this->ibu = (string) $beer->ibu;
        $this->ebc = (string) $beer->ebc;
        $this->ph = (string) $beer->ph;
        $this->volume = (string) $beer->volume;
        $this->ingredients = $beer->ingredients;
        $this->brewer_tips = $beer->brewer_tips;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'first_brewed_date' => ['required', 'date'],
            'abv' => ['required', 'numeric', 'min:0', 'max:100'],
            'ibu' => ['required', 'integer', 'min:0', 'max:200'],
            'ebc' => ['required', 'integer', 'min:0', 'max:100'],
            'ph' => ['required', 'numeric', 'min:0', 'max:14'],
            'volume' => ['required', 'integer', 'min:1'],
            'ingredients' => ['required', 'string'],
            'brewer_tips' => ['required', 'string'],
        ];
    }

    /**
     * Create a new beer.
     */
    public function store(): void
    {
        Beer::create($this->validate());
    }

    /**
     * Update the beer.
     */
    public function update(): void
    {
        $this->beer->update($this->validate());
    }
}
