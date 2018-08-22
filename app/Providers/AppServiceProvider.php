<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!class_exists('StringHelper')) {
            class_alias('App\Helpers\StringHelper', 'StringHelper');
        }
    }
}
