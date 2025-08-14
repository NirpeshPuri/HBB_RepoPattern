<?php

namespace App\Repository;

use App\Repository\interfaces\RegisterRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterRepository implements RegisterRepositoryInterface
{
    public function createUser(array $userData)
    {
        return User::create([
            'name' => $userData['name'],
            'age' => $userData['age'],
            'weight' => $userData['weight'],
            'address' => $userData['address'],
            'phone' => $userData['phone'],
            'blood_type' => $userData['blood_type'],
            'user_type' => $userData['user_type'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);
    }

    public function validateUserData(array $data): array
    {
        return Validator::make($data, [
            'name' => ['required', 'regex:/^[A-Za-z]+[A-Za-z0-9]*$/', 'max:25'],
            'age' => ['required', 'integer', 'min:16', 'max:65'],
            'weight' => ['required', 'numeric', 'min:45', 'max:160'],
            'address' => ['required', 'regex:/^[A-Za-z0-9\s,\'.-]+$/', 'regex:/[A-Za-z]/', 'max:30'],
            'phone' => ['required', 'digits:10'],
            'blood_type' => ['required', 'string'],
            'user_type' => ['required', 'in:receiver,donor'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/'
            ],
        ], [
            'name.regex' => 'Name must start with letters and may end with numbers.',
            'age.min' => 'Age must be between 16 and 65.',
            'weight.min' => 'Weight must be at least 45 kg and maximum 160 kg.',
            'address.regex' => 'Address must contain alphabets and can include numbers, spaces, commas, apostrophes, and hyphens.',
            'phone.digits' => 'Phone number must be exactly 10 digits.',
            'email.email' => 'Please enter a valid email address.',
            'password.regex' => 'Password must contain at least 6 characters with one uppercase letter, one lowercase letter, one number and one special character'
        ])->validate();
    }
}
