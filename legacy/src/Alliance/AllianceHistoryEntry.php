<?php

declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceHistoryEntry
{
    public int $id;
    public int $allianceId;
    public int $timestamp;
    public string $text;

    public function __construct(array $data)
    {
        $this->id = (int) $data['history_id'];
        $this->allianceId = (int) $data['history_alliance_id'];
        $this->timestamp = (int) $data['history_timestamp'];
        $this->text = $data['history_text'];
    }
}
