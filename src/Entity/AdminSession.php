<?php declare(strict_types=1);

namespace EtoA\Entity;
use Doctrine\ORM\Mapping as ORM;
use EtoA\Admin\AdminSessionRepository;

#[ORM\Entity(repositoryClass: AdminSessionRepository::class)]
#[ORM\Table(name: 'admin_user_sessions')]
class AdminSession
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: AdminUser::class)]
    private ?AdminUser $user = null;

    #[ORM\Column]
    private ?string $ipAddr;

    #[ORM\Column]
    private ?string $userAgent;

    #[ORM\Column]
    private int $timeLogin;

    #[ORM\Column]
    private int $timeAction = 0;

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

    public function getUser(): ?AdminUser
    {
        return $this->user;
    }

    public function setUser(?AdminUser $user): static
    {
        $this->user = $user;

        return $this;
    }
}
