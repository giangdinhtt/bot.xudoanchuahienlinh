<?php
use App\Http\Controllers\BotManController;

$botman = app('botman');

$botman->fallback(function($bot) {
    //$bot->reply('Chưa hiểu ý bạn lắm, bạn cần gì nè, gõ /help để biết thêm chi tiết nhé');
});

$botman->hears('^/(\w+)\s*([\w ]+)*$', function ($bot, $command = null, $arg = null) {
    $user = $bot->getUser();
    $bot->reply("Hi {$user->getFirstName()}! Bạn đang dùng command `$command` với tham số `$arg`");
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');
