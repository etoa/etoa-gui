<?php declare(strict_types=1);

namespace EtoA\User;

class UserRating
{
    public int $userId;
    public string $userNick;
    public string $raceName;
    public ?string $allianceTag;
    public int $rating;

    public function __construct(array $data)
    {
        $this->userId = (int) $data['user_id'];
        $this->userNick = $data['user_nick'];
        $this->raceName = $data['race_name'];
        $this->allianceTag = $data['alliance_tag'];
    }
}
