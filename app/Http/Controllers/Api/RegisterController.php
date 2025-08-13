<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repository\interfaces\RegisterRepositoryInterface;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    private $registerRepo;

    public function __construct(RegisterRepositoryInterface $registerRepo)
    {
        $this->registerRepo = $registerRepo;
    }

    public function register(Request $request)
    {
        try {
            $validatedData = $this->registerRepo->validateUserData($request->all());
            $user = $this->registerRepo->createUser($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors() ?? null
            ], 422);
        }
    }
}