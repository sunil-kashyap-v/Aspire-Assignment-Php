<?php

namespace App\Providers;


use App\Repository\Implementations\AuthenticationImplementation;
use App\Repository\Implementations\LoanImplementation;
use App\Repository\Interfaces\AuthenticationInterface;
use App\Repository\Interfaces\LoanInterface;
use Illuminate\Support\ServiceProvider;

class InterfaceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthenticationInterface::class, AuthenticationImplementation::class);

        $this->app->bind(LoanInterface::class, LoanImplementation::class);
    }
}