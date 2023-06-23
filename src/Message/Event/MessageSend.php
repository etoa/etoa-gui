<?php declare(strict_types=1);

namespace EtoA\Message\Event;

use Symfony\Contracts\EventDispatcher\Event;

class MessageSend extends Event
{
    const SEND_SUCCESS = 'message.send.success';
}
