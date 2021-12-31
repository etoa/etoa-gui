<?php declare(strict_types=1);

namespace EtoA\Message;

use Symfony\Component\HttpFoundation\Request;

class AdminMessageRequest
{
    public const MESSAGE_TYPE_IN_GAME = 0;
    public const MESSAGE_TYPE_EMAIL = 1;
    public const MESSAGE_TYPE_BOTH = 2;

    public int $fromId;
    public ?int $userId = null;
    public string $subject;
    public string $text;
    public int $type;

    public function sendAsEmail(): bool
    {
        return in_array($this->type, [self::MESSAGE_TYPE_EMAIL, self::MESSAGE_TYPE_BOTH], true);
    }

    public function sendAsInGameMessage(): bool
    {
        return in_array($this->type, [self::MESSAGE_TYPE_IN_GAME, self::MESSAGE_TYPE_BOTH], true);
    }

    public static function fromRequest(Request $request): AdminMessageRequest
    {
        $message = new AdminMessageRequest();
        if ($request->query->has('userId')) {
            $message->userId = $request->query->getInt('userId');
        }

        if ($request->query->has('type')) {
            $message->type = $request->query->getInt('type');
        }

        if ($request->query->has('subject')) {
            $message->subject = $request->query->get('subject');
        }

        if ($request->query->has('text')) {
            $message->text = $request->query->get('text');
        }

        return $message;
    }
}
