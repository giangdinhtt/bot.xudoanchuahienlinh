<?php

namespace App\Listeners\Bot;

use \BotMan\BotMan\BotMan;

abstract class CommandListener
{
    /**
     * Create the command listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get command that handled
     *
     * @return array
     */
    abstract public function getCommands();

    /**
     * Handle the event.
     *
     * @param BotMan $bot
     * @param array $parameters
     * @return void
     */
    abstract public function handle(BotMan $bot, array $parameters = null);
}
