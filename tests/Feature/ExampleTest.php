<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;  //The user is stored temporarily
uses(RefreshDatabase::class);

it('checks if true is true', function () {
    expect(true)->toBeTrue();
});

it('adds two numbers', function () {
    $sum = 2 + 3;
    expect($sum)->toBe(5);
});

it('checks multiple things', function () {
    expect(10)->toBeGreaterThan(5);
    expect('hello')->toStartWith('he');
    expect([1, 2, 3])->toHaveCount(3);
});





// test('assert_true',function()
// {
//     // $response = $this->get('/');

//     //     $response->assertStatus(404);
//     $this->assertTrue(true);
// });
