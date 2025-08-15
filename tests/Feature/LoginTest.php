<?php

namespace Tests\Feature;

use Tests\TestCase;

uses(TestCase::class);

it('allows an existing user to log in', function () {
    // Use database user credentials
    $response = $this->postJson('/api/login', [
        'email' => 'donor1@gmail.com',
        'password' => 'Donor@1',
        'user_type' => 'donor',
    ]);

    // Assert successful login
    $response->assertStatus(200)->assertJsonStructure(['success', 'message', 'access_token', 'token_type', 'user' => ['id', 'name', 'email', 'user_type']]);

    $json = $response->json();
    expect($json['success'])->toBeTrue();
    expect($json['access_token'])->not()->toBeNull();
});
