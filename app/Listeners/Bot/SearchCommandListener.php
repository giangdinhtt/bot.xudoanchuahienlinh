<?php

namespace App\Listeners\Bot;

use \BotMan\BotMan\BotMan;

class SearchCommandListener extends CommandListener
{
    /**
     * Get supported commands.
     *
     * @return array
     */
    public function getCommands() {
        return ['search', 'tracuu', 'timkiem'];
    }

    /**
     * Handle the command.
     *
     * @param BotMan $bot
     * @param array $params
     * @return void
     */
    public function handle(BotMan $bot, array $params = null) {
        $user = $bot->getUser();
        $bot->reply("Hi {$user->getFirstName()}! Bạn đang dùng tìm kiếm `$params`");
    }
}
