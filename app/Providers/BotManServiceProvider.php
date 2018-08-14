<?php

namespace App\Providers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Support\ServiceProvider;

class BotManServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('botman', function($app) {
            $config = [
                // Your driver-specific configuration
                "telegram" => [
                    "token" => env('TELEGRAM_TOKEN')
                ]
            ];

            // Load the driver(s) you want to use
            DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);

            // Create an instance
            $botman = BotManFactory::create($config);

            return $botman;
        });

        if (file_exists('routes/botman.php')) {
            require base_path('routes/botman.php');
        }
    }
}
