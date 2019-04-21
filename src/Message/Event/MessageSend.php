<?php declare(strict_types=1);

namespace EtoA\Message\Event;

use Symfony\Component\EventDispatcher\Event;

class MessageSend extends Event
{
    const SEND_SUCCESS = 'message.send.success';
}
