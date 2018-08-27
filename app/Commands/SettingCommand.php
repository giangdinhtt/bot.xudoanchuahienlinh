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
        $bot->reply("Hi {$user->getFirstName()}! Mình cần {$user->getFirstName()} cung cấp một số thông tin để có thể hỗ trợ {$user->getFirstName()} tốt hơn");
        $bot->ask('Số điện thoại bạn đang dùng là gì?', function (Answer $answer) use ($bot) {

            if ($answer->isInteractiveMessageReply()) {
                \Log::error($answer->getValue());
            }
            $bot->reply("Mình đã ghi nhận, cám ơn {$bot->getUser()->getFirstName()} nhé!");
            \Log::error($bot->getMessage->getPayload());
        }, ['reply_markup' => json_encode([
            'keyboard' => [[['text' => 'Đồng ý cung cấp số điện thoại', 'request_contact' => true]]]
        ])]);
    }
}
