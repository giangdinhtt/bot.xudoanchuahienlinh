<?php

namespace App\Providers\BotMan;

use App\Commands\AbsentCommand;
use App\Commands\CommandManager;
use App\Commands\SearchCommand;
use App\Commands\SettingCommand;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Container\LaravelContainer;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Storages\Drivers\FileStorage;
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
            $storage = new FileStorage(storage_path('botman'));

            $botman = BotManFactory::create($config, new LaravelCache(), $app->make('request'), $storage);

            $botman->setContainer(new LaravelContainer($this->app));

            return $botman;
        });

        $this->app->singleton('command-manager', function($app) {
            $manager = new CommandManager();
            $manager->register(new SettingCommand());
            $manager->register(new SearchCommand());
            $manager->register(new AbsentCommand());

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
