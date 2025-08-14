<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\interfaces\RegisterRepositoryInterface;

class RegisterController extends Controller
{
    public function __construct(protected RegisterRepositoryInterface $registerRepo)
    {
        $this->registerRepo = $registerRepo;
    }

    public function register(Request $request)
    {
        try {
            // Validate incoming data using repository
            $validatedData = $this->registerRepo->validateUserData($request->all());

            // Create the user
            $user = $this->registerRepo->createUser($validatedData);

            // Return success response without token
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Registration successful',
                    'user' => $user,
                ],
                201,
            );
        } catch (\Exception $e) {
            // Handle validation or other errors
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                422,
            );
        }
    }
}
