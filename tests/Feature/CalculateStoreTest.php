<?php

use App\Models\Client;
use App\Models\User;

test('calculate store accepts an existing client id', function () {
    $user = User::factory()->create();
    $client = Client::query()->create([
        'name' => 'Maria',
        'curp' => 'LOMM800101HDFPRR09',
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('calculate.store'), [
            'client_id' => $client->id,
        ]);

    $response->assertRedirect(route('calculate'));
    $this->assertDatabaseCount('clients', 1);
});

test('calculate store rejects a missing existing client id', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('calculate'))
        ->post(route('calculate.store'), [
            'client_id' => 999,
        ]);

    $response
        ->assertRedirect(route('calculate'))
        ->assertSessionHasErrors('client_id');
});

test('calculate store rejects missing required new client fields', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('calculate'))
        ->post(route('calculate.store'), [
            'client_id' => null,
            'client' => [
                'name' => '',
                'curp' => '',
            ],
        ]);

    $response
        ->assertRedirect(route('calculate'))
        ->assertSessionHasErrors(['client.name', 'client.curp']);
});

test('calculate store creates a new client from required client fields', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('calculate.store'), [
            'client_id' => null,
            'client' => [
                'name' => 'Alfredo',
                'last_name' => 'Palacios',
                'phone' => '5512345678',
                'email' => 'alfredo@example.com',
                'curp' => 'PAAA800101HDFLLL09',
                'notes' => 'Cliente nuevo para calculo.',
            ],
        ]);

    $response->assertRedirect(route('calculate'));

    $this->assertDatabaseHas('clients', [
        'name' => 'Alfredo',
        'last_name' => 'Palacios',
        'phone' => '5512345678',
        'email' => 'alfredo@example.com',
        'curp' => 'PAAA800101HDFLLL09',
        'notes' => 'Cliente nuevo para calculo.',
    ]);
});
