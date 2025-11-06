<?php

namespace App\Livewire\Forms;

use App\Models\Store;
use Illuminate\Support\Str;
use Livewire\Form;

class StoreForm extends Form
{
    public ?Store $store = null;

    public string $name = '';

    public string $slug = '';

    public string $website = '';

    public string $phone = '';

    public string $opening_hours_json = '';

    /**
     * Set the store to edit.
     */
    public function setStore(Store $store): void
    {
        $this->store = $store;
        $this->name = $store->name;
        $this->slug = $store->slug;
        $this->website = $store->website;
        $this->phone = $store->phone;
        $this->opening_hours_json = is_array($store->opening_hours_json)
            ? json_encode($store->opening_hours_json, JSON_PRETTY_PRINT)
            : '';
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        $unique_rule = 'unique:stores,slug';
        if ($this->store) {
            $unique_rule .= ',' . $this->store->id;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', $unique_rule],
            'website' => ['required', 'string', 'url', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'opening_hours_json' => ['nullable', 'json'],
        ];
    }

    /**
     * Generate slug from name.
     */
    public function generateSlug(): void
    {
        $this->slug = Str::slug($this->name);
    }

    /**
     * Create a new store.
     */
    public function store(): Store
    {
        $validated = $this->validate();
        $validated['user_id'] = auth()->id();
        $validated['opening_hours_json'] = $validated['opening_hours_json']
            ? json_decode($validated['opening_hours_json'], true)
            : null;

        return Store::create($validated);
    }

    /**
     * Update the store.
     */
    public function update(): void
    {
        $validated = $this->validate();
        $validated['opening_hours_json'] = $validated['opening_hours_json']
            ? json_decode($validated['opening_hours_json'], true)
            : null;

        $this->store->update($validated);
    }
}
