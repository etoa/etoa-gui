<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Defense\DefenseQueueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DefenseQueueRepository::class)]
#[ORM\Table(name: 'def_queue')]
class DefenseQueueItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "queue_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "queue_user_id", type: "integer")]
    private int $userId;

    #[ORM\Column(name: "queue_def_id", type: "integer")]
    private int $defenseId;

    #[ORM\Column(name: "queue_entity_id", type: "integer")]
    private int $entityId;

    #[ORM\Column(name: "queue_cnt", type: "integer")]
    private int $count;

    #[ORM\Column(name: "queue_starttime", type: "integer")]
    private int $startTime;

    #[ORM\Column(name: "queue_endtime", type: "integer")]
    private int $endTime;

    #[ORM\Column(name: "queue_objtime", type: "integer")]
    private int $objectTime;

    #[ORM\Column(name: "queue_build_type", type: "integer")]
    private int $buildType;

    #[ORM\Column(name: "queue_user_click_time", type: "integer")]
    private int $userClickTime;

    public static function createFromData(array $data): DefenseQueueItem
    {
        $item = new DefenseQueueItem();
        $item->id = (int) $data['queue_id'];
        $item->userId = (int) $data['queue_user_id'];
        $item->defenseId = (int) $data['queue_def_id'];
        $item->entityId = (int) $data['queue_entity_id'];
        $item->count = (int) $data['queue_cnt'];
        $item->startTime = (int) $data['queue_starttime'];
        $item->endTime = (int) $data['queue_endtime'];
        $item->objectTime = (int) $data['queue_objtime'];
        $item->buildType = (int) $data['queue_build_type'];
        $item->userClickTime = (int) $data['queue_user_click_time'];

        return $item;
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

    public function getDefenseId(): ?int
    {
        return $this->defenseId;
    }

    public function setDefenseId(int $defenseId): static
    {
        $this->defenseId = $defenseId;

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

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

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

    public function getObjectTime(): ?int
    {
        return $this->objectTime;
    }

    public function setObjectTime(int $objectTime): static
    {
        $this->objectTime = $objectTime;

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

    public function getUserClickTime(): ?int
    {
        return $this->userClickTime;
    }

    public function setUserClickTime(int $userClickTime): static
    {
        $this->userClickTime = $userClickTime;

        return $this;
    }
}
