<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Alliance\AllianceBuildingCooldownRepository;

#[ORM\Entity(repositoryClass: AllianceBuildingCooldownRepository::class)]
class AllianceBuildingCooldown
{
    #[ORM\Id]
    #[ORM\Column(name: 'cooldown_user_id')]
    private ?int $userId = null;

    #[ORM\JoinColumn(name: 'cooldown_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'cooldown_alliance_building_id', referencedColumnName: 'alliance_building_id')]
    #[ORM\ManyToOne(targetEntity: AllianceBuilding::class)]
    private ?AllianceBuilding $allianceBuilding = null;

    #[ORM\Column]
    private ?int $cooldownEnd = null;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getCooldownEnd(): ?int
    {
        return $this->cooldownEnd;
    }

    public function setCooldownEnd(int $cooldownEnd): static
    {
        $this->cooldownEnd = $cooldownEnd;

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

    public function getAllianceBuilding(): ?AllianceBuilding
    {
        return $this->allianceBuilding;
    }

    public function setAllianceBuilding(?AllianceBuilding $allianceBuilding): static
    {
        $this->allianceBuilding = $allianceBuilding;

        return $this;
    }
}
