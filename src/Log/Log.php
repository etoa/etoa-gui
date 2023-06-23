<?php declare(strict_types=1);

namespace EtoA\Log;

class Log
{
    public int $id;
    public int $timestamp;
    public int $facility;
    public int $severity;
    public string $message;
    public string $ip;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->timestamp = (int) $data['timestamp'];
        $this->facility = (int) $data['facility'];
        $this->severity = (int) $data['severity'];
        $this->message = $data['message'];
        $this->ip = $data['ip'];
    }
}
