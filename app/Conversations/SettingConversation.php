<?php

namespace App\Conversations;

use App\Commands\Command;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Spatie\Emoji\Emoji;

class SettingConversation extends Conversation
{
    protected $role;

    protected $phone;

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askRole();
    }

    public function askRole()
    {
        $question = Question::create("{$this->getUserFirstName()} là phụ huynh hay huynh trưởng?")
            ->fallback("Mình chưa hiểu câu trả lời lắm " . Emoji::disappointedFace())
            ->callbackId('ask-role')
            ->addButtons([
                Button::create("Tôi là phụ huynh")->value('parent'),
                Button::create("Tôi là huynh trưởng")->value('teacher'),
                Button::create("Tôi giữ chức vụ khác")->value('other')
            ]);
        $this->ask($question, function(Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->role = $answer->getValue();
                switch ($answer->getValue()) {
                    case 'parent':
                        break;
                    case 'teacher':
                        break;
                    default:
                        $this->say("Hiện tại mình chỉ có thể hỗ trợ cho `phụ huynh` hoặc `huynh trưởng` của xứ đoàn thôi, {$this->getUserFirstName()} thông cảm nhé", Command::PARSE_MODE_MARKDOWN);
                        return;
                }
                \Log::info($this->role);
                \Log::info($this->phone);
                $this->say("Thông tin đã được ghi nhận" . Emoji::huggingFace(), Command::PARSE_MODE_MARKDOWN);
                $this->askPhone();
            }
        });
    }

    public function askPhone()
    {
        $this->ask("Số điện thoại {$this->getUserFirstName()} đang dùng là gì (số điện thoại mà {$this->getUserFirstName()} đã điền lúc đăng ký học giáo lý á)?", function (Answer $answer) {
            $this->phone = $answer->getText();
            $this->say(Emoji::thumbsUpSign() . "Cám ơn {$this->getUserFirstName()} đã cung cấp thông tin, bây giờ {$this->getUserFirstName()} có thể dùng lệnh /help để được hỗ trợ rồi" . Emoji::huggingFace(), Command::PARSE_MODE_MARKDOWN);
            \Log::info($this->role);
            \Log::info($this->phone);
        }, ['reply_markup' => json_encode([
            'keyboard' => [[['text' => 'Đồng ý cung cấp số điện thoại', 'request_contact' => true]]]
        ])]);
    }

    public function getUser()
    {
        return $this->getBot()->getUser();
    }

    public function getUserId()
    {
        return $this->getUser()->getId();
    }

    public function getUserFirstName()
    {
        return $this->getUser()->getFirstName();
    }

    public function skipsConversation(IncomingMessage $message)
    {
        if ($message->getText() == 'pause') {
            return true;
        }

        return false;
    }

    public function stopsConversation(IncomingMessage $message)
    {
        if ($message->getText() == 'stop') {
            return true;
        }

        return false;
    }
}
