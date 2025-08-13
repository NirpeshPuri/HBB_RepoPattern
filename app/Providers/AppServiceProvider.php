<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\interfaces\BloodBankRepositoryInterface;
use App\Repository\BloodBankRepository;
use App\Repository\interfaces\ContactRepositoryInterface;
use App\Repository\ContactRepository;
use App\Repository\interfaces\DonorRepositoryInterface;
use App\Repository\DonorRepository;
//use App\Repository\interfaces\LoginRepositoryInterface;
//use App\Repository\LoginRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind each repository interface to its implementation individually
        $this->app->bind(
            \App\Repository\interfaces\AdminRepositoryInterface::class,
            \App\Repository\AdminRepository::class
        );

        $this->app->bind(
            \App\Repository\interfaces\RegisterRepositoryInterface::class,
            \App\Repository\RegisterRepository::class
        );

        $this->app->bind(
            \App\Repository\interfaces\BloodSearchRepositoryInterface::class,
            \App\Repository\BloodSearchRepository::class
        );

        $this->app->bind(
            \App\Repository\interfaces\ProfileUpdateRepositoryInterface::class,
            \App\Repository\ProfileUpdateRepository::class
        );
        $this->app->bind(
            \App\Repository\interfaces\LoginRepositoryInterface::class,
            \App\Repository\LoginRepository::class
);

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
        //
    }
}
