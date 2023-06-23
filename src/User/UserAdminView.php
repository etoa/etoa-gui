<?php declare(strict_types=1);

namespace EtoA\User;

class UserAdminView extends User
{
    public ?string $ipAddr;
    public ?string $userAgent;
    public ?int $timeAction;
    public ?int $timeLog;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->ipAddr = $data['ip_addr'] ?? $data['ip_log'];
        $this->userAgent = $data['user_agent'] ?? $data['agent_log'];
        $this->timeAction = $data['time_action'] ? (int) $data['time_action'] : null;
        $this->timeLog = $data['time_log'] ? (int) $data['time_log'] : null;
    }
}
