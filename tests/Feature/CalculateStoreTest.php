<?php

use App\Models\Client;
use App\Models\User;

test('calculate store accepts an existing client id', function () {
    $user = User::factory()->create();
    $client = Client::query()->create([
        'name' => 'Maria',
        'curp' => 'LOMM800101HDFPRR09',
        'birthdate' => '1980-01-01',
        'nss' => '12345678901',
        'unemployment_assistance_discounted_weeks' => 0,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('calculate.store'), [
            'client_id' => $client->id,
        ]);

    $response->assertRedirect(route('calculate'));
    $this->assertDatabaseCount('clients', 1);
    $this->assertDatabaseCount('client_family_information', 0);
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
        ->assertSessionHasErrors([
            'client.name',
            'client.curp',
            'client.birthdate',
            'client.nss',
            'client.unemployment_assistance_discounted_weeks',
            'family_information.has_spouse',
            'family_information.minor_or_student_children_count',
            'family_information.parents_count',
        ]);
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
                'phone' => '(55) 1234-5678',
                'curp' => 'paaa800101hdflll09',
                'birthdate' => '1980-01-01',
                'nss' => '12345678901',
                'regime_end_date' => '2024-12-31',
                'unemployment_assistance_discounted_weeks' => '4',
                'notes' => 'Cliente nuevo para calculo.',
            ],
            'family_information' => [
                'has_spouse' => '1',
                'minor_or_student_children_count' => '2',
                'parents_count' => '1',
            ],
        ]);

    $response->assertRedirect(route('calculate'));

    $this->assertDatabaseHas('clients', [
        'name' => 'Alfredo',
        'last_name' => 'Palacios',
        'phone' => '5512345678',
        'curp' => 'PAAA800101HDFLLL09',
        'birthdate' => '1980-01-01 00:00:00',
        'nss' => '12345678901',
        'regime_end_date' => '2024-12-31 00:00:00',
        'unemployment_assistance_discounted_weeks' => 4,
        'notes' => 'Cliente nuevo para calculo.',
    ]);

    $this->assertDatabaseHas('client_family_information', [
        'has_spouse' => true,
        'minor_or_student_children_count' => 2,
        'parents_count' => 1,
    ]);
});

test('calculate store rejects invalid client contact formats', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('calculate'))
        ->post(route('calculate.store'), [
            'client_id' => null,
            'client' => [
                'name' => 'Alfredo',
                'phone' => '55 1234',
                'email' => 'alfredo',
                'curp' => 'CURP_INVALIDA',
                'birthdate' => now()->addDay()->toDateString(),
                'nss' => '123',
                'unemployment_assistance_discounted_weeks' => '-1',
            ],
            'family_information' => [
                'has_spouse' => '1',
                'minor_or_student_children_count' => '-1',
                'parents_count' => '-1',
            ],
        ]);

    $response
        ->assertRedirect(route('calculate'))
        ->assertSessionHasErrors([
            'client.phone',
            'client.email',
            'client.curp',
            'client.birthdate',
            'client.nss',
            'client.unemployment_assistance_discounted_weeks',
            'family_information.minor_or_student_children_count',
            'family_information.parents_count',
        ]);
});
