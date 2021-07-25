<?php declare(strict_types=1);

namespace EtoA\User;

class UserMulti
{
    public int $id;
    public int $userId;
    public int $multiUserId;
    public ?string $multiUserNick;
    public string $reason;
    public bool $active;
    public int $timestamp;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->multiUserId = (int) $data['multi_id'];
        $this->multiUserNick = $data['multi_nick'];
        $this->reason = $data['connection'];
        $this->active = (bool) $data['activ'];
        $this->timestamp = (int) $data['timestamp'];
    }
}
