<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\AllianceApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceApplicationRepository::class)]
#[ORM\Table(name: 'alliance_applications')]
class AllianceApplication
{
    #[ORM\Id]
    #[ORM\Column]
    private int $userId;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private User|null $user;

    #[ORM\Id]
    #[ORM\Column]
    private int $allianceId;

    #[ORM\JoinColumn(name: 'alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private Alliance|null $alliance;

    #[ORM\Column]
    private int $timestamp;

    #[ORM\Column]
    private string $text;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getAllianceId(): ?int
    {
        return $this->allianceId;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

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

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): static
    {
        $this->alliance = $alliance;

        return $this;
    }
}
