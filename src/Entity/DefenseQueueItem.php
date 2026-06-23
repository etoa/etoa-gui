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

    #[ORM\JoinColumn(name: 'queue_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'queue_entity_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Planet::class)]
    private ?Planet $entity = null;

    #[ORM\JoinColumn(name: 'queue_def_id', referencedColumnName: 'def_id')]
    #[ORM\ManyToOne(targetEntity: Defense::class)]
    private ?Defense $defense = null;

    #[ORM\Column(name: "queue_cnt", type: "integer")]
    private int $count = 0;

    #[ORM\Column(name: "queue_starttime", type: "integer")]
    private int $startTime = 0;

    #[ORM\Column(name: "queue_endtime", type: "integer")]
    private int $endTime = 0;

    #[ORM\Column(name: "queue_objtime", type: "integer")]
    private int $objectTime = 0;

    #[ORM\Column(name: "queue_build_type", type: "integer")]
    private int $buildType = 0;

    #[ORM\Column(name: "queue_user_click_time", type: "integer")]
    private int $userClickTime = 0;

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

    public function getUserClickTime(): ?int
    {
        return $this->userClickTime;
    }

    public function setUserClickTime(int $userClickTime): static
    {
        $this->userClickTime = $userClickTime;

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

    public function getEntity(): ?Planet
    {
        return $this->entity;
    }

    public function setEntity(?Planet $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    public function getDefense(): ?Defense
    {
        return $this->defense;
    }

    public function setDefense(?Defense $defense): static
    {
        $this->defense = $defense;

        return $this;
    }
}
