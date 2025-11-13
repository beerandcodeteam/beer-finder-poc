<?php

use App\Models\Store;
use App\Models\User;
use App\Policies\StorePolicy;

beforeEach(function () {
    $this->policy = new StorePolicy();
});

test('admin can view any store', function () {
    $admin = User::factory()->make(['is_admin' => true]);
    $store = Store::factory()->make(['user_id' => 999]);

    expect($this->policy->view($admin, $store))->toBeTrue();
});

test('user can view their own store', function () {
    $user = User::factory()->make(['id' => 1, 'is_admin' => false]);
    $store = Store::factory()->make(['user_id' => 1]);

    expect($this->policy->view($user, $store))->toBeTrue();
});

test('user cannot view other users store', function () {
    $user = User::factory()->make(['id' => 1, 'is_admin' => false]);
    $store = Store::factory()->make(['user_id' => 2]);

    expect($this->policy->view($user, $store))->toBeFalse();
});

test('admin can update any store', function () {
    $admin = User::factory()->make(['is_admin' => true]);
    $store = Store::factory()->make(['user_id' => 999]);

    expect($this->policy->update($admin, $store))->toBeTrue();
});

test('user can update their own store', function () {
    $user = User::factory()->make(['id' => 1, 'is_admin' => false]);
    $store = Store::factory()->make(['user_id' => 1]);

    expect($this->policy->update($user, $store))->toBeTrue();
});

test('user cannot update other users store', function () {
    $user = User::factory()->make(['id' => 1, 'is_admin' => false]);
    $store = Store::factory()->make(['user_id' => 2]);

    expect($this->policy->update($user, $store))->toBeFalse();
});

test('admin can delete any store', function () {
    $admin = User::factory()->make(['is_admin' => true]);
    $store = Store::factory()->make(['user_id' => 999]);

    expect($this->policy->delete($admin, $store))->toBeTrue();
});

test('user can delete their own store', function () {
    $user = User::factory()->make(['id' => 1, 'is_admin' => false]);
    $store = Store::factory()->make(['user_id' => 1]);

    expect($this->policy->delete($user, $store))->toBeTrue();
});

test('user cannot delete other users store', function () {
    $user = User::factory()->make(['id' => 1, 'is_admin' => false]);
    $store = Store::factory()->make(['user_id' => 2]);

    expect($this->policy->delete($user, $store))->toBeFalse();
});

test('any authenticated user can create stores', function () {
    $user = User::factory()->make(['is_admin' => false]);
    $admin = User::factory()->make(['is_admin' => true]);

    expect($this->policy->create($user))->toBeTrue();
    expect($this->policy->create($admin))->toBeTrue();
});

test('any authenticated user can view any stores list', function () {
    $user = User::factory()->make(['is_admin' => false]);
    $admin = User::factory()->make(['is_admin' => true]);

    expect($this->policy->viewAny($user))->toBeTrue();
    expect($this->policy->viewAny($admin))->toBeTrue();
});
