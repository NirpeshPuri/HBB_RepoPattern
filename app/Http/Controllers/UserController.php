<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Repository\interfaces\UserRepositoryInterface;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function receiverDashboard()
    {
        return response()->json($this->userRepo->receiverDashboard());
    }

    public function donorDashboard()
    {
        return response()->json($this->userRepo->donorDashboard());
    }

    public function index()
    {
        $users = $this->userRepo->index();
        return response()->json($users);
    }

    public function destroy(User $user)
    {
        $result = $this->userRepo->destroy($user);
        return response()->json($result);
    }
}
