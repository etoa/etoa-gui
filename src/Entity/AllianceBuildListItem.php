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
    public int $id;

    #[ORM\Column(name: "alliance_buildlist_alliance_id", type: "integer")]
    private int $allianceId;

    #[ORM\Column(name: "alliance_buildlist_building_id", type: "integer")]
    private int $buildingId;

    #[ORM\JoinColumn(name: 'alliance_buildlist_building_id', referencedColumnName: 'alliance_building_id')]
    #[ORM\ManyToOne(targetEntity: AllianceBuilding::class)]
    private AllianceBuilding $allianceBuilding;

    #[ORM\Column(name: "alliance_buildlist_current_level", type: "integer")]
    private int $level;

    #[ORM\Column(name: "alliance_buildlist_build_start_time", type: "integer")]
    private int $buildStartTime;

    #[ORM\Column(name: "alliance_buildlist_build_end_time", type: "integer")]
    private int $buildEndTime;

    #[ORM\Column(name: "alliance_buildlist_cooldown", type: "integer")]
    private int $cooldown;

    #[ORM\Column(name: "alliance_buildlist_member_for", type: "integer")]
    private int $memberFor;

    public static function createFromAlliance(AllianceWithMemberCount $alliance): AllianceBuildListItem
    {
        $item = new AllianceBuildListItem();
        $item->id = 0;
        $item->allianceId = $alliance->getId();
        $item->buildingId = 0;
        $item->level = 0;
        $item->buildStartTime = 0;
        $item->buildEndTime = 0;
        $item->cooldown = 0;
        $item->memberFor = $alliance->memberCount;

        return $item;
    }

    public static function createFromData(array $data): AllianceBuildListItem
    {
        $item = new AllianceBuildListItem();
        $item->id = (int) $data['alliance_buildlist_id'];
        $item->allianceId = (int) $data['alliance_buildlist_alliance_id'];
        $item->buildingId = (int) $data['alliance_buildlist_building_id'];
        $item->level = (int) $data['alliance_buildlist_current_level'];
        $item->buildStartTime = (int) $data['alliance_buildlist_build_start_time'];
        $item->buildEndTime = (int) $data['alliance_buildlist_build_end_time'];
        $item->cooldown = (int) $data['alliance_buildlist_cooldown'];
        $item->memberFor = (int) $data['alliance_buildlist_member_for'];

        return $item;
    }

    public function isUnderConstruction(): bool
    {
        return $this->buildEndTime > time();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAllianceId(): ?int
    {
        return $this->allianceId;
    }

    public function setAllianceId(int $allianceId): static
    {
        $this->allianceId = $allianceId;

        return $this;
    }

    public function getBuildingId(): ?int
    {
        return $this->buildingId;
    }

    public function setBuildingId(int $buildingId): static
    {
        $this->buildingId = $buildingId;

        return $this;
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
}
