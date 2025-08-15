<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DonateBlood;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    // Create a test donor user
    $this->user = User::where('email', 'donor11@gmail.com')->first();
    if (!$this->user) {
        $this->user = User::factory()->create([
            'name' => 'Donor10',
            'email' => 'donor10@gmail.com',
            'password' => bcrypt('Donor@10'),
            'user_type' => 'donor',
        ]);
    }
    $this->actingAs($this->user, 'sanctum');
});

it('can view all donations', function () {
    $response = $this->getJson('/api/donations');

    $response->assertStatus(200);
});

it('can submit a new donation', function () {
    $filePath = base_path('tests/images/blood_card.jpg');
    $file = new \Illuminate\Http\UploadedFile(
        $filePath,
        'blood_card.jpg',
        'application/jpg',
        null,
        true, // $test = true so Laravel accepts it as a test file
    );

    $response = $this->postJson('/api/donations', [
        'admin_id' => 1,
        'user_name' => $this->user->name,
        'email' => $this->user->email,
        'phone' => '9800000010',
        'blood_type' => 'O+',
        'blood_quantity' => 1,
        'request_form' => $file,
    ]);

    $response->assertStatus(200)->assertJson([
        'success' => true,
    ]);
});

it('can update an existing donation', function () {
    $donation = DonateBlood::firstOrCreate([
        'user_id' => $this->user->id,
        'admin_id' => 1,
        'user_name' => $this->user->name,
        'email' => $this->user->email,
        'phone' => '9800000010',
        'blood_type' => 'O+',
        'blood_quantity' => 1,
        'request_form' => 'assets/donor_proofs/blood_card.jpg',
        'status' => 'pending',
        'donation_date' => now(),
    ]);

    $response = $this->putJson("/api/donations/{$donation->id}", [
        'blood_quantity' => 2,
    ]);

    $response->assertStatus(200)->assertJson([
        'success' => true,
    ]);
});

it('can delete a donation', function () {
    $donation = DonateBlood::firstOrCreate([
        'user_id' => $this->user->id,
        'admin_id' => 1,
        'user_name' => $this->user->name,
        'email' => $this->user->email,
        'phone' => '9810000010',
        'blood_type' => 'A+',
        'blood_quantity' => 1,
        'request_form' => 'assets/donor_proofs/sample.pdf',
        'status' => 'pending',
        'donation_date' => now(),
    ]);

    $response = $this->deleteJson("/api/donations/{$donation->id}");

    $response->assertStatus(200)->assertJson([
        'success' => true,
    ]);
});

it('can check eligibility', function () {
    $response = $this->getJson("/api/donations/eligibility/{$this->user->id}");

    $response->assertStatus(200)->assertJsonStructure(['eligible', 'next_donation_date']);
});

it('can find nearby admins', function () {
    $response = $this->postJson('/api/donations/nearby-admins', [
        'latitude' => 27.700769,
        'longitude' => 85.30014,
    ]);

    $response->assertStatus(200)->assertJsonStructure([
        '*' => ['id', 'name', 'latitude', 'longitude', 'distance'],
    ]);
});
