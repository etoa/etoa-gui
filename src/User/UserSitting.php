<?php declare(strict_types=1);

namespace EtoA\User;

class UserSitting
{
    public int $id;
    public int $userId;
    public ?string $userNick;
    public int $sitterId;
    public ?string $sitterNick;
    public string $password;
    public int $dateFrom;
    public int $dateTo;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->userNick = (string) $data['user_nick'];
        $this->sitterId = (int) $data['sitter_id'];
        $this->sitterNick = (string) $data['sitter_nick'];
        $this->password = $data['password'];
        $this->dateFrom = (int) $data['date_from'];
        $this->dateTo = (int) $data['date_to'];
    }
}
