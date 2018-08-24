<?php

namespace App\Commands;

use BotMan\BotMan\BotMan;

abstract class Command
{
    const PARSE_MODE_MARKDOWN = ['parse_mode' => 'markdown'];
    const PARSE_MODE_HTML = ['parse_mode' => 'html'];

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
     * @return array string[]
     */
    abstract public function getCommands();

    /**
     * Get command descriptions
     *
     * @return string
     */
    abstract public function getDescriptions();

    /**
     * Handle the event.
     *
     * @param BotMan $bot
     * @param string $params
     * @return void
     */
    abstract public function handle(BotMan $bot, string $params = null);
}