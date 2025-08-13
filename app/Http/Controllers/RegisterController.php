<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\interfaces\RegisterRepositoryInterface;

class RegisterController extends Controller
{
    private $registerRepo;

    public function __construct(RegisterRepositoryInterface $registerRepo)
    {
        $this->registerRepo = $registerRepo;
    }

    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        try {
            $validatedData = $this->registerRepo->validateUserData($request->all());
            $this->registerRepo->createUser($validatedData);
            
            return redirect()->route('login')->with('success', 'Registration successful! Please login.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }
    }
}