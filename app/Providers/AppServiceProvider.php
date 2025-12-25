<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force root URL for subdirectory installations
        URL::forceRootUrl(config('app.url'));

        // Configure Livewire to use correct update route for subdirectory
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/codexse/public/livewire/update', $handle)
                ->middleware('web')
                ->name('livewire.update');
        });
    }
}
