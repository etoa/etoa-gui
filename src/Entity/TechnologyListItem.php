<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Technology\TechnologyDataRepository;

#[ORM\Entity(repositoryClass: TechnologyDataRepository::class)]
#[ORM\Table(name: 'techlist')]
class TechnologyListItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "techlist_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "techlist_user_id", type: "integer")]
    private int $userId;

    #[ORM\Column(name: "techlist_tech_id", type: "integer")]
    private int $technologyId;

    #[ORM\Column(name: "techlist_entity_id", type: "integer")]
    private int $entityId;

    #[ORM\Column(name: "techlist_current_level", type: "integer")]
    private int $currentLevel;

    #[ORM\Column(name: "techlist_build_type", type: "integer")]
    private int $buildType;

    #[ORM\Column(name: "techlist_build_start_time", type: "integer")]
    private int $startTime;

    #[ORM\Column(name: "techlist_build_end_time", type: "integer")]
    private int $endTime;

    #[ORM\Column(name: "techlist_prod_percent", type: "integer")]
    private int $prodPercent;

    public static function createFromData(array $data): TechnologyListItem
    {
        $item = new self();
        $item->id = (int) $data['techlist_id'];
        $item->userId = (int) $data['techlist_user_id'];
        $item->technologyId = (int) $data['techlist_tech_id'];
        $item->entityId = (int) $data['techlist_entity_id'];
        $item->currentLevel = (int) $data['techlist_current_level'];
        $item->buildType = (int) $data['techlist_build_type'];
        $item->startTime = (int) $data['techlist_build_start_time'];
        $item->endTime = (int) $data['techlist_build_end_time'];
        $item->prodPercent = (int) $data['techlist_prod_percent'];

        return $item;
    }

    public static function empty(): TechnologyListItem
    {
        return new TechnologyListItem();
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

    public function getTechnologyId(): ?int
    {
        return $this->technologyId;
    }

    public function setTechnologyId(int $technologyId): static
    {
        $this->technologyId = $technologyId;

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

    public function getBuildType(): ?int
    {
        return $this->buildType;
    }

    public function setBuildType(int $buildType): static
    {
        $this->buildType = $buildType;

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

    public function getProdPercent(): ?int
    {
        return $this->prodPercent;
    }

    public function setProdPercent(int $prodPercent): static
    {
        $this->prodPercent = $prodPercent;

        return $this;
    }
}
