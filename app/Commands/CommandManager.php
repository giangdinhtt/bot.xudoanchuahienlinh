<?php

namespace App\Commands;

use App\Helpers\StringHelper;
use BotMan\BotMan\BotMan;
use Spatie\Emoji\Emoji;

class CommandManager
{
    public const COMMAND_PATTERN = '^/(\w+)\s*([\w ]+)*$';

    /**
     * Supported commands
     *
     * @var Command[]
     */
    protected $commands = [];

    /**
     * The commands mapping
     *
     * @var Command[]
     */
    protected $commandMap = [];

    public function register(Command $command)
    {
        $this->commands[] = $command;
        foreach ($command->getCommands() as $cmd) {
            $this->commandMap[$cmd] = $command;
        }
    }

    /**
     * @return string ^/(\w+)\s*([\w ]+)*$
     */
    public function getCommandPatterns()
    {
        $commands = array_keys($this->commandMap);
        $pattern = sprintf('^/(%s)\s*([\w ]+)*$', join('|', $commands));
        return self::COMMAND_PATTERN;
    }

    public function handle(BotMan $bot, string $cmd, string $params = null)
    {
        $this->preprocess($bot, $cmd, $params);

        if (in_array($cmd, ['help', 'trogiup'])) {
            $this->replyHelps($bot, $params);
            return;
        }

        $user = $bot->getUser();
        if (!array_key_exists($cmd, $this->commandMap)) {
            $msg = sprintf("Rất tiếc yêu cầu `/%s` của %s chưa được hỗ trợ %s, %s có thể dùng lệnh /help hoặc /trogiup để biết thêm thông tin nhé", $cmd, $user->getFirstName(), Emoji::disappointedButRelievedFace(), $user->getFirstName());
            $bot->reply($msg, Command::PARSE_MODE_MARKDOWN);
            return;
        }

        $command = $this->commandMap[$cmd];
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

    public function replyHelps(BotMan $bot, string $params = null)
    {
        $bot->reply($this->getHelps($bot, $params), Command::PARSE_MODE_MARKDOWN);
    }

    private function getHelps(BotMan $bot, string $params = null)
    {
        $user = $bot->getUser();
        $msg = "{$user->getFirstName()} cần mình giúp gì nà " . Emoji::heartWithRibbon() . "\n";
        foreach ($this->commands as $command) {
            $msg .= "\n";
            $msg .= $command->getHelps();
            $msg .= "\n";
        }
        return $msg;
    }
}