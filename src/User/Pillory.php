<?php declare(strict_types=1);

namespace EtoA\User;

class Pillory
{
    public string $userNick;
    public int $blockedFrom;
    public int $blockedTo;
    public string $banReason;
    public string $adminNick;
    public string $adminEmail;

    public function __construct(array $data)
    {
        $this->userNick = $data['user_nick'];
        $this->blockedFrom = (int) $data['user_blocked_from'];
        $this->blockedTo = (int) $data['user_blocked_to'];
        $this->banReason = $data['user_ban_reason'];
        $this->adminNick = $data['admin_nick'];
        $this->adminEmail = $data['admin_email'];
    }
}
