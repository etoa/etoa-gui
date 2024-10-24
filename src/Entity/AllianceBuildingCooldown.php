<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Alliance\AllianceBuildingCooldownRepository;

#[ORM\Entity(repositoryClass: AllianceBuildingCooldownRepository::class)]
class AllianceBuildingCooldown
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $userId = null;

    #[ORM\Id]
    #[ORM\Column]
    private ?int $allianceBuildingId = null;

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

    public function getAllianceBuildingId(): ?int
    {
        return $this->allianceBuildingId;
    }

    public function setAllianceBuildingId(int $allianceBuildingId): static
    {
        $this->allianceBuildingId = $allianceBuildingId;

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
}
