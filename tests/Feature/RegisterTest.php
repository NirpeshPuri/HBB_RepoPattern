<?php

namespace Tests\Feature;

use Tests\TestCase;

uses(TestCase::class);

it('allows a new user to register', function () {
    $userData = [
        'name' => 'Donor11',
        'email' => 'donor11@gmail.com',
        'password' => 'Donor@11',
        'password_confirmation' => 'Donor@11',
        'user_type' => 'donor',
        'age' => 30,
        'weight' => 70,
        'address' => 'Kathmandu',
        'phone' => '9800000010',
        'blood_type' => 'O+',
    ];

    $response = $this->postJson('/api/register', $userData);

    // Check status and JSON structure
    $response->assertStatus(201)->assertJsonStructure(['success', 'message', 'user' => ['id', 'name', 'email', 'user_type', 'age', 'weight', 'address', 'phone', 'blood_type', 'created_at', 'updated_at']]);

    $json = $response->json();
    expect($json['success'])->toBeTrue();
    expect($json['user']['email'])->toBe($userData['email']);
})->skip('Skipping register test for now');
