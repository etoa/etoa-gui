<?php

declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceApplication
{
    public int $userId;
    public int $allianceId;
    public int $timestamp;
    public string $text;

    public function __construct(array $data)
    {
        $this->userId = (int) $data['user_id'];
        $this->allianceId = (int) $data['alliance_id'];
        $this->text = $data['text'];
        $this->timestamp = (int) $data['timestamp'];
    }
}
