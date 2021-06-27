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
    public bool $read;
    public bool $deleted;
    public bool $massMail;

    public static function createFromArray(array $data): Message
    {
        $message = new Message();

        $message->id = (int) $data['id'];
        $message->catId = (int) $data['message_cat_id'];
        $message->userFrom = (int) $data['message_user_from'];
        $message->userTo = (int) $data['message_user_to'];
        $message->timestamp = (int) $data['message_timestamp'];
        $message->subject = $data['subject'];
        $message->text = $data['text'];
        $message->read = $data['message_read'] == 1;
        $message->deleted = $data['message_deleted'] == 1;
        $message->massMail = $data['message_massmail'] == 1;

        return $message;
    }
}
