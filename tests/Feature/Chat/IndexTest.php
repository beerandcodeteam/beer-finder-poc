<?php

use App\Livewire\Chat\Index;
use Livewire\Livewire;

test('chat page is accessible', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSeeLivewire(Index::class);
});

test('chat component renders correctly', function () {
    Livewire::test(Index::class)
        ->assertSee('Beer Finder Chat')
        ->assertSee('Pergunte sobre cervejas e receba recomendações!');
});

test('user can send a message', function () {
    Livewire::test(Index::class)
        ->set('message', 'Qual uma cerveja que fica boa para um churrasco com a familia')
        ->call('sendMessage')
        ->assertSet('message', '')
        ->assertCount('messages', 2);
});

test('message validation requires minimum length', function () {
    Livewire::test(Index::class)
        ->set('message', 'ab')
        ->call('sendMessage')
        ->assertHasErrors(['message']);
});

test('bot response includes beer recommendations', function () {
    Livewire::test(Index::class)
        ->set('message', 'Qual uma cerveja que fica boa para um churrasco')
        ->call('sendMessage')
        ->assertSet('message', '')
        ->assertCount('messages', 2)
        ->assertSee('Antartica Original');
});
