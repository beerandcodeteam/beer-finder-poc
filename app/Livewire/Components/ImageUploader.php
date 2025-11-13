<?php

namespace App\Livewire\Components;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

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

        if ($this->model && method_exists($this->model, 'images')) {
            $this->existingImages = $this->model->images()
                ->orderBy('is_cover', 'desc')
                ->get()
                ->map(fn (Image $image) => [
                    'id' => $image->id,
                    'path' => $image->path,
                    'url' => Storage::temporaryUrl($image->path, now()->addMinute()),
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
        if (! $this->model || ! method_exists($this->model, 'images')) {
            return;
        }

        $this->model->images()->update(['is_cover' => false]);
        $this->model->images()->where('id', $imageId)->update(['is_cover' => true]);

        $this->mount($this->model, $this->storagePath);
        Toaster::success('Capa atualizada com sucesso');
    }

    /**
     * Remove an existing image.
     */
    public function removeExistingImage(int $imageId): void
    {
        if (! $this->model || ! method_exists($this->model, 'images')) {
            return;
        }

        $image = $this->model->images()->find($imageId);

        if ($image) {
            Storage::delete($image->path);
            $image->delete();
        }

        $this->mount($this->model, $this->storagePath);
        Toaster::success('Imagem removida com sucesso');
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
        if (! $this->model || ! method_exists($this->model, 'images') || empty($this->images)) {
            return;
        }

        $this->validate([
            'images.*' => ['image', 'max:2048'],
        ]);

        $hasNoCover = $this->model->images()->where('is_cover', true)->doesntExist();

        foreach ($this->images as $index => $image) {
            $path = $image->store($this->storagePath);

            $this->model->images()->create([
                'path' => $path,
                'is_cover' => $hasNoCover && $index === 0,
            ]);
        }

        $this->images = [];
        $this->mount($this->model, $this->storagePath);
        Toaster::success('Imagens criada com sucesso');
    }

    /**
     * Render the component.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.components.image-uploader');
    }
}
