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

    /**
     * Boot the botman services for the application.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->rglob("app/*.php", 0) as $filename)
        {
            if (class_exists($filename)) continue;
            include $filename;
            //$classes = get_declared_classes();
            //$class = end($classes);

            $commands  = array();
            foreach(get_declared_classes() as $class){
                if($class instanceof \App\Listeners\Bot\CommandListener) $commands[] = $class;
            }
            \Log::info($commands);

        }
    }

    public function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags); 
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}
