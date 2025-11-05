<?php

namespace App\Livewire\Components;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageUploader extends Component
{
    use WithFileUploads;

    public ?Model $model = null;

    public string $storagePath = 'images';

    public array $images = [];

    public array $existingImages = [];

    /**
     * Mount the component.
     */
    public function mount(?Model $model = null, string $storagePath = 'images'): void
    {
        $this->model = $model;
        $this->storagePath = $storagePath;

        if ($this->model && method_exists($this->model, 'image')) {
            $this->existingImages = $this->model->image()
                ->get()
                ->map(fn (Image $image) => [
                    'id' => $image->id,
                    'path' => $image->path,
                    'url' => Storage::url($image->path),
                    'is_cover' => $image->is_cover,
                ])
                ->toArray();
        }
    }

    /**
     * Set an image as cover.
     */
    public function setCover(int $imageId): void
    {
        if (! $this->model || ! method_exists($this->model, 'image')) {
            return;
        }

        $this->model->image()->update(['is_cover' => false]);
        $this->model->image()->where('id', $imageId)->update(['is_cover' => true]);

        $this->mount($this->model, $this->storagePath);
        $this->dispatch('image-cover-updated');
    }

    /**
     * Remove an existing image.
     */
    public function removeExistingImage(int $imageId): void
    {
        if (! $this->model || ! method_exists($this->model, 'image')) {
            return;
        }

        $image = $this->model->image()->find($imageId);

        if ($image) {
            Storage::delete($image->path);
            $image->delete();
        }

        $this->mount($this->model, $this->storagePath);
        $this->dispatch('image-deleted');
    }

    /**
     * Remove a pending upload image.
     */
    public function removePendingImage(int $index): void
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
    }

    /**
     * Save uploaded images to the model.
     */
    public function saveImages(): void
    {
        if (! $this->model || ! method_exists($this->model, 'image') || empty($this->images)) {
            return;
        }

        $this->validate([
            'images.*' => ['image', 'max:2048'],
        ]);

        $hasNoCover = $this->model->image()->where('is_cover', true)->doesntExist();

        foreach ($this->images as $index => $image) {
            $path = $image->store($this->storagePath, 'public');

            $this->model->image()->create([
                'path' => $path,
                'is_cover' => $hasNoCover && $index === 0,
            ]);
        }

        $this->images = [];
        $this->mount($this->model, $this->storagePath);
        $this->dispatch('images-saved');
    }

    /**
     * Render the component.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.components.image-uploader');
    }
}
