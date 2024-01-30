<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServicerProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->loadHelpers();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/../Helpers/*.php') as $filename)
        {
            require_once $filename;
        }
    }
}
