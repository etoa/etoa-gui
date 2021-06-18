<?php

declare(strict_types=1);

namespace EtoA\Message;

class Message
{
    public int $id;
    public int $catId;
    public int $userFrom;
    public int $userTo;
    public int $timestamp;
    public string $subject;
    public string $text;
}