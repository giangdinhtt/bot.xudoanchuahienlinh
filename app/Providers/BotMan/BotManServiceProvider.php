<?php

namespace App\Providers\BotMan;

use App\Commands\CommandManager;
use App\Commands\SearchCommand;
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

        $this->app->singleton('command-manager', function($app) {
            $manager = new CommandManager();
            $manager->register(new SearchCommand());

            return $manager;
        });
    }

    /**
     * Boot the botman services for the application.
     *
     * @return void
     */
    public function boot()
    {
        if (file_exists('routes/botman.php')) {
            require base_path('routes/botman.php');
        }
    }
}
