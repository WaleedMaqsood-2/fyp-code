<?php

namespace App\Providers;

use App\Services\WhisperApiService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
          $this->app->singleton(WhisperApiService::class, function ($app) {
        return new WhisperApiService();
    });
        $this->app->register(\App\Providers\NotificationServiceProvider::class);
    }


    /**
     * Bootstrap any application services.
     */
    // app/Providers/AppServiceProvider.php

    public function boot(): void
    {
        Paginator::useBootstrapFive();


       
 Gate::define('admin', function (User $user) {
    return  $user->role->role_name === 'Admin';
});

    }

    // app/Providers/AppServiceProvider.php میں


}