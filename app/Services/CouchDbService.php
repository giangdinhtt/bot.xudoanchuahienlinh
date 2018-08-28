<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CouchDbService
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
