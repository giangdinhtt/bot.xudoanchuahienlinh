<?php
use App\Http\Controllers\BotManController;

$botman = app('botman');

$botman->hears('Hi', function ($bot) {
echo 'aaa';
    $bot->reply('Hello!');
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');
