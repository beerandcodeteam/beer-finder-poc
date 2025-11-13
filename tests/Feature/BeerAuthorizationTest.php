<?php

use App\Livewire\Beer\Create;
use App\Livewire\Beer\Index;
use App\Livewire\Beer\Update;
use App\Models\Beer;
use App\Models\User;
use Livewire\Livewire;

test('admin can access beer create page', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get(route('beers.create'))
        ->assertSuccessful();
});

test('non-admin cannot access beer create page', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('beers.create'))
        ->assertForbidden();
});

test('admin can access beer update page', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $beer = Beer::factory()->create();

    $this->actingAs($admin)
        ->get(route('beers.update', $beer))
        ->assertSuccessful();
});

test('non-admin cannot access beer update page', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $beer = Beer::factory()->create();

    $this->actingAs($user)
        ->get(route('beers.update', $beer))
        ->assertForbidden();
});

test('admin can create beer via livewire', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('form.name', 'Test Beer')
        ->set('form.tagline', 'Test tagline')
        ->set('form.description', 'Test description')
        ->set('form.abv', 5.5)
        ->set('form.ibu', 40)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('beers.index'));

    expect(Beer::where('name', 'Test Beer')->exists())->toBeTrue();
});

test('non-admin cannot create beer via livewire', function () {
    $user = User::factory()->create(['is_admin' => false]);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form.name', 'Test Beer')
        ->set('form.tagline', 'Test tagline')
        ->set('form.description', 'Test description')
        ->set('form.abv', 5.5)
        ->set('form.ibu', 40)
        ->call('save')
        ->assertForbidden();
});

test('admin can update beer via livewire', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $beer = Beer::factory()->create(['name' => 'Original Name']);

    Livewire::actingAs($admin)
        ->test(Update::class, ['beer' => $beer])
        ->set('form.name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('beers.index'));

    expect($beer->fresh()->name)->toBe('Updated Name');
});

test('non-admin cannot update beer via livewire', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $beer = Beer::factory()->create(['name' => 'Original Name']);

    Livewire::actingAs($user)
        ->test(Update::class, ['beer' => $beer])
        ->assertForbidden();
});

test('admin can delete beer via livewire', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $beer = Beer::factory()->create();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('remove', $beer)
        ->assertHasNoErrors();

    expect(Beer::find($beer->id))->toBeNull();
});

test('non-admin cannot delete beer via livewire', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $beer = Beer::factory()->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('remove', $beer)
        ->assertForbidden();

    expect(Beer::find($beer->id))->not->toBeNull();
});

test('all authenticated users can view beers list', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('beers.index'))
        ->assertSuccessful();
});
