<?php

namespace App\Commands;

use BotMan\BotMan\BotMan;

class SearchCommand extends Command
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
     * Get command descriptions
     *
     * @return string
     */
    public function getDescriptions()
    {
        return 'Tìm kiếm thông tin theo tên, số điện thoại, facebook...';
    }


    /**
     * Handle the event.
     *
     * @param BotMan $bot
     * @param string $params
     * @return void
     */
    public function handle(BotMan $bot, string $params = null)
    {
        $user = $bot->getUser();
        $bot->reply("Hi {$user->getFirstName()}! Bạn đang dùng tìm kiếm `$params`");
    }
}
