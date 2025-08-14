<?php

namespace App\Repository;

use App\Repository\interfaces\ProfileUpdateRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileUpdateRepository implements ProfileUpdateRepositoryInterface
{
    public function getProfile()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Unauthenticated user.',
                ];
            }

            return [
                'success' => true,
                'user' => $user,
            ];
        } catch (\Throwable $e) {
            Log::error('Profile fetch error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch profile: ' . $e->getMessage(),
            ];
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Unauthenticated user.',
                ];
            }

            // Validate request
            $request->validate(
                [
                    'name' => ['required', 'regex:/^[A-Za-z]{4,}[A-Za-z0-9 ]*$/', 'max:25'],
                    'age' => ['required', 'integer', 'min:16', 'max:65'],
                    'weight' => ['required', 'numeric', 'min:45', 'max:160'],
                    'address' => ['required', 'regex:/^[A-Za-z0-9\s,\'.-]+$/', 'regex:/[A-Za-z]/', 'max:30'],
                    'phone' => ['required', 'digits:10'],
                    'blood_type' => ['required', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
                    'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
                    'current_password' => [
                        'required',
                        function ($attribute, $value, $fail) use ($user) {
                            if (!Hash::check($value, $user->password)) {
                                $fail('The current password is incorrect.');
                            }
                        },
                    ],
                    'new_password' => ['nullable', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/'],
                ],
                [
                    'name.regex' => 'Name must start with letters and may end with numbers.',
                    'age.min' => 'Age must be between 16 and 65.',
                    'weight.min' => 'Weight must be at least 45 kg and maximum 160 kg.',
                    'address.regex' => 'Address must contain alphabets and can include numbers, spaces, commas, apostrophes, and hyphens.',
                    'phone.digits' => 'Phone number must be exactly 10 digits.',
                    'email.email' => 'Please enter a valid email address.',
                    'email.unique' => 'This email is already taken.',
                    'blood_type.in' => 'Please select a valid blood type.',
                    'new_password.regex' => 'Password must contain at least 6 characters with one uppercase letter, one lowercase letter, one number, and one special character.',
                ],
            );

            $updateData = $request->only(['name', 'age', 'weight', 'address', 'phone', 'blood_type', 'email']);

            // Update password if provided
            if ($request->filled('new_password')) {
                $updateData['password'] = Hash::make($request->new_password);
            }

            $user->update($updateData);

            return [
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user,
            ];
        } catch (\Throwable $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage(),
            ];
        }
    }
}
