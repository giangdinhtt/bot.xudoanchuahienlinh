<?php

namespace App\Commands;

use App\Helpers\StringHelper;
use BotMan\BotMan\BotMan;
use Spatie\Emoji\Emoji;

class CommandManager
{
    public const COMMAND_PATTERN = '^/(\w+)\s*([\w ]+)*$';
    /**
     * The commands mapping
     *
     * @var Command[]
     */
    protected $commands = [];

    public function register(Command $command)
    {
        foreach ($command->getCommands() as $cmd) {
            $this->commands[$cmd] = $command;
        }
    }

    /**
     * @return string ^/(\w+)\s*([\w ]+)*$
     */
    public function getCommandPatterns()
    {
        $commands = array_keys($this->commands);
        $pattern = sprintf('^/(%s)\s*([\w ]+)*$', join('|', $commands));
        return self::COMMAND_PATTERN;
    }

    public function handle(BotMan $bot, string $cmd, string $params = null)
    {
        $this->preprocess($bot, $cmd, $params);
        $user = $bot->getUser();
        if (!array_key_exists($cmd, $this->commands)) {
            $msg = sprintf("Rất tiếc yêu cầu `/%s` của %s chưa được hỗ trợ %s, %s có thể dùng lệnh /help để thêm thông tin nhé", $cmd, $user->getFirstName(), Emoji::disappointedButRelievedFace(), $user->getFirstName());
            $bot->reply($msg, Command::PARSE_MODE_MARKDOWN);
            return;
        }
        $command = $this->commands[$cmd];
        $command->handle($bot, $params);
    }

    private function preprocess(BotMan $bot, string &$cmd, string &$params = null)
    {
        $bot->types();
        $cmd = strtolower($cmd);
        if ($params != null) {
            $params = StringHelper::standardize($params);
        }
    }
}