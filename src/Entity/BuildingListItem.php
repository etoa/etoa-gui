<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Building\BuildingListItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildingListItemRepository::class)]
#[ORM\Table(name: 'buildlist')]
class BuildingListItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "buildlist_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "buildlist_user_id", type: "integer")]
    private int $userId;

    #[ORM\Column(name: "buildlist_building_id", type: "integer")]
    private int $buildingId;

    #[ORM\ManyToOne(targetEntity: Entity::class)]
    #[ORM\JoinColumn(name: 'buildlist_entity_id', referencedColumnName: 'id')]
    private Entity $entity;

    #[ORM\Column(name: "buildlist_entity_id", type: "integer")]
    private int $entityId;

    #[ORM\Column(name: "buildlist_current_level", type: "integer")]
    private int $currentLevel;

    #[ORM\Column(name: "buildlist_build_start_time", type: "integer")]
    private int $startTime;

    #[ORM\Column(name: "buildlist_build_end_time", type: "integer")]
    private int $endTime;

    #[ORM\Column(name: "buildlist_build_type", type: "integer")]
    private int $buildType;

    #[ORM\Column(name: "buildlist_prod_percent", type: "integer")]
    private int $prodPercent;

    #[ORM\Column(name: "buildlist_people_working", type: "integer")]
    private int $peopleWorking;

    #[ORM\Column(name: "buildlist_people_working_status", type: "integer")]
    private int $peopleWorkingStatus;

    #[ORM\Column(name: "buildlist_deactivated", type: "integer")]
    private int $deactivated;

    #[ORM\Column(name: "buildlist_cooldown", type: "integer")]
    private int $cooldown;

    public static function createFromData(array $data): BuildingListItem
    {
        $item = new BuildingListItem();
        $item->id = (int) $data['buildlist_id'];
        $item->userId = (int) $data['buildlist_user_id'];
        $item->buildingId = (int) $data['buildlist_building_id'];
        $item->entityId = (int) $data['buildlist_entity_id'];
        $item->currentLevel = (int) $data['buildlist_current_level'];
        $item->startTime = (int) $data['buildlist_build_start_time'];
        $item->endTime = (int) $data['buildlist_build_end_time'];
        $item->buildType = (int) $data['buildlist_build_type'];
        $item->prodPercent = (int) $data['buildlist_prod_percent'];
        $item->peopleWorking = (int) $data['buildlist_people_working'];
        $item->peopleWorkingStatus = (int) $data['buildlist_people_working_status'];
        $item->deactivated = (int) $data['buildlist_deactivated'];
        $item->cooldown = (int) $data['buildlist_cooldown'];

        return $item;
    }

    public static function empty(): BuildingListItem
    {
        $item = new BuildingListItem();
        $item->id = 0;
        $item->userId = 0;
        $item->buildingId = 0;
        $item->entityId = 0;
        $item->currentLevel = 0;
        $item->startTime = 0;
        $item->endTime = 0;
        $item->buildType = 0;
        $item->prodPercent = 0;
        $item->peopleWorking = 0;
        $item->peopleWorkingStatus = 0;
        $item->deactivated = 0;
        $item->cooldown = 0;

        return $item;
    }

    public function isDeactivated(): bool
    {
        return $this->deactivated > time();
    }

    public function isUnderConstruction(): bool
    {
        return in_array($this->buildType, [3, 4], true) && $this->endTime > time();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBuildingId(): ?int
    {
        return $this->buildingId;
    }

    public function setBuildingId(int $buildingId): static
    {
        $this->buildingId = $buildingId;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): static
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getCurrentLevel(): ?int
    {
        return $this->currentLevel;
    }

    public function setCurrentLevel(int $currentLevel): static
    {
        $this->currentLevel = $currentLevel;

        return $this;
    }

    public function getStartTime(): ?int
    {
        return $this->startTime;
    }

    public function setStartTime(int $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?int
    {
        return $this->endTime;
    }

    public function setEndTime(int $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getBuildType(): ?int
    {
        return $this->buildType;
    }

    public function setBuildType(int $buildType): static
    {
        $this->buildType = $buildType;

        return $this;
    }

    public function getProdPercent(): ?int
    {
        return $this->prodPercent;
    }

    public function setProdPercent(int $prodPercent): static
    {
        $this->prodPercent = $prodPercent;

        return $this;
    }

    public function getPeopleWorking(): ?int
    {
        return $this->peopleWorking;
    }

    public function setPeopleWorking(int $peopleWorking): static
    {
        $this->peopleWorking = $peopleWorking;

        return $this;
    }

    public function getPeopleWorkingStatus(): ?int
    {
        return $this->peopleWorkingStatus;
    }

    public function setPeopleWorkingStatus(int $peopleWorkingStatus): static
    {
        $this->peopleWorkingStatus = $peopleWorkingStatus;

        return $this;
    }

    public function getDeactivated(): ?int
    {
        return $this->deactivated;
    }

    public function setDeactivated(int $deactivated): static
    {
        $this->deactivated = $deactivated;

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
}
