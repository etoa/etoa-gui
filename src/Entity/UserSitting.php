<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\User\UserSittingRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSittingRepository::class)]
class UserSitting implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy:'userSittings')]
    private User $user;
    
    #[ORM\JoinColumn(name: 'sitter_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $sitter;

    #[ORM\Column]
    private string $password;

    #[ORM\Column(type: "integer")]
    private int $dateFrom = 0;

    #[ORM\Column(type: "integer")]
    private int $dateTo = 0;

    public function getPassword(): string
    {
        return $this->password;
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

    public function getSitterId(): ?int
    {
        return $this->sitterId;
    }

    public function setSitterId(int $sitterId): static
    {
        $this->sitterId = $sitterId;

        return $this;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getDateFrom(): ?int
    {
        return $this->dateFrom;
    }

    public function setDateFrom(int $dateFrom): static
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?int
    {
        return $this->dateTo;
    }

    public function setDateTo(int $dateTo): static
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSitter(): ?User
    {
        return $this->sitter;
    }

    public function setSitter(?User $sitter): static
    {
        $this->sitter = $sitter;

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
