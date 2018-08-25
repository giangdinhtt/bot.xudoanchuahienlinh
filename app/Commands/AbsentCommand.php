<?php

namespace App\Commands;

use BotMan\BotMan\BotMan;

class AbsentCommand extends Command
{
    /**
     * Get supported commands.
     *
     * @return array
     */
    public function getCommands() {
        return ['diemdanh', 'vang', 'absent'];
    }

    /**
     * Get command descriptions
     *
     * @return string
     */
    public function getDescriptions()
    {
        return 'Điểm danh vắng mặt';
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
        $bot->reply("Hi {$user->getFirstName()}! Bạn đang tìm kiếm `$params`");
    }
}
