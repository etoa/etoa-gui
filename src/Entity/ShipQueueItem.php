<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Ship\ShipQueueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipQueueRepository::class)]
#[ORM\Table(name: 'ship_queue')]
class ShipQueueItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "queue_id", type: "integer")]
    private int $id;

    #[ORM\JoinColumn(name: 'queue_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'queue_ship_id', referencedColumnName: 'ship_id')]
    #[ORM\ManyToOne(targetEntity: Ship::class)]
    private ?Ship $ship = null;

    #[ORM\ManyToOne(targetEntity: Planet::class)]
    #[ORM\JoinColumn(name: 'queue_entity_id', referencedColumnName: 'id')]
    private ?Planet $entity = null;

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

    public static function createFromData(array $data): ShipQueueItem
    {
        $item = new ShipQueueItem();
        $item->id = (int) $data['queue_id'];
        $item->userId = (int) $data['queue_user_id'];
        $item->shipId = (int) $data['queue_ship_id'];
        $item->entityId = (int) $data['queue_entity_id'];
        $item->count = (int) $data['queue_cnt'];
        $item->startTime = (int) $data['queue_starttime'];
        $item->endTime = (int) $data['queue_endtime'];
        $item->objectTime = (int) $data['queue_objtime'];
        $item->buildType = (int) $data['queue_build_type'];

        return $item;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getShip(): ?Ship
    {
        return $this->ship;
    }

    public function setShip(?Ship $ship): static
    {
        $this->ship = $ship;

        return $this;
    }

    public function getEntity(): ?Planet
    {
        return $this->entity;
    }

    public function setEntity(?Planet $entity): static
    {
        $this->entity = $entity;

        return $this;
    }
}
