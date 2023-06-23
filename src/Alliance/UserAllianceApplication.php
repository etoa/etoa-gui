<?php declare(strict_types=1);

namespace EtoA\Alliance;

class UserAllianceApplication
{
    public int $allianceId;
    public int $timestamp;

    public function __construct(array $data)
    {
        $this->allianceId = (int) $data['alliance_id'];
        $this->timestamp = (int) $data['timestamp'];
    }
}
