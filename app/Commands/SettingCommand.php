<?php

namespace App\Commands;

use BotMan\BotMan\BotMan;

class SettingCommand extends Command
{
    /**
     * Get supported commands.
     *
     * @return array
     */
    public function getCommands() {
        return ['caidat', 'start', 'setting', 'config'];
    }

    public function isParamsRequired()
    {
        return false;
    }

    /**
     * Get command descriptions
     *
     * @return string
     */
    public function getDescriptions()
    {
        return 'Cài đặt một số thông tin để được hỗ trợ tốt hơn';
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
        $bot->reply("Hi {$user->getFirstName()}! Mình cần {$user->getFirstName()} cung cấp một số thông tin để có thể hỗ trợ {$user->getFirstName()} tốt hơn `$params`");
    }
}
