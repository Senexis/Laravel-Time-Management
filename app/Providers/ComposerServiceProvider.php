<?php

namespace App\Providers;

use View;
use Illuminate\Support\ServiceProvider;
use App\Http\Composers\HeaderComposer;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(HeaderComposer::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', HeaderComposer::class);
    }
}
