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

    #[ORM\Column(type: "integer")]
    private int $userId;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $sitter;

    #[ORM\Column(type: "integer")]
    private int $sitterId;

    #[ORM\Column]
    private string $password;

    #[ORM\Column(type: "integer")]
    private int $dateFrom;

    #[ORM\Column(type: "integer")]
    private int $dateTo;

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

    public function getSitterNick(): ?string
    {
        return $this->sitterNick;
    }

    public function setSitterNick(string $sitterNick): static
    {
        $this->sitterNick = $sitterNick;

        return $this;
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
}
