<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class CouchDbServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('couchdb_base', function($app) {
            $baseUrl = sprintf('http://%s:%s@%s/', urlencode(env('COUCHDB_USERNAME')), urlencode(env('COUCHDB_PASSWORD')), env('COUCHDB_HOST'));
            $client = new Client(['base_uri' => $baseUrl, 'headers' => [ 'Content-Type' => 'application/json']]);

            return $client;
        });
        $this->app->singleton('couchdb', function($app) {
            $baseUrl = sprintf('http://%s:%s@%s/%s/', urlencode(env('COUCHDB_USERNAME')), urlencode(env('COUCHDB_PASSWORD')), env('COUCHDB_HOST'), env('COUCHDB_DATABASE'));
            $client = new Client(['base_uri' => $baseUrl, 'headers' => [ 'Content-Type' => 'application/json']]);

            return $client;
        });
    }
}
