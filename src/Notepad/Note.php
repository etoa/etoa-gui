<?php declare(strict_types=1);

namespace EtoA\Notepad;

class Note
{
    public int $id;
    public int $timestamp;
    public string $subject;
    public string $text;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->timestamp = (int) $data['timestamp'];
        $this->subject = $data['subject'];
        $this->text = $data['text'];
    }
}
