<?php

use App\Http\Controllers\BotManController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/botman', 'BotManController@handle');
$router->post('/botman', 'BotManController@handle');
$router->get('/botman/tinker', 'BotManController@tinker');

//$botman = app('botman');

//$botman->hears('Hi', function ($bot) {
//    $bot->reply('Hello!');
//});
//$botman->hears('Start conversation', BotManController::class.'@startConversation');
