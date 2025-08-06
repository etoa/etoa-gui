<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Admin\AdminSessionLogRepository;

#[ORM\Entity(repositoryClass: AdminSessionLogRepository::class)]
#[ORM\Table(name: 'admin_user_sessionlog')]
class AdminSessionLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private string $sessionId;

    #[ORM\Column]
    private int $userId;

    #[ORM\Column]
    private ?string $ipAddr;

    #[ORM\Column]
    private ?string $userAgent;

    #[ORM\Column]
    private int $timeLogin;

    #[ORM\Column]
    private int $timeAction;

    #[ORM\Column]
    private int $timeLogout;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): static
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getIpAddr(): ?string
    {
        return $this->ipAddr;
    }

    public function setIpAddr(string $ipAddr): static
    {
        $this->ipAddr = $ipAddr;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): static
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getTimeLogin(): ?int
    {
        return $this->timeLogin;
    }

    public function setTimeLogin(int $timeLogin): static
    {
        $this->timeLogin = $timeLogin;

        return $this;
    }

    public function getTimeAction(): ?int
    {
        return $this->timeAction;
    }

    public function setTimeAction(int $timeAction): static
    {
        $this->timeAction = $timeAction;

        return $this;
    }

    public function getTimeLogout(): ?int
    {
        return $this->timeLogout;
    }

    public function setTimeLogout(int $timeLogout): static
    {
        $this->timeLogout = $timeLogout;

        return $this;
    }


}
