<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Futureecom\Catalog\App\Contracts\SearchStrategy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(SearchStrategy::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
