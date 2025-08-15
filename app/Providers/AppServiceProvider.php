<?php

namespace App\Providers;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Repository\interfaces\AdminRepositoryInterface;
use App\Repository\AdminRepository;
use App\Repository\interfaces\RegisterRepositoryInterface;
use App\Repository\RegisterRepository;
use App\Repository\interfaces\BloodSearchRepositoryInterface;
use App\Repository\BloodSearchRepository;
use App\Repository\interfaces\ProfileUpdateRepositoryInterface;
use App\Repository\ProfileUpdateRepository;
use App\Repository\interfaces\LoginRepositoryInterface;
use App\Repository\LoginRepository;
use App\Repository\interfaces\BloodBankRepositoryInterface;
use App\Repository\BloodBankRepository;
use App\Repository\interfaces\ContactRepositoryInterface;
use App\Repository\ContactRepository;
use App\Repository\interfaces\DonorRepositoryInterface;
use App\Repository\DonorRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(RegisterRepositoryInterface::class, RegisterRepository::class);
        $this->app->bind(BloodSearchRepositoryInterface::class, BloodSearchRepository::class);
        $this->app->bind(ProfileUpdateRepositoryInterface::class, ProfileUpdateRepository::class);
        $this->app->bind(LoginRepositoryInterface::class, LoginRepository::class);
        $this->app->bind(BloodBankRepositoryInterface::class, BloodBankRepository::class);
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(DonorRepositoryInterface::class, DonorRepository::class);
        //$this->app->bind(LoginRepositoryInterface::class, LoginRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));

        Route::middleware('web')->group(base_path('routes/web.php'));

        Schema::defaultStringLength(191);
    }
}
