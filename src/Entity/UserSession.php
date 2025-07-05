<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\User\UserSessionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSessionRepository::class)]
#[ORM\Table(name: 'user_sessions')]
class UserSession
{
    #[ORM\Id]
    #[ORM\Column(type: "string")]
    private string $id;

    #[ORM\OneToOne(mappedBy: "session", targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    private User $user;

    #[ORM\Column]
    private ?string $ipAddr;

    #[ORM\Column]
    private ?string $userAgent;

    #[ORM\Column]
    private int $timeLogin;

    #[ORM\Column]
    private int $timeAction= 0;

    #[ORM\Column]
    private int $lastSpan = 0;

    #[ORM\Column]
    private int $botCount = 0;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

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

    public function getLastSpan(): ?int
    {
        return $this->lastSpan;
    }

    public function setLastSpan(int $lastSpan): static
    {
        $this->lastSpan = $lastSpan;

        return $this;
    }

    public function getBotCount(): ?int
    {
        return $this->botCount;
    }

    public function setBotCount(int $botCount): static
    {
        $this->botCount = $botCount;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
