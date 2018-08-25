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
     * `true` if command require parameters
     *
     * @return boolean
     */
    public function isParamsRequired()
    {
        return true;
    }

    /**
     * Get command descriptions
     *
     * @return string
     */
    abstract public function getDescriptions();


    /**
     * Get replying message for /help command
     *
     * @return string
     */
    public function getHelps()
    {
        $commands = $this->getCommands();
        if (!is_array($commands)) {
            $commands = [$commands];
        }
        $msg = '';
        foreach ($commands as $cmd) {
            $msg .= sprintf('/%s ', $cmd);
        }
        if ($this->isParamsRequired()) {
            $msg .= '_<nội dung>_ ';
        }
        $msg .= '- ' . $this->getDescriptions();
        return $msg;
    }

    /**
     * Handle the event.
     *
     * @param BotMan $bot
     * @param string $params
     * @return void
     */
    abstract public function handle(BotMan $bot, string $params = null);
}