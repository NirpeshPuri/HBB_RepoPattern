<?php

namespace App\Repository\interfaces;

use Illuminate\Http\Request;
use App\Models\User;

interface UserRepositoryInterface
{
    public function receiverDashboard();
    public function donorDashboard();
    public function index();
    public function destroy(User $user);
}
