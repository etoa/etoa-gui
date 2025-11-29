<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Alliance\AllianceWithMemberCount;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceBuildListRepository::class)]
#[ORM\Table(name: 'alliance_buildlist')]
class AllianceBuildListItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "alliance_buildlist_id", type: "integer")]
    private int $id;

    #[ORM\JoinColumn(name: 'alliance_buildlist_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class, inversedBy: 'buildlist')]
    private Alliance|null $alliance;

    #[ORM\JoinColumn(name: 'alliance_buildlist_building_id', referencedColumnName: 'alliance_building_id')]
    #[ORM\ManyToOne(targetEntity: AllianceBuilding::class)]
    private AllianceBuilding $allianceBuilding;

    #[ORM\Column(name: "alliance_buildlist_current_level", type: "integer")]
    private int $level = 0;

    #[ORM\Column(name: "alliance_buildlist_build_start_time", type: "integer")]
    private int $buildStartTime = 0;

    #[ORM\Column(name: "alliance_buildlist_build_end_time", type: "integer")]
    private int $buildEndTime = 0;

    #[ORM\Column(name: "alliance_buildlist_cooldown", type: "integer")]
    private int $cooldown = 0;

    #[ORM\Column(name: "alliance_buildlist_member_for", type: "integer")]
    private int $memberFor = 0;

    public function isUnderConstruction(): bool
    {
        return $this->buildEndTime > time();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getBuildStartTime(): ?int
    {
        return $this->buildStartTime;
    }

    public function setBuildStartTime(int $buildStartTime): static
    {
        $this->buildStartTime = $buildStartTime;

        return $this;
    }

    public function getBuildEndTime(): ?int
    {
        return $this->buildEndTime;
    }

    public function setBuildEndTime(int $buildEndTime): static
    {
        $this->buildEndTime = $buildEndTime;

        return $this;
    }

    public function getCooldown(): ?int
    {
        return $this->cooldown;
    }

    public function setCooldown(int $cooldown): static
    {
        $this->cooldown = $cooldown;

        return $this;
    }

    public function getMemberFor(): ?int
    {
        return $this->memberFor;
    }

    public function setMemberFor(int $memberFor): static
    {
        $this->memberFor = $memberFor;

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
