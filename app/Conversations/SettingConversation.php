<?php

namespace App\Conversations;

use App\Helpers\CouchDbHelper;
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
                $this->say("Thông tin đã được ghi nhận" . Emoji::huggingFace(), Command::PARSE_MODE_MARKDOWN);
                $this->askPhone();
            }
        });
    }

    /**
     *
     */
    public function askPhone()
    {
        $this->ask("Số điện thoại {$this->getUserFirstName()} đang dùng là gì (số điện thoại mà {$this->getUserFirstName()} đã điền lúc đăng ký học giáo lý á)?", function (Answer $answer) {
            //$this->phone = $answer->getText();
            $payload = $answer->getMessage()->getPayload();
            \Log::error($payload);
            if (!empty($payload)) {
                $this->phone = $this->extractPhoneNumber($payload);
            }
            $permissionGrant = $this->checkPermission($this->role, $this->phone);
            $removeKeyboard = [
                'reply_markup' => json_encode([
                    'remove_keyboard' => true
                ])
            ];
            if ($permissionGrant) {
                $this->say(Emoji::thumbsUpSign() . "Cám ơn {$this->getUserFirstName()} đã cung cấp thông tin, bây giờ {$this->getUserFirstName()} có thể dùng lệnh /help để được hỗ trợ rồi " . Emoji::huggingFace(), array_merge(Command::PARSE_MODE_MARKDOWN, $removeKeyboard));
                return;
            }
            $this->say(Emoji::disappointedButRelievedFace() . " Không tìm thầy số điện thoại `{$this->phone}` trong thông tin đăng ký " . Emoji::disappointedButRelievedFace() . "  {$this->getUserFirstName()} có thể email đến `xudoanchuahienlinh@gmail.com` với mã yêu cầu `{$this->getUserId()}` để được hỗ trợ nhé " . Emoji::huggingFace(), array_merge(Command::PARSE_MODE_MARKDOWN, $removeKeyboard));
        }, ['reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => 'Đồng ý cung cấp số điện thoại', 'request_contact' => true]
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
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

    private function checkPermission($role, $phone)
    {   \Log::info("$role - $phone");
        if (!in_array($role, ['parent', 'teacher'])) return false;

        $payload = CouchDbHelper::queryView('bot', $role == 'parent' ? 'parents' : 'teachers', $phone);
        \Log::info($payload);
        
        $rows = $payload['rows'] ?? [];
        $studentIds = [];
        foreach ($rows as $row) {
            $studentIds[] = $row['id'];
            $parent = $row['value'];
            $telegram = $parent['telegram'] ?? null;
            // Need to update auth
        }
        return count($rows) > 0;
    }

    /**
     * Message with phone
        {
            "message_id": 254,
            "from": {
                "id": 286025420,
                "is_bot": false,
                "first_name": "Giang",
                "last_name": "Dinh",
                "username": "giangdinhtt",
                "language_code": "en-US"
            },
            "chat": {
                "id": 286025420,
                "first_name": "Giang",
                "last_name": "Dinh",
                "username": "giangdinhtt",
                "type": "private"
            },
            "date": 1535446027,
            "contact": {
                "phone_number": "84932093019",
                "first_name": "Giang",
                "last_name": "Dinh",
                "user_id": 286025420
            }
        }
     * or
        {
            "message_id": 291,
            "from": {
                "id": 286025420,
                "is_bot": false,
                "first_name": "Giang",
                "last_name": "Dinh",
                "username": "giangdinhtt",
                "language_code": "en-US"
            },
            "chat": {
                "id": 286025420,
                "first_name": "Giang",
                "last_name": "Dinh",
                "username": "giangdinhtt",
                "type": "private"
            },
            "date": 1535451545,
            "text": "84932093019",
            "entities": [
                {
                "offset": 0,
                "length": 11,
                "type": "phone_number"
                }
            ]
        }
     */
    private function extractPhoneNumber($payload)
    {
        $phone = null;
        // case 1
        $contact = $payload['contact'] ?? null;
        if ($contact != null) {
            $phone = $contact['phone_number'] ?? null;
        } else {
            // case 2
            $entities = $payload['entities'] ?? [];
            foreach ($entities as $entity) {
                if ($entity['type'] === 'phone_number') {
                    $phone = substr($payload['text'], (int) $entity['offset'], (int) $entity['length']);;
                }
            }
        }

        \Log::error('Phone: ' . $phone);
        return $phone;
    }
}
