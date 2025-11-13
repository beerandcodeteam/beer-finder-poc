<?php

use App\Livewire\Store\Create;
use App\Livewire\Store\Index;
use App\Livewire\Store\Update;
use App\Models\Store;
use App\Models\User;
use Livewire\Livewire;

test('admin can see all stores in listing', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $otherUser = User::factory()->create(['is_admin' => false]);

    $adminStore = Store::factory()->create(['user_id' => $admin->id, 'name' => 'Admin Store']);
    $otherStore = Store::factory()->create(['user_id' => $otherUser->id, 'name' => 'Other Store']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->assertSee('Admin Store')
        ->assertSee('Other Store');
});

test('non-admin can only see their own stores in listing', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $otherUser = User::factory()->create(['is_admin' => false]);

    $userStore = Store::factory()->create(['user_id' => $user->id, 'name' => 'My Store']);
    $otherStore = Store::factory()->create(['user_id' => $otherUser->id, 'name' => 'Other Store']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSee('My Store')
        ->assertDontSee('Other Store');
});

test('any authenticated user can access store create page', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('stores.create'))
        ->assertSuccessful();
});

test('user can access their own store update page', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $store = Store::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('stores.update', $store))
        ->assertSuccessful();
});

test('user cannot access other users store update page', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $otherUser = User::factory()->create(['is_admin' => false]);
    $store = Store::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user)
        ->get(route('stores.update', $store))
        ->assertForbidden();
});

test('admin can access any store update page', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create(['is_admin' => false]);
    $store = Store::factory()->create(['user_id' => $user->id]);

    $this->actingAs($admin)
        ->get(route('stores.update', $store))
        ->assertSuccessful();
});

test('user can create store via livewire', function () {
    $user = User::factory()->create(['is_admin' => false]);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form.name', 'Test Store')
        ->set('form.slug', 'test-store')
        ->set('form.website', 'https://test.com')
        ->set('form.phone', '1234567890')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('stores.index'));

    expect(Store::where('name', 'Test Store')->exists())->toBeTrue();
    expect(Store::where('name', 'Test Store')->first()->user_id)->toBe($user->id);
});

test('admin can create store via livewire', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('form.name', 'Admin Store')
        ->set('form.slug', 'admin-store')
        ->set('form.website', 'https://admin.com')
        ->set('form.phone', '0987654321')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('stores.index'));

    expect(Store::where('name', 'Admin Store')->exists())->toBeTrue();
});

test('user can update their own store via livewire', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $store = Store::factory()->create(['user_id' => $user->id, 'name' => 'Original Name']);

    Livewire::actingAs($user)
        ->test(Update::class, ['store' => $store])
        ->set('form.name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('stores.index'));

    expect($store->fresh()->name)->toBe('Updated Name');
});

test('user cannot update other users store via livewire', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $otherUser = User::factory()->create(['is_admin' => false]);
    $store = Store::factory()->create(['user_id' => $otherUser->id, 'name' => 'Original Name']);

    Livewire::actingAs($user)
        ->test(Update::class, ['store' => $store])
        ->assertForbidden();

    expect($store->fresh()->name)->toBe('Original Name');
});

test('admin can update any store via livewire', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create(['is_admin' => false]);
    $store = Store::factory()->create(['user_id' => $user->id, 'name' => 'Original Name']);

    Livewire::actingAs($admin)
        ->test(Update::class, ['store' => $store])
        ->set('form.name', 'Admin Updated')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('stores.index'));

    expect($store->fresh()->name)->toBe('Admin Updated');
});

test('user can delete their own store via livewire', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $store = Store::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('remove', $store)
        ->assertHasNoErrors();

    expect(Store::find($store->id))->toBeNull();
});

test('user cannot delete other users store via livewire', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $otherUser = User::factory()->create(['is_admin' => false]);
    $store = Store::factory()->create(['user_id' => $otherUser->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('remove', $store)
        ->assertForbidden();

    expect(Store::find($store->id))->not->toBeNull();
});

test('admin can delete any store via livewire', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create(['is_admin' => false]);
    $store = Store::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('remove', $store)
        ->assertHasNoErrors();

    expect(Store::find($store->id))->toBeNull();
});

test('all authenticated users can view stores list page', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('stores.index'))
        ->assertSuccessful();
});
