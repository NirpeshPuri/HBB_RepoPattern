<?php

namespace App\Repository;

use App\Repository\interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function receiverDashboard()
    {
        return ['message' => 'Receiver dashboard']; // For API, just return JSON
    }

    public function donorDashboard()
    {
        return ['message' => 'Donor dashboard']; // For API, just return JSON
    }

    public function index()
    {
        $users = User::where('user_type', '!=', 'admin')
            ->select([
                'id',
                'name',
                'age',
                'weight',
                'address',
                'phone',
                'blood_type',
                'user_type',
                'email',
                'created_at'
            ])
            ->latest()
            ->paginate(10);

        return $users;
    }

    public function destroy(User $user)
    {
        if ($user->user_type === 'admin') {
            return ['success' => false, 'message' => 'Cannot delete admin users'];
        }

        $user->delete();
        return ['success' => true, 'message' => 'User deleted successfully'];
    }
}
